<?php

namespace App\Repository;

use App\Entity\Etat;
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

        // ajout du filtre par sorties passees si necessaire
        if ($passees == true) {
            $qb->join("s.etat", "e")
                ->andWhere('e.id = 5');
        } else {
            $qb->join("s.etat", "e")
                ->andWhere($qb->expr()->between('e.id', 2, 4))
                ->orWhere('e.id = 1 and s.organisateur = :orga')
                ->setParameter('orga', $user);
        }

        // ajout du filtre par inscrit/pas inscrit si necessaire
        if ($inscrit == true) {
            $qb->andWhere(':inscrit MEMBER OF s.inscrit')
                ->setParameter('inscrit', $user);
        } elseif ($pasInscrit == true) {
            $qb->andWhere(':inscrit NOT MEMBER OF s.inscrit')
                ->setParameter('inscrit', $user);
        }

        // ajout des mot cles a la requete si necessaire
        if ($motCles != null) {
            $qb->andWhere($qb->expr()->like('s.nom',
                $qb->expr()->literal("%" . ":motcles" . "%")))
                    ->setParameter('motcles', $motCles);
        }

        // ajout de la date debut a la requete SQL si necessaire
        if ($dateDebutRech != null) {
            $qb->andWhere('s.dateDebut >= :dateDebutRech')
                ->setParameter('dateDebutRech', $dateDebutRech);
        }

        // ajout de la date fin a la requete SQL si necessaire
        if ($dateFinRech != null) {
            $qb->andWhere('s.dateDebut <= :dateFinRech')
                ->setParameter('dateFinRech', $dateFinRech);
        }

        // execution de la requete et envoie du resultat
        return $qb->orderBy('s.dateDebut', 'ASC')->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findAccueil()
    {
        // creation du query builder
        $qb = $this->createQueryBuilder('s');

        //recherche des sorties avec etat non passees ou annulees
        $qb->join('s.etat', 'e')
            ->where('e.id = 2');

        // execution de la requete et envoie du resultat
        return $qb->orderBy('s.dateDebut', 'ASC')->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findAArchiver($date)
    {
        //creation du query builder
        $qb = $this->createQueryBuilder('s');

        //recuperation des sorties qui ont plus d'un mois depuis leur déroulement
        $qb->where('s.dateDebut < :date')
            ->setParameter('date', $date);

        // execution de la requete et envoie du resultat
        return $qb->getQuery()->getResult();
    }

    /**
     * @return int
     */
    public function countParticipants($id)
    {
        //creation du query builder et de la date recherchee
        $qb = $this->createQueryBuilder('s');

        // compte du nombre de participants à une sortie
        $qb->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->join('s.inscrit', 'i')
            ->select($qb->expr()->count('i.id'));

        // execution de la requete et envoie du resultat
        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findACloturer($now)
    {
        //creation du query builder
        $qb = $this->createQueryBuilder('s');

        //recuperation des sorties dont la date d'inscription est passee et qui ne sont encore ouvertes
        $qb->where('s.dateFinInscription < :now')
            ->setParameter('now', $now)
            ->join('s.etat', 'e')
            ->andWhere('e.id = :etat')
            ->setParameter('etat', 2);

        // execution de la requete et envoie du resultat
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findEnCours($now)
    {
        //creation du query builder
        $qb = $this->createQueryBuilder('s');

        //recuperation des sorties qui sont en cours de deroulement
        $qb->where('s.dateDebut < :now')
            ->setParameter('now', $now)
            ->join('s.etat', 'e')
            ->andWhere($qb->expr()->between('e.id', 2, 3));


        // execution de la requete et envoie du resultat
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findAAnnuler($now)
    {
        //creation du query builder
        $qb = $this->createQueryBuilder('s');

        //recuperation des sorties qui sont a annuler
        $qb->where('s.dateFinInscription < :now')
            ->setParameter('now', $now)
            ->join('s.etat', 'e')
            ->andWhere('e.id = :creee')
            ->setParameter('creee', 1);


        // execution de la requete et envoie du resultat
        return $qb->getQuery()->getResult();
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findPassees($date)
    {
        //creation du query builder
        $qb = $this->createQueryBuilder('s');

        //ajustement de la date
        $date->sub(new \DateInterval('P1D'));

        //recuperation des sorties qui sont a passer a l'etat passee
        $qb->where('s.dateDebut < :date')
            ->setParameter('date', $date)
            ->join('s.etat', 'e')
            ->andWhere($qb->expr()->between('e.id', 2, 4));

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
