<?php

namespace App\Controller;

use App\Entity\Favorites;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Repository\FavoritesRepository;

class FavoritesController extends AbstractController
{
    private $entityManager;
    private $security;
    private $favoritesRepo;
    public function __construct(EntityManagerInterface $entityManager, Security $security, FavoritesRepository $favoritesRepo)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->favoritesRepo = $favoritesRepo;
    }

    /**
     * @Route("/mes-favoris", name="favorites")
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $products = $this->entityManager->getRepository(Favorites::class)->findAll();
        $products = $paginator->paginate(
            $products,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('favorites/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/mes-favoris/ajouter/{id}", name="add_to_favorites")
     */
    public function add($id): Response
    {
        $product = $this->entityManager->getRepository(Product::class)->findOneById($id);

        $favorite = new Favorites();
        $favorite->setName($product[0]->getName());
        $favorite->setDescription($product[0]->getDescription());
        $favorite->setPrice($product[0]->getPrice());
        $favorite->setCreatedAt($product[0]->getCreatedAt());
        $favorite->setIllustration($product[0]->getIllustration());
        $favorite->setUser($this->security->getUser());
        $favorite->setId($id);

        $this->entityManager->persist($favorite);
        $this->entityManager->flush();

        return $this->redirectToRoute('offers');
    }

    /**
     * @Route("/mes-favoris/supprimer/{id}", name="remove_favorites")
     */
    public function remove($id): Response
    {
        $fav_product = $this->entityManager->getRepository(Favorites::class)->findOneById($id);
        $this->entityManager->remove($fav_product[0]);
        $this->entityManager->flush();

        return $this->redirectToRoute('favorites');
    }
}