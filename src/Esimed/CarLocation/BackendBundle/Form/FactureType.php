<?php

namespace Esimed\CarLocation\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('dateArriveEffectue', null , array(
            'date_widget' => 'single_text',
            'input' => 'datetime',
            'date_format' => 'dd/MM/yyyy',
            'attr' => array('class' => 'date'),
            'label' => 'Date d\'arrivée'))
            ->add('kmEffectue', null , array('label' => 'Kilométre effectué'))
        ;
    }

    public function getName()
    {
        return 'esimed_carlocation_backendbundle_facturetype';
    }
}
