<?php

namespace App\Form;

use App\Entity\Prerequis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class PrerequisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('niveauetude', ChoiceType::class, [
                'choices' => [
                    'bac' => 'bac',
                    'bac+1' => 'bac_1',
                    'bac+2' => 'bac_2',
                    'bac+3' => 'bac_3',
                    'bac+4' => 'bac_4',
                    'bac+5' => 'bac_5',


                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('description');
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prerequis::class,
        ]);
    }
}
