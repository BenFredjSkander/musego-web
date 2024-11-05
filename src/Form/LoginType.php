<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['error_bubbling' => false,
                'attr' => ['autocomplete' => 'email'], 'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email',
                    ]),
                    new Email(['message' => 'Please enter a valid email address.']),
                ],])
            ->add('password', PasswordType::class,
                ['label' => 'Mot de passe',
                    'error_bubbling' => false,
                    'attr' => [
                        'class' => 'password-field, form-control',
                        'autocomplete' => 'new-password',
                        "data-toggle" => "password",
                        "data-eye-open-class" => "uil-eye-slash",
                        "data-eye-close-class" => "uil-eye"
                    ],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ]
                ])
            ->add('login', SubmitType::class, ['label' => 'Se connecter',
                    'attr' => ['class' => 'btn btn-primary w-sm waves-effect waves-light'],
                    'row_attr' => ['class' => 'mt-3 text-end']]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['novalidate' => 'novalidate', 'data-form-type' => 'login'],
        ]);
    }

    public function getBlockPrefix(): string
    {
        return ''; // return an empty string here
    }
}
