<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\ProductTag;
use Doctrine\DBAL\Types\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('tag', EntityType::class, array(
                'class' => ProductTag::class,
                'choice_label' => 'name'
            ))
//            ->add('images', CollectionType::class, array(
//                'entry_type' => ImageProductType::class,
//                'allow_add' => true,
//                'label' => "Image(s)"
//            ))
            ->add('description', TextareaType::class)
            ->add('content', CKEditorType::class, array(
                'config' => array(
                    'toolbar' => 'standard'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
