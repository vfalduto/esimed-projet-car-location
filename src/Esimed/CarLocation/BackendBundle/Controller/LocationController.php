<?php

namespace Esimed\CarLocation\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Esimed\CarLocation\BackendBundle\Entity\Location;
use Esimed\CarLocation\BackendBundle\Form\LocationType;
use Esimed\CarLocation\BackendBundle\Form\LocationEditType;

/**
 * Location controller.
 *
 * @Route("/location")
 */
class LocationController extends Controller
{
    /**
     * Lists all Location entities.
     *
     * @Route("/", name="location")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('EsimedCarLocationBackendBundle:Location')->loadAllUnArchive();
        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Location entity.
     *
     * @Route("/{id}/show", name="location_show")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Location')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver la location.');
        }

        $deleteForm = $this->createDeleteForm($id);
        return array(
            'entity'        => $entity,
            'delete_form'   => $deleteForm->createView(),
        );
    }

    /**
     * Liste les voitures filtré
     *
     * @Route("/filter", name="location_filter")
     * @Template()
     */
    public function filterAction() {
        //retrieve entityManager and connected user
        $em = $this->getDoctrine()->getEntityManager();

        //recuperation des vehicules possible pour cette location
        $location = new Location($em);
        $form = $this->createForm(new LocationType($em), $location);
        $form->bindRequest($this->getRequest());

        $entities = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->filter($location);

        //json encoder
        $i = 0;
        $data = array();
        foreach($entities as $entity) {
            $data[$i] = $entity->toArray(array(
                'modele', 'marque', 'id', 'disponible', 'categorie'
            ));
            $data[$i]['localisation'] = $entity->getLocalisation($location->getDateDepart());
            $data[$i]['path_show'] = $this->generateUrl('voiture_show', array('id' => $entity->getId()));
            $i++;
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($data));
    }

    /**
     * Displays a form to create a new Location entity.
     *
     * @Route("/new", name="location_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Location();
        $form   = $this->createForm(new LocationType($this->getDoctrine()->getEntityManager()), $entity);

        $em = $this->getDoctrine()->getEntityManager();
        $voitures = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->findAll();

        return array(
            'entity'  => $entity,
            'form'    => $form->createView(),
        );
    }

    /**
     * Creates a new Location entity.
     *
     * @Route("/create/voiture/{voiture}", name="location_create_b")
     * @Route("/create", name="location_create")
     * @Method("post")
     * @Template("EsimedCarLocationBackendBundle:Location:new.html.twig")
     */
    public function createAction($voiture)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $entity  = new Location($em);

        //selection de la voiture
        $v = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->find($voiture);
        if (!$v) { throw $this->createNotFoundException('Voiure can be set.' . $v);}

        $entity->setVoiture($v);

        $request = $this->getRequest();
        $form    = $this->createForm(new LocationType($em), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('location_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Location entity.
     *
     * @Route("/{id}/edit", name="location_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Location')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver la location');
        } elseif ($entity->getEtat() != Location::$ETAT_RESERVATION) {
            $this->get('session')->setFlash('error',"La location ne peux plus être modifié");
            $this->redirect('location_show', array('id' => $entity->getId()));
        }

        $editForm = $this->createForm(new LocationEditType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing Location entity.
     *
     * @Route("/{id}/update/voiture/{voiture}", name="location_update_b")
     * @Route("/{id}/update", name="location_update")
     * @Method("post")
     * @Template("EsimedCarLocationBackendBundle:Location:edit.html.twig")
     */
    public function updateAction($id, $voiture)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Location')->find($id);

        // Vérification de la location
        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver la location');
        } elseif ($entity->getEtat() != Location::$ETAT_RESERVATION) {
            $this->get('session')->setFlash('error',"La location ne peux plus être modifié");
            $this->redirect('location_show', array('id' => $entity->getId()));
        }

        // Vérification du véhicule
        $voiture = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->find($voiture);
        if (!$voiture) {
            throw $this->createNotFoundException('Voiture inexistante');
        }
        $entity->setVoiture($voiture);

        //bind des données
        $entity->setEntityManager($em);
        $editForm   = $this->createForm(new LocationEditType(), $entity);
        $request = $this->getRequest();
        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('success',"La location a bien été modifié");
            return $this->redirect($this->generateUrl('location_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView()
        );
    }

    /**
     * Deletes a Location entity.
     *
     * @Route("/{id}/delete", name="location_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('EsimedCarLocationBackendBundle:Location')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Impossible de trouver la location.');
            }

            //vérifie si on peux supprimer la location (dans le cas d'une reservation.
            if ($entity->isCanBeDeleted()) {
                $em->remove($entity);
                $em->flush();
            } else {
                $this->get('session')->setFlash('error',"Cette location ne peux être supprimée");
            }
        }

        return $this->redirect($this->generateUrl('location'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

    /**
     * @param $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Route("/{id}/cloture", name="location_cloture")
     * @Method("get")
     */
    public function clotureAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $location = $em->getRepository('EsimedCarLocationBackendBundle:Location')->find($id);

        if (!$location) {
            throw $this->createNotFoundException('Impossible de trouver la location.');
        }
        $location->setEntityManager($em);

        if ($location->isCanBeCloture()) {
            $location->setEtat(Location::$ETAT_CLOTURE);
            $em->persist($location);
            $em->flush();
        } else {
            $this->get('session')->setFlash('error',"Cette location ne peux être cloturé");
        }
        return $this->redirect($this->generateUrl('location_show', array('id'=> $location->getId())));
    }

    /**
     * @param $id
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @Route("/{id}/archive", name="location_archive")
     * @Method("get")
     */
    public function archiveAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $location = $em->getRepository('EsimedCarLocationBackendBundle:Location')->find($id);

        if (!$location) {
            throw $this->createNotFoundException('Impossible de trouver la location.');
        }
        $location->setEntityManager($em);

        if ($location->isCanBeArchive()) {
            $location->setEtat(Location::$ETAT_ARCHIVE);
            $em->persist($location);
            $em->flush();
            $this->get('session')->setFlash('success',"La location " . $location .  " à bien été archivé");
        } else {
            $this->get('session')->setFlash('error',"Cette location ne peux être archivé");
        }
        return $this->redirect($this->generateUrl('location'));
    }

    /**
     * Displays a form to create a new Facture entity.
     *
     * @Route("/devis/{devis}/select", name="devis_select")
     * @Method("get")
     */
    public function devisSelectAction($devis) {
        $em = $this->getDoctrine()->getEntityManager();
        $devis = $em->getRepository('EsimedCarLocationBackendBundle:Devis')->find($devis);

        if (!$devis) {
            throw $this->createNotFoundException('Impossible de trouver le devis.');
        }  elseif (count($devis->getLocation()->getFacture()) != null) {
            $this->get('session')->setFlash('error',"On ne peux pas générer une seconde facture pour une location");
            return $this->redirect($this->generateUrl('location_show', array('id' => $devis->getLocation()->getId())));
        }

        //create empty facture
        $facture = new \Esimed\CarLocation\BackendBundle\Entity\Facture();
        $facture
            ->setDevis($devis)
            ->setLocation($devis->getLocation())
            ->setKmEffectue(0);
        $em->persist($facture);

        //change state
        $devis->getLocation()->setEtat(Location::$ETAT_DEVIS_VALIDE);
        $em->persist($devis->getLocation());
        $em->flush();

        return $this->redirect($this->generateUrl('location_show', array('id'=> $devis->getLocation()->getId())));
    }
}
