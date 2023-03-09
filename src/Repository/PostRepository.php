<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Post>
 *
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    public function add(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Post $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



    /**
     * @return Post[] Returns an array of Post objects
     */
    public function findTheLastFourPostType($value): array
    {
        return $this->createQueryBuilder('p')
        ->leftJoin('p.user', 'a')
        ->where('a.type = :type')
        ->setParameter('type', $value)
        ->orderBy('p.createdAt', 'DESC')
        ->setMaxResults(4)
        ->getQuery()
        ->getResult();
        
    }

    /**
     * 
     * @return Post[] Returns an array of Movie objects ordered by title
     */
    public function findAllPostsBySearch($needle = null){
        // Quand on créer requpete personnalisé avec le builder , on utilise la ligne ci-dessous
        return $this->createQueryBuilder('p')
            ->orderBy("p.title")
            ->where("p.title LIKE :needle")
            ->setParameter("needle","%$needle%")
            ->getQuery()
            ->getResult();
    }
     

//    public function findOneBySomeField($value): ?Post
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
