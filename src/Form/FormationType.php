<?php

namespace App\Form;

use App\Entity\Atelier;
use App\Entity\Formation;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Champs Obligatoire'
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-z,A-Z,0-9,\s ]+$/',
                        'message' => 'Ce champs ne doit être rempli que de caractères simple'
                    ])
                ]])
            ->add('atelier', EntityType::class, [
                'class' => Atelier::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => false
            ])
            ->add('dateDebut', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Champs Obligatoire'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'value' => 'today',
                    ]),

                ],
                'attr' => ['min' => date('Y-m-d')]
            ])
            ->add('dateFin', DateType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Champs Obligatoire'
                    ]),
                    new Assert\GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[dateDebut].data',
                        'message' => ' date  fin <  date début.',
                    ]),
                ],
                'attr' => ['min' => date('Y-m-d')]
            ])
            ->add('niveau', ChoiceType::class, [
                'choices' => [
                    'debutant' => 'debutant',
                    'intermediaire' => 'intermediaire',
                    'avance' => 'avance'
                ]
            ])
            ->add('idFormateur', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'multiple' => false,
                'expanded' => false,
                'label' => 'Formateur',
                'query_builder' => function (UserRepository $er) {
                    return $er->getFormateurUsers();
                }
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'],
                'row_attr' => ['class' => 'mt-3 text-end']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
