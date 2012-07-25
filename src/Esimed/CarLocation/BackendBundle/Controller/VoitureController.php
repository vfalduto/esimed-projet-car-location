<?php

namespace Esimed\CarLocation\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Esimed\CarLocation\BackendBundle\Entity\Voiture;
use Esimed\CarLocation\BackendBundle\Form\VoitureType;
use Esimed\CarLocation\BackendBundle\Form\VoitureSearchType;

/**
 * Voiture controller.
 *
 * @Route("/voiture")
 */
class VoitureController extends Controller
{
    /**
     * Lists all Voiture entities.
     * @Route("/", name="voiture")
     * @Template()
     */
    public function indexAction() {
        $agent = $this->get('security.context')->getToken()->getUser();


        $em = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')
            ->loadDispoForAgence($agent->getAgence())
            ->getQuery()
            ->getResult();

        $searchForm = $this->createForm(new VoitureSearchType($em));

        return array(
            'entities' => $entities,
            'search_form' => $searchForm->createView()
        );
    }

    /**
     * Liste les voitures filtré avec le formulaire de recherhcd
     *
     * @Route("/search", name="voiture_search")
     * @Template()
     */
    public function searchAction() {
        //retrieve entityManager and connected user
        $em = $this->getDoctrine()->getEntityManager();
        $agent = $this->get('security.context')->getToken()->getUser();


        $form = $this->createForm(new VoitureSearchType($em));
        $form->bindRequest($this->getRequest());

        $entities = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')
            ->search($agent->getAgence(), $form->getData())->getQuery()->getResult();

        //json encoder
        $i = 0;
        $data = array();
        foreach($entities as $entity) {
            $data[$i] = $entity->toArray(array(
                'modele', 'marque', 'id', 'disponible', array('getType', 'categorie'), 'localisation'
            ));
            $data[$i]['path_show'] = $this->generateUrl('voiture_show', array('id' => $entity->getId()));
            $data[$i]['path_edit'] = $this->generateUrl('voiture_edit', array('id' => $entity->getId()));
            $data[$i]['nbPorte'] = $entity->getNbPorte() . ' / ' . $entity->getNbPassager();
                $i++;
        }

        return new \Symfony\Component\HttpFoundation\Response(json_encode($data));
    }

    /**
     * Finds and displays a Voiture entity.
     *
     * @Route("/{id}/show", name="voiture_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Voiture entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $entity->canBeDeleted($this->get('security.context')->getToken()->getUser()) ? $deleteForm->createView() : null
        );
    }

    /**
     * Displays a form to create a new Voiture entity.
     *
     * @Route("/new", name="voiture_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Voiture();
        $form   = $this->createForm(new VoitureType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Voiture entity.
     *
     * @Route("/create", name="voiture_create")
     * @Method("post")
     * @Template("EsimedCarLocationBackendBundle:Voiture:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Voiture();
        $request = $this->getRequest();
        $form    = $this->createForm(new VoitureType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();

            $agent = $this->get('security.context')->getToken()->getUser();

            $entity
                ->setAgence($agent->getAgence())
                ->setStationneAgence($agent->getAgence());

            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('success',"Le véhicule a bien été ajouté");
            return $this->redirect($this->generateUrl('voiture_show', array('id' => $entity->getId())));
            
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Voiture entity.
     *
     * @Route("/{id}/edit", name="voiture_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Voiture entity.');
        }

        $editForm = $this->createForm(new VoitureType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $entity->canBeDeleted($this->get('security.context')->getToken()->getUser()) ? $deleteForm->createView() : null,
        );
    }

    /**
     * Edits an existing Voiture entity.
     *
     * @Route("/{id}/update", name="voiture_update")
     * @Method("post")
     * @Template("EsimedCarLocationBackendBundle:Voiture:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Voiture entity.');
        }

        $editForm   = $this->createForm(new VoitureType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('success',"Le véhicule a bien été modifié");
            return $this->redirect($this->generateUrl('voiture_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $entity->canBeDeleted($this->get('security.context')->getToken()->getUser()) ? $deleteForm->createView() : null,
        );
    }

    /**
     * Deletes a Voiture entity.
     *
     * @Route("/{id}/delete", name="voiture_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Voiture entity.');
            }

            //set flage supprimee on
            if ($entity->canBeDeleted($this->get('security.context')->getToken()->getUser())) {

                $entity->setStationneAgence(null);
                $entity->setSupprimee(true);

                $em->persist($entity);
                $em->flush();

                $this->get('session')->setFlash('success',"Le véhicule : " . $entity . ' a bien été supprimé');
            } else {
                $this->get('session')->setFlash(
                    'error',"Le véhicule : " .
                    entity .
                    'ne peux pas être supprimé, vérifié qu\il n\'y a pas de location en cours pour ce véhicule'
                );
            }

        }


        return $this->redirect($this->generateUrl('voiture'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
