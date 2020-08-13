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
            ->add('title',null,['label'=>'challenge.label.title'])
            ->add('description',null,['label'=>'challenge.label.description'])
            ->add('type',null,['label'=>'challenge.label.type'])
            ->add('banner',null,['label'=>'challenge.label.banner'])
            ->add('maxChallenger',null,['label'=>'challenge.label.maxChallenger'])
            ->add('registrationOpening',null,['label'=>'challenge.label.registrationOpening'])
            ->add('registrationClosing',null,['label'=>'challenge.label.registrationClosing'])
            ->add('rules',null,['label'=>'challenge.label.rules'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Challenge::class,
        ]);
    }
}
