<?php

namespace App\Form;

use App\Entity\Upload;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
//            ->add('uploads', EntityType::class, array(
//                'class' => Upload::class,
//                'choice_label' => 'imageName'
//            ))
            ->add('save', SubmitType::class)
//            ->add('roles')
//            ->add('password')
//            ->add('avatar', FileType::class, array('label' => 'Photo (png, jpeg)'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
