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
            ->add('email', EmailType::class, ["label" => "Mail :", 'constraints' => [
                new NotBlank([
                    'message' => 'le mail doit etre valide',
                ])
            ]])
            ->add('nom', TextType::class, ["label" => "Nom :", 'constraints' => [
                new NotBlank([
                    'message' => 'Un nom doit etre rempli',
                ])
            ]])
            ->add('prenom', TextType::class, ["label" => "Prenom :", 'constraints' => [
                new NotBlank([
                    'message' => 'Un prenom doit etre rempli',
                ])
            ]])
            ->add('pseudo', TextType::class, ["label" => "Pseudo :", 'constraints' => [
                new NotBlank([
                    'message' => 'Un pseudo doit etre rempli',
                ])
            ]])
            ->add('telephone', TextType::class, ["label" => "Téléphone:"])
            ->add('oldPassword', PasswordType::class, ['label' => 'Ancien mot de passe :', 'mapped' => false])
            ->add('password', PasswordType::class, [
                'label' => 'Nouveau mot de passe :'])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmez nouveau mot de passe :',
                'mapped' => false
            ])
            ->add('enregistrer', SubmitType::class, ["label" => "Enregistrer Informations"])
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
