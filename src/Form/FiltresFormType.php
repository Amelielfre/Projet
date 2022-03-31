<?php

namespace App\Form;

use App\Controller\Admin\DashboardController;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FiltresFormType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('site', EntityType::class, [
                'label' => 'Site :',
                'class' => Site::class,
                'choice_label' => 'nom'
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => "Entre : ",
                'widget' => "single_text",
                'required' => false])
            ->add('dateFin', DateTimeType::class, [
                'label' => "et : ",
                'widget' => "single_text",
                'required' => false])
            ->add('rechercher', SubmitType::class, [
                'label' => "Rechercher"]);
    }

}