<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFromAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('email')
            ->add('roles')->add('roles', ChoiceType::class, array(
                'label' => 'Role : (par défaut User) ',
                'mapped' => true,
                'expanded' => true,
                'multiple' => true,
                'choices' => array(
                    'Admin' => 'ROLE_ADMIN',
                )
            ))
            ->add('isActivate', ChoiceType::class, array(
                'choices' => array(
                    'active' => true,
                    'inactive' => false
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
