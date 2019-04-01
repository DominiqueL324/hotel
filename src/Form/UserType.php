<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('cni')
            ->add('matricule')
            ->add('born_at')
            ->add('lieu_naissance')
            ->add('login')
            ->add('password')
            ->add('role',ChoiceType::class,[
                'choices'=> $this->getChoice()
            ])
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
