<?php

namespace App\Repository;

use App\Entity\Formation;
use App\Entity\Inscription;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 *
 * @method Formation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Formation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Formation[]    findAll()
 * @method Formation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function save(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Formation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function findformationByX($value,$critere) {
        return $this->createQueryBuilder('formation')
            ->where('formation.'.$critere.' LIKE :value')
            ->setParameter('value',$value.'%')
            ->getQuery()
            ->getResult();
    }
    public function findformationByNomFormateur($nom,$prenom) {
        $em = $this->getEntityManager();
        $req = $em->createQuery('
   SELECT f
   FROM App\Entity\Formation f
   JOIN App\Entity\User u 
   WHERE f.idFormateur = u.idUser 
   AND (
       (LOWER(u.nom) LIKE LOWER(:nomConcat) AND :prenom = \'\') 
       OR (LOWER(u.nom) = LOWER(:nom) AND LOWER(u.prenom) LIKE LOWER(:prenomConcat))
   )
')->setParameters([
            'nomConcat' => $nom.'%',
            'prenomConcat' => $prenom.'%',
            'nom' => $nom,
            'prenom' => $prenom
        ]);

        return $req->getResult();

    }

    public function getNomFormateur($id): ?string
    {
        $entityManager = $this->getEntityManager();
        $user = $entityManager->getRepository(User::class)->find($id);

        if ($user) {
            return $user->getNom() . ' ' . $user->getPrenom();
        }

        return null;
    }

    public function findAllExceptMine($id)
    {

        $em = $this->getEntityManager();
        $formations = $em->createQuery('
        SELECT f
        FROM App\Entity\Formation f
        WHERE f.idFormateur != :id       
    ')->setParameter('id', $id)->getResult();
        foreach ($formations as $key => $formation) {
            $i = $this->getEntityManager()->getRepository(Inscription::class)->findOneBy(['idFormation'=>$formation->getIdFormation(),
                'idUser'=>$id]);
           if (isset($i)) {
               unset($formations[$key]);
           }
        }

        return $formations;

    }




//    /**
//     * @return Formation[] Returns an array of Formation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder("f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Formation
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
