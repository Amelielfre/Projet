<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            EmailField::new('email', 'Email'),
            TextField::new('nom', 'Nom')->setFormTypeOption('disabled','disabled'),
            TextField::new('password', 'Mot de passe')->setFormTypeOption('disabled','disabled'),
            TextField::new('prenom', 'Prénom')->setFormTypeOption('disabled','disabled'),
            TextField::new('pseudo', 'Pseudo'),
            TelephoneField::new('telephone', 'Téléphone')->setFormTypeOption('disabled','disabled'),
            ArrayField::new('roles', 'Rôles'),
            BooleanField::new('actif', 'Actif'),
            AssociationField::new('site', 'Campus'),
        ];
    }

}
