<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;
use http\Client\Curl\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ["label" => "Nom de la sortie"])
            ->add('dateDebut', DateTimeType::class, ["label" => "Date de la sortie", "widget" => "single_text"])
            ->add('duree', IntegerType::class, ["label" => "Durée"])
            ->add('dateFinInscription', DateType::class, ["label" => "Date de fin d'inscription", "widget" => "single_text"])
            ->add('nbInscriptionsMax', IntegerType::class, ["label" => "Nombre de participants maximum"])
            ->add('description', TextType::class, ["label" => "Description"])
            ->add('siteOrganisateur', EntityType::class, ["class"=>Site::class,"disabled"=>true ,"label" => "Site Organisateur"])
            ->add('lieu', EntityType::class, ["class"=>Lieu::class,'choice_label'=>'nom' , "label" => "Lieu"])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
