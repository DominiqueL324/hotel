<?php

namespace App\Form;

use App\Entity\Offre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OffreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle')
            ->add('superficie')
            ->add('etage')
            ->add('clim',ChoiceType::class,[
                'choices'=> $this->getChoiceClim()
            ])
            ->add('wifi',ChoiceType::class,[
                'choices'=> $this->getChoiceWifi()
            ])
            ->add('porte')
            ->add('prix')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Offre::class,
        ]);
    }
    private function getChoiceClim()
    {
        $roles = Offre::CLIM;
        $output = [];
        foreach ($roles as $key => $value) {
            $output[$value] = $key;
        }
        return $output;
    }

    private function getChoiceWifi()
    {
        $roles = Offre::WIFI;
        $output = [];
        foreach ($roles as $key => $value) {
            $output[$value] = $key;
        }
        return $output;
    }
}
