<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Sortie $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Sortie $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findByFiltres($site, $user, $orga, $inscrit, $pasInscrit, $passees, $motCles = null, $dateDebutRech = null, $dateFinRech = null)
    {
        $qb = $this->createQueryBuilder('s');

        // ajout du site à la requete SQL
        $qb->join("s.organisateur", "o")
            ->andWhere('o.site = :site')
            ->setParameter('site', $site->getId());

        //ajout du filtre par sorties organisees si necessaire
        if ($orga == true) {
            $qb->andWhere('s.organisateur = :orga')
                ->setParameter('orga', $user);
        }

        // ajout du filtre par inscrit/pas inscrit si necessaire
        if ($inscrit == true) {

        } elseif ($pasInscrit == true) {

        }

        // ajout du filtre par sorties passees si necessaire
        if ($passees == true) {
            $qb->join("s.etat", "e")
                ->andWhere('e.libelle = :etat')
                ->setParameter('etat', "passée");
        }

        // ajout des mot cles a la requete si necessaire
        if ($motCles != null) {
            $qb->andWhere($qb->expr()->like('s.nom',
                $qb->expr()->literal("%" . $motCles . "%")));
        }

        // ajout de la date a la requete SQL si necessaire
        if ($dateDebutRech != null && $dateFinRech != null) {
            $qb->andWhere('s.dateDebut >= :dateDebutRech')
                ->setParameter('dateDebutRech', $dateDebutRech)
                ->andWhere('s.dateDebut <= :dateFinRech')
                ->setParameter('dateFinRech', $dateFinRech);
        }

        // execution de la requete et envoie du resultat
        return $qb->getQuery()->getResult();
    }

//     /**
//      * @return Sortie[] Returns an array of Sortie objects
//      */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
