<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email', EmailType::class)
            ->add('roles',
                ChoiceType::class, [
                    'choices' => ['Admin' => 'ROLE_ADMIN', 'Formateur' => 'ROLE_FORMATEUR'],
                    'multiple' => true,
                    'expanded' => true, 'constraints' => [new Count(['max' => 1])]
//                'expanded' => FALSE,
                ]
            )
            ->add('phoneNumber', TelType::class)
//            ->add('profilePic', FileType::class, ['multiple' => false, 'attr' => ["accept" => "image/*"]])
            ->add('birthdate', DateType::class, ['widget' => 'single_text', 'attr' => [
                'max' => date('Y-m-d')
            ]])
            ->add('speciality')
            ->add('hiringDate', DateType::class, ['widget' => 'single_text'])
            ->add('locked')
            ->add('isVerified')
            ->add('save', SubmitType::class,
                ['attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'],
                    'row_attr' => ['class' => 'mt-3 text-end']]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate'], 'label_attr' => ['class' => 'col-md-2 col-form-label']
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; // return an empty string here
    }
}
