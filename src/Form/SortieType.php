<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;

use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,
                [
                    "label" => "Nom de la sortie",
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ])
                    ]
                ])
            ->add('dateDebut', DateTimeType::class,
                [
                    "label" => "Date de la sortie", "widget" => "single_text",
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ])
                    ]
                ])
            ->add('duree', IntegerType::class,
                [
                    "required"=>false,
                    "label" => "Durée",
                ])
            ->add('dateFinInscription', DateType::class,
                [
                    "label" => "Date de fin d'inscription", "widget" => "single_text",
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ])
                    ]
                ])
            ->add('nbInscriptionsMax', IntegerType::class,
                [
                    "label" => "Nombre de participants maximum",
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ])
                    ]
                ])
            ->add('description', TextType::class,
                [
                    "required"=>false,
                    "label" => "Description",
                ])
            ->add('siteOrganisateur', EntityType::class,
                [
                    "class"=>Site::class,
                    "disabled"=>true,
                    "label" => "Site Organisateur"
                ])
            ->add('ville', EntityType::class,
                [
                    "class"=>Ville::class,
                    'choice_label'=>'nom' ,
                    "label" => "Ville",
                    "mapped"=>false,
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ])
                    ]
                ])
            ->add('ajout', SubmitType::class,['label' => 'Confirmer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
