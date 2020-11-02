<?php

namespace App\Form;

use App\Entity\ChallengeSetting;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChallengeSettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('ratio')
            ->add('inputType',ChoiceType::class,[
                'choices'=>[
                    "Champ texte"=>ChallengeSetting::TEXT,
                    "Liste déroulante"=>ChallengeSetting::SELECT,
                    "Case à cocher"=>ChallengeSetting::CHECKBOX,
                ],
                "label"=>"Type de champ à remplir"
            ])
            ->add('defaultValue', null, [
                "label" => "Valeur par défaut",
                "attr" => ["placeholder" => "séparer avec ; les differentes valeur de select"]
            ])
            ->add('isUsedForScore', null, ['label' => "Est utilisé pour le score"])//            ->add('challenge')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChallengeSetting::class,
        ]);
    }
}
