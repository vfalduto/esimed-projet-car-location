<?php

namespace Esimed\CarLocation\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ClientType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prenom', 'text', array(
                'label' => 'Prénom'
            ))
            ->add('dateNaissance', 'birthday', array(
                'label'  => 'Date de naissance',
            ))
            ->add('datePermis', 'birthday', array(
                'label'  => 'Date d\'obtention du permis',
                'required' => true
            ))
        ;
    }

    public function getName()
    {
        return 'esimed_carlocation_backendbundle_clienttype';
    }
}
