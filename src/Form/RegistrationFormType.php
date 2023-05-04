<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Nom doit pas etre vide']),
                ],
            ])
            ->add('prenom', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Prenom doit pas etre vide']),
                ],
            ])
            ->add('num_tel', null, [
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/\d{8}$/',
                        'message' => 'Numéro de téléphone invalide',
                    ]),
                ],
            ])
            ->add('adresse', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Adresse doit pas etre vide']),
                ],
            ])
            ->add('centre_intere')
            ->add('adresse_entreprise')
            ->add('nom_entreprise')
            ->add('cv')
            ->add('etat_user')
            ->add('age', null, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Age doit pas etre vide']),
                ],
            ])
            ->add('note')
            ->add('rating')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
