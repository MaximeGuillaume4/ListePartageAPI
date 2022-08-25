<?php

namespace App\Repository;

use App\Entity\Partage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Partage>
 *
 * @method Partage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Partage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Partage[]    findAll()
 * @method Partage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PartageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Partage::class);
    }

    public function add(Partage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Partage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findPartageListesByUserId(int $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT pl.partage_id, pl.liste_id
            FROM partage_user pu
            LEFT JOIN partage_liste pl on pl.partage_id = pu.partage_id
            WHERE pu.user_id = :user_id
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['user_id' => $userId]);

        return $resultSet->fetchAllAssociative();
    }

    public function partageExists(int $userId, int $listeId): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = '
            SELECT pl.partage_id
            FROM partage_user pu
            LEFT JOIN partage_liste pl on pl.partage_id = pu.partage_id
            WHERE pu.user_id = :user_id and pl.liste_id = :liste_id
            ';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery(['user_id' => $userId, 'liste_id' => $listeId]);

        if(sizeof($resultSet->fetchAllAssociative()) > 0){
            return true;
        }
        else{
            return false;
        }

    }

//    /**
//     * @return Partage[] Returns an array of Partage objects
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

//    public function findOneBySomeField($value): ?Partage
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
