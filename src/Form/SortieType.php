<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Site;
use App\Entity\Sortie;

use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,
                [
                    "label" => "Nom de la sortie *",
                    "attr" => [
                        "placeholder" => "Apéro en terasse...",
                    ],
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ])
                    ]
                ])
            ->add('dateDebut', DateTimeType::class,
                [
                    "label" => "Date de la sortie *", "widget" => "single_text",
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ]),
                    ]
                ])
            ->add('duree', IntegerType::class,
                [
                    "required" => false,
                    'constraints' => [
                        new Positive([
                            'message' => "La durée doit être positive",
                        ]),
                    ],
                    "attr" => [
                        "placeholder" => "en min",
                    ],
                    "label" => "Durée",
                ])
            ->add('dateFinInscription', DateType::class,
                [
                    "label" => "Date de fin d'inscription *", "widget" => "single_text",
                    'constraints' => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ]),
                    ],
                ])
            ->add('nbInscriptionsMax', IntegerType::class,
                [
                    "label" => "Nombre de participants maximum",
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ]),
                        new Positive([
                            'message' => "Le nombre de participant doit être supérieur à 0"
                        ])
                    ]
                ])
            ->add('description', TextType::class,
                [
                    "required" => false,
                    "attr" => [
                        "placeholder" => "Sortie 100% fun",
                    ],
                    "label" => "Description",
                ])
            ->add('siteOrganisateur', EntityType::class,
                [
                    "class" => Site::class,
                    "disabled" => true,
                    "label" => "Site Organisateur"
                ])
            ->add('ville', EntityType::class,
                [
                    "class" => Ville::class,
                    'choice_label' => 'nom',
                    "label" => "Ville *",
                    'placeholder' => "Sélectionner une ville",
                    "mapped" => false,
                    "constraints" => [
                        new NotBlank([
                            'message' => "Veuillez remplir ce champs",
                        ])
                    ]
                ]);

        $builder->get('ville')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->addLieuField($form->getParent(), $form->getData());
            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                /* @var $lieu \App\Entity\Lieu */
                $lieu = $data->getLieu();
                $form = $event->getForm();
                if ($lieu) {
                    $ville = $lieu->getVille();
                    $this->addLieuField($form, $ville);
                    $form->get('ville')->setData($ville);
                } else {
                    $this->addLieuField($form, null);
                }
            }
        );


    }
    private function addLieuField(FormInterface $form, ?Ville $ville){
        $builder = $form->add('lieu', EntityType::class,[
            'class' => Lieu::class,
            'choice_label' => 'nom',
            'placeholder' => $ville ? 'Selectionnez votre lieu' : 'Selectionnez votre ville',
            'required' => true,
            'auto_initialize' => false,
            'choices' => $ville ? $ville->getLieux() : []
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
