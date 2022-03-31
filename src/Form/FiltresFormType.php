<?php

namespace App\Form;

use App\Controller\Admin\DashboardController;
use App\Entity\Site;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class FiltresFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'label' => 'Site :',
                'class' => Site::class,
                'choice_label' => 'nom'
            ])
            ->add('motCles', TextType::class, [
                'label' => 'le nom de la sortie contient : ',
                'required' => false
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => "Entre : ",
                'widget' => "single_text",
                'required' => false])
            ->add('dateFin', DateTimeType::class, [
                'label' => "et : ",
                'widget' => "single_text",
                'required' => false])
            ->add('orga', CheckboxType::class, [
                'label' => "Sorties dont je suis l'organisateur/trice",
                'required' => false
            ])
            ->add('inscrit', CheckboxType::class, [
                'label' => "Sorties auxquelles je suis inscrit/e",
                'required' => false
            ])
            ->add('pasInscrit', CheckboxType::class, [
                'label' => "Sorties auxquelles je ne suis pas inscrit/e",
                'required' => false
            ])
            ->add('passees', CheckboxType::class, [
                'label' => "Sorties passées",
                'required' => false
            ])
            ->add('rechercher', SubmitType::class, [
                'label' => "Rechercher"]);
    }

}