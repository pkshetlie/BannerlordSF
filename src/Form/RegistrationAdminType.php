<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', null, [
                'label' => 'user.label.firstname',
                'attr' => ['placeholder' => 'user.placeholder.firstname']
            ])
            ->add('lastname', null, [
                'label' => 'user.label.lastname',
                'attr' => [
                    'placeholder' => 'user.placeholder.lastname'
                ]
            ])
            ->add('username', null, [
                'label' => 'user.label.username',
                'attr' => [
                    'placeholder' => 'user.placeholder.username'
                ]
            ])
            ->add('email', null, [
                'label' => 'user.label.email',
                'attr' => [
                    'placeholder' => 'user.placeholder.email'
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    "Admin" => "ROLE_ADMIN",
                    "Arbitre" => "ROLE_ARBITRE",
                    "Caster" => "ROLE_CASTER",
                    "Développeur" => "ROLE_DEVELOPPEUR",
                    "Rédacteur" => "ROLE_REDACTEUR",
                    "Joueur" => "ROLE_USER",
                ],
                'multiple' => true,
                'label' => 'user.label.roles',
                'attr' => [
                    'placeholder' => 'user.placeholder.roles'
                ]
            ])
            ->add('twitchID', null, [
                'label' => 'user.label.twitchID',
                'required' => false,
                'attr' => [
                    'placeholder' => 'user.placeholder.twitchID'
                ]
            ])
            ->add('discordID', null, [
                'label' => 'user.label.discordID',
                'required' => false,
                'attr' => [
                    'placeholder' => 'user.placeholder.discordID'
                ]
            ])
            ->add('babyProof', CheckboxType::class, [
                'label' => 'Mode baby proof',
                'required' => false,
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'required' => false,
                'constraints' => [

//                    new Regex([
//                        'pattern' => '#^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}$#',
//                        'message' => 'Votre mot de passe n\'est pas suffismeent fort, vous devez mettre au moins 8 caractère dont des majuscules '
//                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
