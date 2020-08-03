<?php

namespace App\Form;

use App\Entity\Challenge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('dateStart')
            ->add('dateEnd')
            ->add('hourStart')
            ->add('hourEnd')
            ->add('description')
            ->add('type')
            ->add('banner')
            ->add('maxChallenger')
            ->add('registrationOpening')
            ->add('registrationClosing')
            ->add('rules')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class,
        ]);
    }
}
