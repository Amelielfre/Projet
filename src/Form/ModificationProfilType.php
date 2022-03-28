<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class ModificationProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ["label" => "Mail :",'attr' => ['value' => "FDP123456"]])
            ->add('nom', TextType::class, ["label" => "Nom :"])
            ->add('prenom', TextType::class, ["label" => "Prenom :"])
            ->add('pseudo', TextType::class, ["label" => "Pseudo :"])
            ->add('telephone', TextType::class, ["label" => "Téléphone:"])
            ->add('enregistrer', SubmitType::class, ["label" => "Enregistrer Informations"])
            ->add('password', PasswordType::class, [

                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['value' => "FDP123456"],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Mot de passe :',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre password doit au moins avoir {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
