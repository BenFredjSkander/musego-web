<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['attr' => ['autocomplete' => 'email']])
            ->add('username', null, ['attr' => ['autocomplete' => 'given-name', 'autocapitalize' => 'words']])
            ->add('phoneNumber', TelType::class)
            ->add('register', SubmitType::class, ['label' => 'Enregistre', 'attr' => ['class' => 'btn btn-secondary w-sm waves-effect waves-light'], 'row_attr' => ['class' => 'mt-3 text-right']]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
