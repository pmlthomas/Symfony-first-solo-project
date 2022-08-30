<?php

namespace App\Repository;

use App\Classe\Search;
use App\Entity\Product;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    private $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Product::class);
        $this->getEntityManager = $entityManager;
    }

    public function addOffer($offer, $form, $request, $security)
    {
        $date = new DateTimeImmutable();
        $offer = new Product();

        $offer->setCreatedAt($date);
        $offer->setAuthor($security->getUser());
        $offer->setName($form->getData()->getName());
        $offer->setDescription($form->getData()->getDescription());
        $offer->setPrice($form->getData()->getPrice());
        $offer->setIllustration($form->getData()->getIllustration());
        $offer->setRegion($form->getData()->getRegion());
        $offer->setAddress($form->getData()->getAddress());
        $offer->setSubCategory($form->getData()->getSubCategory());

        $this->entityManager->persist($offer);
        $this->entityManager->flush();
    }

    public function remove(Product $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Product[]
     */
    public function findBySearch($search)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p', 's')
            ->join('p.sub_category', 's')
            ->join('p.region', 'r')
            ->andWhere('p.is_deleted LIKE :false')
            ->setParameter('false', FALSE);

        if (!empty($search->getSubCategory())) {
            $query = $query
                ->andWhere('s.name LIKE :sub_category')
                ->setParameter('sub_category', $search->getSubCategory());
        }
        if (!empty($search->getLocation())) {
            if ($search->getLocation() == "Toute la france") {
                $query = $query
                    ->andWhere('s.name LIKE :sub_category')
                    ->setParameter('sub_category', $search->getSubCategory());
            } else {
                $query = $query
                    ->andWhere('r.name LIKE :location')
                    ->setParameter('location', $search->getLocation());
            }
        }
        return $query->getQuery()->getResult();
    }

    public function findOneById($id)
    {
        $query = $this  
            ->createQueryBuilder('p')
            ->select('p')
            ->andWhere('p.id LIKE :id')
            ->setParameter('id', $id);

        return $query->getQuery()->getResult();
    }

    public function findByUserId($user_id)
    {
        $query = $this
            ->createQueryBuilder('p')
            ->select('p')
            ->join('p.author', 'u')
            ->andWhere('u.id LIKE :id')
            ->setParameter('id', $user_id)
            ->andWhere('p.is_deleted LIKE :false')
            ->setParameter('false', FALSE);

        return $query->getQuery()->getResult();
    }
    
//    /**
//     * @return Product[] Returns an array of Product objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
