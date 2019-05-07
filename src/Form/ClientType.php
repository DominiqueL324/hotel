<?php

namespace App\Form;

use App\Entity\Client;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom')
            ->add('cni',null,[
                'label'=>'Numéro CNI'
            ])
            ->add('telephone')
            ->add('sexe', ChoiceType::class,[
                'choices'  => [
                        '*******' => 'john do',
                        'Homme' => 'homme',
                        'Femme' => 'femme',
                    ],
            ])
            ->add('type', ChoiceType::class,[
                'choices'  => [
                        '*******' => 'john do',
                        'Réligieu' => 'religieu',
                        'Client' => 'client',
                    ],
                    'label'=>'Type de Client'
            ])
            ->add('email')
            ->add('pays_residence',null,[
                'label'=>'Pays de résidence'
            ])
            ->add('nationalite')
            ->add('lieu_de_naissance',null,[
                'label'=>'Lieu de Naissance'
            ])
            ->add('profession')
            ->add('cni_made_at', DateType::class,[
                'label'=>'Date CNI',
                'widget'=>'single_text'
            ])
            ->add('born_at', DateType::class,[
                'label'=>'Date de Naissance',
                'widget'=>'single_text'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Client::class,
        ]);
    }
}
