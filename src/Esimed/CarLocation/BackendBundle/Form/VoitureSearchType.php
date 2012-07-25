<?php

namespace Esimed\CarLocation\BackendBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;

use Esimed\CarLocation\BackendBundle\Entity\Categorie;


class VoitureSearchType extends AbstractType
{
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $manager = null)
    {
        $this->em = $manager;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {

        $builder
     /*       ->add('type', 'choice', array(
                'choices'   => array(
                    'tourisme'   => Categorie::$TYPE_TOURISME,
                    'utilitaire' => Categorie::$TYPE_UTILITAIRE,
                ),
                'multiple'  => true,
                'expanded' => true,
                'widget_type'  => "inline",
                'required' => false
            )) */
            ->add('categorieUtilitaire', 'entity', array(
                    'class' => 'EsimedCarLocationBackendBundle:Categorie',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('c')->where("c.type = :util")->setParameter('util', Categorie::$TYPE_UTILITAIRE);
                    },
                    'property' => 'nom',
                    'multiple'  => true,
                    'expanded' => true,
                    'required' => false,
                    'label' => 'Type Utilitaire'
            ))
            ->add('categorieTourisme', 'entity', array(
                'class' => 'EsimedCarLocationBackendBundle:Categorie',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')->where("c.type = :tourisme")->setParameter('tourisme', Categorie::$TYPE_TOURISME);
                },
                'property' => 'nom',
                'multiple'  => true,
                'expanded' => true,
                'required' => false,
                'label' => 'Type Tourisme'
            ))
            ->add('marque', 'choice', array(
                'choices' =>  $this->em->getRepository('EsimedCarLocationBackendBundle:Voiture')->loadMarques(),
                'multiple'  => true,
                'expanded' => false,
                'required' => false,
            ))
            ->add('moteur', 'choice', array(
                'required' => false,
                'multiple'  => true,
                'expanded' => true,
                'choices' => array(
                    'Essence' => 'Essence', 'Diesel' => 'Diesel', 'Hybride' => 'Hybride', 'Tout électrique' => 'Tout électrique')
            ))
            ->add('boiteVitesse', 'choice', array(
                'required' => false,
                'label' => 'Boite à vitesse',
                'multiple'  => true,
                'expanded' => true,
                'choices' => array("Automatique" => 'Automatique', "Manuelle" => 'Manuelle', "Séquentielle" => 'Séquentielle')
            ))
            ->add('climatisation', 'choice', array(
                'required' => false,
                'empty_value' => true,
                'multiple'  => false,
                'expanded' => true,
                'choices' => array("oui" => 'oui', "non" => 'non'),
                'widget_type'  => "inline",
            ))
            ->add('equipement', 'choice', array(
                'required' => false,
                'label' => 'Equipement audio',
                'choices' => array("radio K7" => 'Radio K7', "CD" => 'CD', "MP3" => 'MP3'),
                'multiple'  => true,
                'expanded' => true,
            ))
            ->add('directionAssistee', 'choice', array(
                'required' => false,
                'label' => 'Direction assisté',
                'multiple'  => false,
                'expanded' => true,
                'choices' => array("oui" => 'oui', "non" => 'non'),
                'widget_type'  => "inline",
            ))
            ->add('disponible', 'choice', array(
                'required' => false,
                'label' => 'Disponible',
                'multiple'  => false,
                'expanded' => true,
                'choices' => array(1 => 'oui', 0 => 'non'),
                'widget_type'  => "inline",
            ))
        ;
    }

    public function getName()
    {
        return 'esimed_carlocation_backendbundle_voiture_search_type';
    }
}
