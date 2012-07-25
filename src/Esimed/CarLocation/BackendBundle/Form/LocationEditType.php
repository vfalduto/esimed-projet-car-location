<?php

namespace Esimed\CarLocation\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;


class LocationEditType extends AbstractType
{

    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $manager = null)
    {
        $this->em = $manager;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('agenceDepart', null, array(
            'label' => 'Agence de départ'
        ))
            ->add('agenceArrive', null, array(
            'label' => 'Agence d\'arrivée'
        ))
            ->add('dateDepart', null, array(
            'date_widget' => 'single_text',
            'input' => 'datetime',
            'date_format' => 'dd/MM/yyyy',
            'attr' => array('class' => 'date'),
            'label' => 'Date de départ'
        ))
            ->add('dateArrivee', null, array(
            'date_widget' => 'single_text',
            'input' => 'datetime',
            'date_format' => 'dd/MM/yyyy',
            'attr' => array('class' => 'date'),
            'label' => 'Date d\'arrivée'
        ))
        ;
    }

    public function getName()
    {
        return 'esimed_carlocation_backendbundle_locationtype';
    }
}
