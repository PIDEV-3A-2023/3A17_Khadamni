<?php

namespace App\Form;

use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nomFormation',TextType::class,[
                'attr' => ['placeholder' => 'formation symfony']
            ])
            ->add('description',TextareaType::class,[
                'attr' => ['placeholder' => 'votre description ...']
            ])
            ->add('duree',IntegerType::class,[
                'attr' => [
                    'placeholder' => '2 semaines',
                    'min' => 1
                ]
            ])
            ->add('prix',IntegerType::class,[
                'attr' => [
                    'placeholder' => '100 TND',
                    'min' => 1
                ]
            ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
