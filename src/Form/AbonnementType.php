<?php

namespace App\Form;

use App\Entity\Abonnement;
use App\Entity\Offre;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AbonnementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => ['Hebdomadaire' => 'Hebdomadaire', 'Mensuel' => 'Menusel', 'Annuel' => 'Annuel'],
                'constraints' => [
                    new Assert\NotBlank(),
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
            ->add('idOffre', EntityType::class, [
                'class' => Offre::class,
                'choice_label' => function (Offre $offre) {
                    return $offre->getId() . ': ' . $offre->getType() . ', ' . $offre->getPrix() . '€';},
                'multiple' => false,
                'expanded' => false,
                'label' => 'Offre'
            ])
            ->add('idUser', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return $user->getId() . ': ' . $user->getUsername();},
                'multiple' => false,
                'expanded' => false,
                'label' => 'Utilisateur'
            ])
            ->add('enregistrer', SubmitType::class, ['attr' => ['class' => 'btn btn-primary waves-effect waves-light w-md'], 'row_attr' => ['class' => 'mt-3 text-end']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Abonnement::class,
            'attr' => ['novalidate' => 'novalidate'],
        ]);
    }
}
