<?php

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EtatReclamationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('etatReclamation', ChoiceType::class, [
                'choices' => [
                    'Traitee' => 'traitee',
                    'En cours' => 'en_cours',
                    'Non traitee' => 'non_traitee',
                ],
                'placeholder' => 'Select status', // Optional placeholder
            ])
            ->add('sujet')
            ->add('motif');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
