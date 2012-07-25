<?php

namespace Esimed\CarLocation\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Esimed\CarLocation\BackendBundle\Entity\Facture;
use Esimed\CarLocation\BackendBundle\Form\FactureType;

/**
 * Facture controller.
 *
 * @Route("/facture")
 */
class FactureController extends Controller
{
    /**
     * Displays a form to create a new Facture entity.
     *
     * @Route("/{facture}/edit", name="facture_edit")
     * @Template()
     */
    public function editAction($facture)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $facture = $em->getRepository('EsimedCarLocationBackendBundle:Facture')->find($facture);

        if (!$facture) {
            throw $this->createNotFoundException('Impossible de trouver la facture');
        }

        $form = $this->createForm(new FactureType(), $facture);

        return array(
            'entity' => $facture,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Facture entity.
     *
     * @Route("/{id}/update", name="facture_update")
     * @Method("post")
     * @Template("EsimedCarLocationBackendBundle:Facture:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $facture = $em->getRepository('EsimedCarLocationBackendBundle:Facture')->find($id);

        if (!$facture) {
            throw $this->createNotFoundException('Impossible de trouver la facture');
        } elseif ($facture->getLocation()->getEtat() != \Esimed\CarLocation\BackendBundle\Entity\Location::$ETAT_DEVIS_VALIDE) {
            $this->get('session')->setFlash('error',"On ne peux pas générer une seconde facture pour une location");
            return $this->redirect($this->generateUrl('location_show', array('id' => $facture->getLocation()->getId())));
        }

        $request = $this->getRequest();
        $form    = $this->createForm(new FactureType(), $facture);
        $form->bindRequest($request);


        if ($form->isValid() && $facture->getLocation()) {
            $em->persist($facture);
            $em->flush();

            $facture->getLocation()->setEntityManager($em);
            $facture->getLocation()->setEtat(\Esimed\CarLocation\BackendBundle\Entity\Location::$ETAT_FACTURE);
            $em->persist($facture->getLocation());
            $em->flush();


            $this->get('session')->setFlash('success',"Facture enregistré");
            return $this->redirect($this->generateUrl('location_show', array('id' => $facture->getLocation()->getId())));

        }

        return array(
            'entity' => $facture,
            'form'   => $form->createView()
        );
    }
}

