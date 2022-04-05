<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\User;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use function Sodium\add;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pseudo', TextType::class, [
                'label' => 'Pseudo :'
                , 'constraints' => [
                    new NotBlank([
                        'message' => 'Un pseudo doit etre rempli',
                    ])
                ]
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom :'
                , 'constraints' => [
                    new NotBlank([
                        'message' => 'Un nom doit etre rempli',
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prenom :'
                , 'constraints' => [
                    new NotBlank([
                        'message' => 'Un prenom doit etre rempli',
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email :'
                , 'constraints' => [
                    new NotBlank([
                        'message' => 'le mail doit etre valide',
                    ])
                ]
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Telephone : ',
                'required' => false
            ])
            ->add('site', EntityType::class, [
                'class' => Site::class,
                'choice_label' => 'nom'])
            ->add('password', PasswordType::class, [
                'label' => 'Password :'
                , 'constraints' => [
                    new NotBlank([
                        'message' => 'le password doit etre valide',
                    ])
                ]
            ])
            ->add('confirm_password', PasswordType::class, [
                'label' => 'Confirmez Password :',
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'le password doit etre le meme',
                    ])
                ]
            ])

            ->add('photo',FileType::class,[
                'mapped'=>false,
                'required'=>false,
                'label'=>'Choisir une photo de profil : '
            ]);


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
