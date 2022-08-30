<?php

namespace App\Form;

use App\Entity\Region;
use App\Entity\Search;
use App\Entity\SubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sub_category', EntityType::class, [
                'class' => SubCategory::class,
                'group_by' => 'category',
                'placeholder' => "Choisir une catégorie",
                'label' => 'Que recherchez-vous ?',
                'required' => true,
                'multiple' => false,
                'expanded' => false
            ])
            ->add('location', EntityType::class, [
                'label' => false,
                'class' => Region::class,
                'placeholder' => "Choisir une location",
                'required' => false,
                'multiple' => false,
                'expanded' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Rechercher'
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
        ]);
    }
}