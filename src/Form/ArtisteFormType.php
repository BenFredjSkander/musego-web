<?php

namespace App\Form;

use App\Entity\Artiste;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ArtisteFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                    new Assert\Regex([
                        'pattern' => '/^(?=\p{Lu})\p{L}+(?: \p{Lu}\p{L}+)*$/u',
                        'message' => 'Le nom doit etre valide et il doit commencer par une lettre majuscule et chaque mot doit commencer par une lettre majuscule.',

                    ]),
                ],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                    new Assert\Regex([
                        'pattern' => '/^(?=\p{Lu})\p{L}+(?: \p{Lu}\p{L}+)*$/u',
                        'message' => 'Le prenom  doit etre valide et il doit  commencer par une lettre majuscule et chaque mot doit commencer par une lettre majuscule.',
                    ]),
                ],
            ])
            ->add('cin', NumberType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Le CIN ne doit contenir que des chiffres.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                    new Assert\Email([
                        'message' => 'L\'email n\'est pas valide',
                        'mode' => 'strict',
                    ]),
                ],
            ])
            ->add('dateNaissance', DateType::class,
                ['widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'La date de naissance ne peut pas être vide.',
                        ]),
                        new Assert\LessThanOrEqual([
                            'value' => 'today - 10 years',
                            'message' => "La date de naissance doit être d'au moins 10 ans avant la date actuelle.",
                        ]),
                    ],
                ])
            ->add('adresse', TextType::class, [
                'constraints' => [
                    new Assert\Callback([
                        'callback' => function ($value, ExecutionContextInterface $context) {
                            // Vérifier que chaque mot commence par une majuscule
                            $words = explode(' ', $value);
                            foreach ($words as $word) {
                                if (mb_strtoupper(mb_substr($word, 0, 1)) !== mb_substr($word, 0, 1)) {
                                    $context->buildViolation('Chaque mot doit commencer par une majuscule')
                                        ->addViolation();
                                    break;
                                }
                            }
                        },
                    ]),
                    new Assert\NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                ],
            ])
            ->add('specialite', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                    new Assert\Regex([
                        'pattern' => '/^[a-zA-Z]+$/i',
                        'message' => 'La specialite ne doit contenir que des lettres.',
                    ]),
                ],
            ])
            ->add('nationalite', CountryType::class, [
                'constraints' => [
                    new Assert\Country(['message' => 'Veuillez saisir un code pays valide.']),
                ],
            ])
            ->add('image', FileType::class, [
                'attr' => ["accept" => "image/*"],
                'data_class' => null,
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                    new File([
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (jpeg, jpg, png, gif)',
                    ])
                ],
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'],
                'row_attr' => ['class' => 'mt-3 text-end']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Artiste::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
