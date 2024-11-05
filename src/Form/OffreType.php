<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => ['Bronze' => 'Bronze', 'Silver' => 'Silver', 'Gold' => 'Gold'],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('prix', MoneyType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez remplir ce champs.']),
                    new Assert\PositiveOrZero([
                        'message' => 'Veuillez introduire une valeur positive.']),
                    new Assert\LessThan(10000),
                    new Assert\Regex([
                        'pattern' => '/^[0-9.\d ]+$/',
                        'message' => 'Ce champs ne peut contenir que des chiffres.'
                    ])
                ],
            ])
            ->add('promotion', PercentType::class, [
                'type' => 'integer',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez remplir ce champs.']),
                    new Assert\PositiveOrZero([
                        'message' => 'Veuillez introduire une valeur positive.']),
                    new Assert\LessThan(100),
                    new Assert\Regex([
                        'pattern' => '/^[0-9.\d ]+$/',
                        'message' => 'Ce champs ne peut contenir que des chiffres.'
                    ])
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez remplir ce champs.']),
                    new Assert\GreaterThan([
                        'value' => 'today',
                        'message' => 'La date de début doit être supérieure à la date actuelle.',
                    ]),
                ],
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Veuillez remplir ce champs.']),
                    new Assert\GreaterThan([
                        'propertyPath' => 'parent.all[dateDebut].data',
                        'message' => 'La date de fin doit être supérieure à la date de début.',
                    ]),
                ],
            ])
            ->add('image', FileType::class, [
                'constraints' => [
                    new Assert\File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Erreur :Le fichier doit être de type PNG ou JPEG.',
                    ]),
                ],
                'data_class' => null,
                'required' => false,
                'attr' => ["accept" => "image/*", "onchange" => "document.getElementById('blah').src = window.URL.createObjectURL(this.files[0])"],

            ])
            ->add('description', TextareaType::class)
            ->add('enregistrer', SubmitType::class, ['attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'], 'row_attr' => ['class' => 'mt-3 text-end']]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
            'attr' => ['novalidate' => 'novalidate'],
            'file_uri' => null
        ]);
    }
}
