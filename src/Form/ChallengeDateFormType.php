<?php

namespace App\Form;

use App\Entity\ChallengeDate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeDateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDate', DateType::class, [
                'format' => DateType::HTML5_FORMAT,
                'widget' => 'single_text',
            ])
            ->add('endDate', DateType::class, [
                'format' => DateType::HTML5_FORMAT,
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChallengeDate::class,
        ]);
    }
}
