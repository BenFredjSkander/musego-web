<?php

namespace App\Form;

use App\Entity\Artiste;
use App\Entity\Categorie;
use App\Entity\Oeuvre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class OeuvreFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                    new Assert\Regex([
                        'pattern' => '/^(?=\p{Lu})\p{L}+(?: \p{Lu}\p{L}+)*$/u',
                        'message' => 'Le titre doit etre valide et il doit commencer par une lettre majuscule et chaque mot doit commencer par une lettre majuscule.',

                    ]),
                ],
            ])
            ->add('dateCreation', DateType::class,
                ['widget' => 'single_text',
                    'constraints' => [
                        new Assert\NotBlank([
                            'message' => 'La date de creation ne peut pas être vide.',
                        ]),
                        new Assert\LessThanOrEqual([
                            'value' => 'today',
                            'message' => 'La date de creation doit être antérieure ou égale à aujourd\'hui.',
                        ]),
                    ],
                    'attr' => ['max' => date('Y-m-d')]
                ])
            ->add('idCategorie', EntityType::class, [
                'class' => Categorie::class,
                'label' => 'Categorie',
                'choice_label' => 'nom',

                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],

            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Ce champ ne doit pas être vide']),
                    new Assert\Callback([
                        'callback' => function ($value, ExecutionContextInterface $context) {
                            // Vérifier que la première lettre de chaque phrase est en majuscule
                            $sentences = preg_split('/(?<=[.?!])\s+/', $value);
                            foreach ($sentences as $sentence) {
                                if (preg_match('/^[a-z]/u', $sentence)) {
                                    $context->buildViolation('La première lettre de chaque phrase doit être en majuscule')
                                        ->addViolation();
                                    return;
                                }
                            }
                            // Vérifier que la première lettre de la première phrase est en majuscule
                            if (preg_match('/^[a-z]/u', $sentences[0])) {
                                $context->buildViolation('La première lettre de la première phrase doit être en majuscule')
                                    ->addViolation();
                            }
                        },
                    ]),
                ],
            ])
            ->add('lieuConservation', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Ce champ ne doit pas être vide']),
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
            ->add('idArtiste', EntityType::class, [
                'class' => Artiste::class,
                'label' => 'Artiste',
                'choice_label' => 'nom',

                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('Enregistrer', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'],
                'row_attr' => ['class' => 'mt-3 text-end']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Oeuvre::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
