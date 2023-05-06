<?php

namespace App\Form;

use App\Entity\Avis;
Use App\Entity\Evenement;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note')
            ->add('commentaire')
            ->add('dateCreation')
            ->add('idEvenement',EntityType::class,
                ['class'=>Evenement::class,
                    'choice_label'=>'nomevenement',
                    'label'=>'L \'évenement'
                ])
            ->add('idUtilisateur',EntityType::class,
                ['class'=>User::class,
                    'choice_label'=>'nom',
                    'label'=>'L \'utilisateur'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
