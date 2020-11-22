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
            ->add('statLabel', null, ['label'=>"Libellé stat"])
            ->add('ratio')
            ->add('position')
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
                "attr" => ["placeholder" => ""]
            ])
            ->add('isUsedForScore', null, ['label' => "Est utilisé pour le score"])//            ->add('challenge')
            ->add('isStepToVictory', null, ['label' => "Est une etape vers la victoire"])//            ->add('challenge')
            ->add('stepToVictoryMin', null, ['label' => "Valeur min pour valider"])//            ->add('challenge')
            ->add('stepToVictoryMax', null, ['label' => "Valeur max pour valider"])//            ->add('challenge')
            ->add('displayBestForStats', null, ['label' => "Stat : meilleur score seulement"])//            ->add('challenge')
            ->add('displayForStats', null, ['label' => "Stat : cumul des runs"])//            ->add('challenge')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ChallengeSetting::class,
        ]);
    }
}
