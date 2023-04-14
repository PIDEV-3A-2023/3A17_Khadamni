<?php

namespace App\Form;

use App\Entity\Stage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
class StageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('poste', null, [
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z]/', // le champ doit commencer par une lettre (minuscule ou majuscule)
                    'message' => 'Le champ doit commencer par une lettre.',
                ]),
            ],
        ])
        ->add('nomEntreprise', null, [
            'constraints' => [
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z]/', // le champ doit commencer par une lettre (minuscule ou majuscule)
                    'message' => 'Le champ doit commencer par une lettre.',
                ]),
            ],
        ])
            ->add('adresseStage')
            ->add('dureeStage')
            ->add('typeStage', ChoiceType::class, [
                'choices' => [
                    '' => '',
                    'payant' => 'payant',
                    'non payant' => 'non_payant',
                    
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stage::class,
        ]);
    }
}
