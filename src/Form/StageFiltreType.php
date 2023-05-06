<?php

namespace App\Form;
use App\Entity\Stage; // Replace with the correct namespace for your Stage entity
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StageFiltreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Build your form fields here
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Stage::class, // Set the correct class for your Stage entity
        ]);
    }
}
