<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\OfferType;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class OffersController extends AbstractController
{
    private $requestStack;
    private $entityManager;
    private $security;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager, Security $security)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager; 
        $this->security = $security;
    }

    /**
     * @route("/offres", name="offers")
     */
    public function index(Request $request, PaginatorInterface $paginator)
    {
        $session = $this->requestStack->getSession();

        $products = $this->entityManager->getRepository(Product::class)->findBySearch($session->get('search'));
        $products = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            2
        );
            if (!isset($products[0])) {
                $productFound = FALSE;
            } else {
                $productFound = TRUE;
            }
        
        return $this->render('offers/show.html.twig', [
            'products' => $products,
            'productFound' => $productFound
        ]);
    }

    /**
     * @route("/ajouter-une-offre", name="add_offer")
     */
    public function add(Request $request, ProductRepository $productRepository, Security $security, EntityManagerInterface $entityManager)
    {
        $offer = new Product();
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTimeImmutable();
            $offer = new Product();

            $upload_directory = "assets/uploads/";
            $illustration = ($upload_directory . basename($_FILES['offer']['name']['illustration']));
            move_uploaded_file($_FILES['offer']['tmp_name']['illustration'], $illustration);

            $offer->setCreatedAt($date);
            $offer->setIllustration($illustration);
            $offer->setAuthor($security->getUser());
            $offer->setName($form->getData()->getName());
            $offer->setDescription($form->getData()->getDescription());
            $offer->setPrice($form->getData()->getPrice());
            $offer->setRegion($form->getData()->getRegion());
            $offer->setAddress($form->getData()->getAddress());
            $offer->setSubCategory($form->getData()->getSubCategory());
            $offer->setIsDeleted(FALSE);

            $this->entityManager->persist($offer);
            $this->entityManager->flush();
        }

        return $this->render('offers/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
