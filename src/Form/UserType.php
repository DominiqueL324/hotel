<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('cni')
            ->add('matricule')
            ->add('born_at',DateType::class,[
                'label'=>'Né le',
                'widget'=>'single_text'
            ])
            ->add('lieu_naissance')
            ->add('roles',ChoiceType::class,[
                'choices'=> $this->getChoice()
            ])
            ->add('username',null,[
                'label'=>"Nom d'utilisateur"
            ])
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

     private function getChoice()
    {
        $roles = User::ROLE;
        $output = [];
        foreach ($roles as $key => $value) {
            $output[$value] = $key;
        }
        return $output;
    }
}
