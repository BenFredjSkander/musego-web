<?php

namespace App\Form;

use App\Entity\Evenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class EvenementType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z]/', // Définir la regex pour vérifier la majuscule en début de chaîne
                        'message' => 'Le champ "Nom" doit commencer par une lettre majuscule.',
                    ]),
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan([
                        'value' => 'today',
                        'message' => 'La date de début doit être supérieure à la date actuelle.',
                    ]),
                ],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [

                    new Assert\NotBlank(),

                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[dateDebut].data',
                        'message' => 'La date de fin doit être supérieure à la date de début.',
                    ]),
                ],
            ])
            ->add('type', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z]/', // Définir la regex pour vérifier la majuscule en début de chaîne
                        'message' => 'Le champ "Type" doit commencer par une lettre majuscule.',
                    ]),
                ],
            ])
            ->add('lieu', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z]/', // Définir la regex pour vérifier la majuscule en début de chaîne
                        'message' => 'Le champ "lieu" doit commencer par une lettre majuscule.',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Regex([
                        'pattern' => '/^[A-Z]/', // Définir la regex pour vérifier la majuscule en début de chaîne
                        'message' => 'Le champ "description" doit commencer par une lettre majuscule.',
                    ]),
                ],
            ])

// ...

            ->add('poster', FileType::class, [
                'constraints' => [
                    new Assert\NotBlank(),

                ],
                'data_class' => null,
                'required' => false,
                'attr' => ["accept" => "image/*"],

            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'],
                'row_attr' => ['class' => 'mt-3 text-end']]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
            'attr' => ['novalidate' => 'novalidate'], 'label_attr' => ['class' => 'col-md-2 col-form-label']
        ]);
    }


}
