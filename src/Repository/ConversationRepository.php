<?php

namespace App\Repository;

use App\Entity\Conversation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    public function add(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findConversationByUserId($id){
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
                SELECT conversation.* FROM conversation 
                WHERE conversation.user1_id ='.$id.'
                OR conversation.user2_id ='.$id;
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

         // returns the result
         return $resultSet->fetchAllAssociative();
    }

    public function findConversation($id_1, $id_2){
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT conversation.* FROM conversation 
            INNER JOIN user ON (conversation.user1_id =' .$id_1.' 
            AND conversation.user2_id ='.$id_2.') OR (conversation.user1_id =' .$id_2.'  
            AND conversation.user2_id ='.$id_1.')'
        ;
        
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();

         // returns the result
         return $resultSet->fetchAssociative();
    }



    /**
     * @return Conversation[] Returns Conversation objects
     */
   public function findTheConversation($user1, $user2)
    {
        return $this->createQueryBuilder('c')
        ->where('c.user1 = :user1 AND c.user2 = :user2')
        ->orWhere('c.user1 = :user2 AND c.user2 = :user1')
        ->setParameter('user1', $user1)
        ->setParameter('user2', $user2)
        ->getQuery()
        ->getOneOrNullResult();
        ;
    }

//    public function findOneBySomeField($value): ?Conversation
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
