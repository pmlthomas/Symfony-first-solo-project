<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\OfferType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AccountController extends AbstractController
{
    private $security;
    private $entityManager;
    public function __construct(Security $security, EntityManagerInterface $entityManager)
    {
        $this->security = $security;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/mon-compte", name="account")
     */
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * @Route("/mon-compte/mes-offres", name="account_offers")
     */
    public function my_offers(PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->security->getUser();
        $products = $this->entityManager->getRepository(Product::class)->findByUserId($user->getId());

        $products = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            2
        );
        return $this->render('account/my_offers.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/mon-compte/mes-offres/modifier/{id}", name="account_edit_offers")
     */
    public function edit($id, Request $request): Response
    {
        $my_offer = new Product();
        $my_offer = $this->entityManager->getRepository(Product::class)->findById($id);

        if ($_POST) {
            $upload_directory = "assets/uploads/";
            $illustration = ($upload_directory . basename($_FILES['image']['name']));
            move_uploaded_file($_FILES['image']['tmp_name'], $illustration);

            $my_offer[0]->setName($_POST['name']);
            $my_offer[0]->setDescription($_POST['description']);
            $my_offer[0]->setPrice($_POST['price']);
            $my_offer[0]->setIllustration($illustration);

            $this->entityManager->persist($my_offer[0]);
            $this->entityManager->flush();

            return $this->redirectToRoute('account_offers');
        }
        return $this->render('account/edit_offer.html.twig', [
            'product' => $my_offer[0]
        ]);
    }

    
    /**
     * @Route("/mon-compte/mes-offres/supprimer/{id}", name="account_delete_offers")
     */
    public function delete($id): Response
    {
        $my_offer = new Product();
        $my_offer = $this->entityManager->getRepository(Product::class)->findById($id);

        $my_offer[0]->setIsDeleted(TRUE);

        $this->entityManager->persist($my_offer[0]);
        $this->entityManager->flush();

        return $this->redirectToRoute('account_offers');
    }
}