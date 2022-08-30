<?php

namespace App\Controller;

use App\Entity\Search;
use App\Form\SearchType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class HomepageController extends AbstractController
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
     * @Route("/", name="homepage")
     */
    public function index(Request $request): Response
    {
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $raw_search = $form->getData();

            $search->setCreatedAt($date = new DateTimeImmutable());
            $search->setSubCategory($raw_search->getSubCategory());
            $search->setUser($this->security->getUser());

            if (!empty($raw_search->getLocation())) {
                $search->setLocation($raw_search->getLocation());
            } else {
                $search->setLocation("Toute la france");
            }

            $this->entityManager->persist($search);
            $this->entityManager->flush();

            $session = $this->requestStack->getSession();
            $session->set('search', $search);

            return $this->redirectToRoute('offers');

        } else {
            if ($this->security->getUser()) {
                $latestSearches = $this->entityManager->getRepository(Search::class)->findThreeLast($this->security->getUser());

                return $this->render('homepage/index.html.twig', [
                    'form' => $form->createView(),
                    'latestSearches' => $latestSearches
                ]);
            }

            return $this->render('homepage/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }
    }
}

