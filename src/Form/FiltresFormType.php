<?php

namespace App\Form;

use App\Controller\Admin\DashboardController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class FiltresFormType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateDebut', DateTimeType::class, [
                'label' => "Entre : ",
                'widget' => "single_text"])
            ->add('dateFin', DateTimeType::class, [
                'label' => "et : ",
                'widget' => "single_text"])
            ->add('rechercher', SubmitType::class, [
                'label' => "Rechercher"]);
    }

}