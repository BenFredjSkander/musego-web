<?php

namespace App\Form;

use App\Entity\Evenement;
use App\Entity\Sponsor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThan;

class SponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('photo', FileType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être de type PNG ou JPEG.',
                    ]),
                ],
                'data_class' => null,
                'required' => false,
                'attr' => ["accept" => "image/*"],

            ])
            ->add('capaciteFin', MoneyType::class, [
                'currency' => 'TND',
                'constraints' => [
                    new GreaterThan([
                        'value' => 1000,
                        'message' => 'La capacité financière doit être supérieure à 1000.'
                    ])
                ]
            ])
            ->add('idEvenement', EntityType::class, [
                'label' => 'Evenement',
                'class' => Evenement::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'],
                'row_attr' => ['class' => 'mt-3 text-end']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sponsor::class,
        ]);
    }
}
