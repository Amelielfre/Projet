<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('email', EmailType::class, ["label" => "E-mail *", 'constraints' => [
                new NotBlank([
                    'message' => "l'email doit etre valide",
                ])
            ]])
            ->add('nom', TextType::class, ["label" => "Nom ", 'constraints' => [
                new NotBlank([
                    'message' => 'Un nom doit etre rempli',
                ])
            ]])
            ->add('prenom', TextType::class, ["label" => "Prenom ", 'constraints' => [
                new NotBlank([
                    'message' => 'Un prenom doit etre rempli',
                ])
            ]])
            ->add('pseudo', TextType::class, ["label" => "Pseudo *", 'constraints' => [
                new NotBlank([
                    'message' => 'Un pseudo doit etre rempli',
                ])
            ]])
            ->add('telephone', TextType::class, [
                "label" => "Téléphone",
                'required' => false])

            ->add('photo',FileType::class,[
                'mapped'=>false,
                'required'=>false,
                'label'=>'Choisir une photo de profil : '
            ])

            ->add('oldPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel  *',
                'mapped' => false,
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe :',
                'mapped' => false,
                'required' => false
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmez nouveau mot de passe :',
                'mapped' => false,
                'required' => false
            ])
            ->add('partage', CheckboxType::class, [
                'label'    => 'Voulez-vous partager votre photo avec les autres utilisateurs ?',
                'required' => false,
                'data' => false,
            ])
//            ->add('enregistrer', SubmitType::class, ["label" => "Enregistrer Informations"])
//            ->add('password', PasswordType::class, [
//
//                // instead of being set onto the object directly,
//                // this is read and encoded in the controller
//                'mapped' => false,
//                'attr' => ['value' => "FDP123456"],
//                'constraints' => [
//                    new NotBlank([
//                        'message' => 'Mot de passe :',
//                    ]),
//                    new Length([
//                        'min' => 6,
//                        'minMessage' => 'Votre password doit au moins avoir {{ limit }} caractères',
//                        // max length allowed by Symfony for security reasons
//                        'max' => 4096,
//                    ]),
//                ],
//            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
