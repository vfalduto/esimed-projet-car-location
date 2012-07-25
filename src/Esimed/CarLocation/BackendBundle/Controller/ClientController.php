<?php

namespace Esimed\CarLocation\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Esimed\CarLocation\BackendBundle\Entity\Client;
use Esimed\CarLocation\BackendBundle\Form\ClientType;

/**
 * Client controller.
 *
 * @Route("/client")
 */
class ClientController extends Controller
{
    /**
     * Lists all Client entities.
     *
     * @Route("/", name="client")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entities = $em->getRepository('EsimedCarLocationBackendBundle:Client')->findAll();

        return array('entities' => $entities);
    }

    /**
     * Finds and displays a Client entity.
     *
     * @Route("/{id}/show", name="client_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'encours'       => $em->getRepository('EsimedCarLocationBackendBundle:Location')->loadEnCours($entity),
            'futur'         => $em->getRepository('EsimedCarLocationBackendBundle:Location')->loadFutur($entity),
            'termine'       => $em->getRepository('EsimedCarLocationBackendBundle:Location')->loadTermine($entity),
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView()
    );
    }

    /**
     * Displays a form to create a new Client entity.
     *
     * @Route("/new", name="client_new")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Client();
        $form   = $this->createForm(new ClientType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a new Client entity.
     *
     * @Route("/create", name="client_create")
     * @Method("post")
     * @Template("EsimedCarLocationBackendBundle:Client:new.html.twig")
     */
    public function createAction()
    {
        $entity  = new Client();
        $request = $this->getRequest();
        $form    = $this->createForm(new ClientType(), $entity);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('client_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Displays a form to edit an existing Client entity.
     *
     * @Route("/{id}/edit", name="client_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm = $this->createForm(new ClientType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Client entity.
     *
     * @Route("/{id}/update", name="client_update")
     * @Method("post")
     * @Template("EsimedCarLocationBackendBundle:Client:edit.html.twig")
     */
    public function updateAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:Client')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Client entity.');
        }

        $editForm   = $this->createForm(new ClientType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        $request = $this->getRequest();

        $editForm->bindRequest($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            $this->get('session')->setFlash('success',"Le client a bien été modifié");
            return $this->redirect($this->generateUrl('client_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Client entity.
     *
     * @Route("/{id}/delete", name="client_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('EsimedCarLocationBackendBundle:Client')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Client entity.');
            }

            if (!count($entity->getLocation())) {
                $this->get('session')->setFlash('success',"Le client a bien été supprimé");
                $em->remove($entity);
                $em->flush();
            } else {
                $this->get('session')->setFlash('error',"Impossible de supprimer un client");
            }

        }

        return $this->redirect($this->generateUrl('client'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
