<?php

namespace Esimed\CarLocation\BackendBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('categorie', null, array(
             'label' => 'Catégorie'))
            ->add('marque')
            ->add('modele', 'text', array(
                'label' => 'Modéle'))
            ->add('nbAnneePermis', null, array(
                'label' => 'Nb d\'année de permis requis'))
            ->add('ageMinimum', null, array(
                'label' => 'Age minimum requis'))
            ->add('moteur', 'choice', array(
                'choices' => array(
                    'Essence' => 'Essence', 'Diesel' => 'Diesel', 'Hybride' => 'Hybride', 'Tout électrique' => 'Tout électrique')
            ))
            ->add('boiteVitesse', 'choice', array(
                'label' => 'Boite à vitesse',
                'choices' => array("Automatique" => 'Automatique', "Manuelle" => 'Manuelle', "Séquentielle" => 'Séquentielle')
            ))
            ->add('nbPorte', 'text', array(
                'label' => 'Nombre de porte'))
            ->add('nbPassager', 'text', array(
                'label' => 'Nombre de passager/bagages'))
            ->add('climatisation', 'choice', array(
                'choices' => array("oui" => 'oui', "non" => 'non')
            ))
            ->add('equipement', 'choice', array(
                'required' => false,
                'label' => 'Equipement audio',
                'choices' => array("radio K7" => 'Radio K7', "CD" => 'CD', "MP3" => 'MP3')
            ))
            ->add('directionAssistee', 'choice', array(
                'label' => 'Direction assisté',
                'choices' => array("oui" => 'oui', "non" => 'non')
            ))
        ;
    }

    public function getName()
    {
        return 'esimed_carlocation_backendbundle_voituretype';
    }
}
