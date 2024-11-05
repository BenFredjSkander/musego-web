<?php

namespace App\Form;

use App\Entity\Atelier;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;


class AtelierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Champs Obligatoire '
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-z,A-Z,\s ]+$/',
                        'message' => 'Ce champs ne peut contenir que des caracteres.'
                    ])
                ],
            ])
            ->add('image', FileType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Champs Obligatoire'
                    ]),
                    new Assert\File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Erreur :Le fichier doit être de type PNG ou JPEG.',
                        'maxSize' => '5M',
                        'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). La taille maximale autorisée est de {{ limit }} {{ suffix }}.',
                    ]),
                ],
                'data_class' => null,
//                'required' => false,
                'attr' => ["accept" => "image/*"],

            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'],
                'row_attr' => ['class' => 'mt-3 text-end']]);;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Atelier::class,
            'constraints' => [
                new UniqueEntity([
                    'entityClass' => Atelier::class,
                    'fields' => 'nom',
                    'message' => 'La valeur {{ value }} est déjà utilisé'
                ]),//assure l'unicité de l'attribut nom d'un atelier

            ],
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
