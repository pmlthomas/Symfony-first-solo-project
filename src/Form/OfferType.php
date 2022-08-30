<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\Product;
use App\Entity\Region;
use App\Entity\SubCategory;
use App\Repository\AddressRepository;
use App\Repository\RegionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class OfferType extends AbstractType
{
    private $security;
    private $userRepository;
    private $entityManager;
    private $regionRepository;
    public function __construct(AddressRepository $addressRepository, Security $security, EntityManagerInterface $entityManager, RegionRepository $regionRepository)
    {
        $this->addressRepository = $addressRepository;
        $this->security = $security;
        $this->entityManager = $entityManager;
        $this->regionRepository = $regionRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de votre offre',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Ex: table basse en bois'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de votre offre',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Informations supplémentaires concernant votre offre. Ex: Est-elle en bon état ?'
                ]
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix de votre offre',
                'required' => true,
                'attr' => [
                    'placeholder' => '€'
                ]
            ])
            ->add('illustration', FileType::class, [
                'label' => 'Image(s) de votre offre',
                'required' => true
            ])
            ->add('sub_category', EntityType::class, [
                'label' => 'Catégorie et sous-catégorie de votre offre',
                'required' => true,
                'class' => SubCategory::class,
                'group_by' => 'Category',
                'multiple' => false,
                'expanded' => false
            ])
            ->add('region', EntityType::class, [
                'label' => 'Votre region',
                'class' => Region::class,
                'required' => true,
                'multiple' => false,
                'expanded' => false
                ])
            ->add('address', EntityType::class, [
                'label' => 'Votre addresse',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'class' => Address::class,
                'choices' => $this->addressRepository->getUserAddresses($this->security->getUser()->getFirstName())
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter mon offre'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
