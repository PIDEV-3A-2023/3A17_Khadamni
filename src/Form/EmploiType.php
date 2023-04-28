<?php

namespace App\Form;

use App\Entity\Emploi;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EmploiType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 5,
                        'max' => 100,
                        'minMessage' => 'Le titre doit avoir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le titre ne peut pas avoir plus de {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'min' => 10,
                        'max' => 500,
                        'minMessage' => 'La description doit avoir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La description ne peut pas avoir plus de {{ limit }} caractères.',
                    ]),
                ],
            ])


            ->add('salaire', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type([
                        'type' => 'numeric',
                        'message' => 'Le salaire doit être un nombre.',
                    ]),
                ],
            ])
            ->add('niveauExperience', ChoiceType::class, [
                'choices' => [
                    'Bac+3' => 'bac+3',
                    'Bac+4' => 'bac+4',
                    'Bac+5' => 'bac+5',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('typeContrat', ChoiceType::class, [
                'choices' => [
                    'Temps plein' => 'temps plein',
                    'Temps partiel' => 'temps partiel',
                    'Contractuel' => 'contractuel',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('salaire', NumberType::class, [
                'constraints' => [
                    new GreaterThan([
                        'value' => 0,
                        'message' => 'Le salaire doit être supérieur à zéro.'
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 1300,
                        'message' => 'Le salaire Le salaire doit être supérieur ou égal à 1300 DT.'
                    ])

                ],

            ]);
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Emploi::class,
        ]);
    }
}
