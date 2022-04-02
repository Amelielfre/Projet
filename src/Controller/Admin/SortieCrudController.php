<?php

namespace App\Controller\Admin;

use App\Entity\Sortie;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SortieCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Sortie::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom', 'Nom'),
            DateTimeField::new('dateDebut', 'Date de début'),
            IntegerField::new('duree', 'Durée'),
            IntegerField::new('nbInscriptionsMax', "Nombre maximum d'inscriptions"),
            AssociationField::new('inscrit', 'Liste des inscrits'),
            AssociationField::new('etat', 'Etat'),
            TextField::new('motifAnnulation', "Motif d'annulation")
        ];
    }

}
