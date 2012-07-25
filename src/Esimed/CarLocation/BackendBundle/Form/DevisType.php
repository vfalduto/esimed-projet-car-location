<?php

namespace Esimed\CarLocation\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class DevisType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('prixTotal')
            ->add('kmTotal')
            ->add('created')
            ->add('updated')
            ->add('location')
        ;
    }

    public function getName()
    {
        return 'esimed_carlocation_backendbundle_devistype';
    }
}
