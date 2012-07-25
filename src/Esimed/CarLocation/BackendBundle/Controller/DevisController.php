<?php

namespace Esimed\CarLocation\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Esimed\CarLocation\BackendBundle\Entity\Devis;
use Esimed\CarLocation\BackendBundle\Form\DevisType;

/**
 * Devis controller.
 *
 * @Route("/devis")
 */
class DevisController extends Controller
{
    /**
     * Creates a new Devis entity.
     *
     * @Route("/create/location/{location}", name="devis_create")
     */
    public function createAction($location)
    {
        $em = $this->getDoctrine()->getManager();
        $location = $em->getRepository('EsimedCarLocationBackendBundle:Location')->find($location);

        if (!$location) {
            throw $this->createNotFoundException('Cette location n\'existe pas.' . $location);
        }

       if ($location->isCanBeCreateOrEditDevis()) {
           $entity = new Devis();
           $entity->setLocation($location);
           $entity->setKmTotal(0);
           $entity->setPrixTotal(0);

           $em->persist($entity);
           $em->flush();

           return $this->redirect($this->generateUrl(
               'devis_edit',
               array('id' => $entity->getId())
           ));
       } else {
            $this->get('session')->setFlash('error',"Impossible de créer un nouveau devis");
            return $this->redirect($this->generateUrl('location_show', array('id' => $location->getId())));
       }
    }

    /**
     * Editions d'un devis
     * @Route("/{id}/edit", name="devis_edit")
     * @Template()
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getEntityManager();

        $entity = $em->getRepository('EsimedCarLocationBackendBundle:devis')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver ce devis');
        }

        if ($entity->getLocation()->isCanBeCreateOrEditDevis()) {
            $forfaits = $em->getRepository('EsimedCarLocationBackendBundle:forfait')
                ->findBy(
                array('categorie' => $entity->getLocation()->getVoiture()->getCategorie()));

            $deleteForm = $this->createDeleteForm($id);


            return array(
                'entity' => $entity,
                'forfaits' => $forfaits,
                'delete_form' => $deleteForm->createView(),
            );
        } else {
            $this->get('session')->setFlash('error',"Impossible de modifier ce devis");
            return $this->redirect($this->generateUrl('location_show', array('id' => $entity->getLocation()->getId())));
        }
    }

    /**
     * Valide devis
     * @Route("/{id}/valid", name="devis_retour")
     * @Template()
     */
    public function validAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $entity = $em->getRepository('EsimedCarLocationBackendBundle:devis')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Impossible de trouver ce devis');
        }

        if (count($entity->getLignesDevis()) == 0) {
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('location_show', array('id' => $entity->getLocation()->getId())));
    }

    /**
     * Ajout d'une ligne au devis
     *
     * @Route("/{id}/addLine/{forfait}", name="devis_add_line")
     */
    public function addLigneAction($id, $forfait) {
        $em = $this->getDoctrine()->getEntityManager();
        $devis = $em->getRepository('EsimedCarLocationBackendBundle:devis')->find($id);
        $forfait = $em->getRepository('EsimedCarLocationBackendBundle:forfait')->find($forfait);

        if (!$devis || !$forfait) {
            throw $this->createNotFoundException('Impossible d\'ajouter une ligne au devis');
        }

        $ligneDevis = new \Esimed\CarLocation\BackendBundle\Entity\LigneDevis();
        $ligneDevis
            ->setCategorie($forfait->getCategorie())
            ->setKmMax($forfait->getKmMax())
            ->setPeriode($forfait->getPeriode())
            ->setPrix($forfait->getPrix())
            ->setDevis($devis);

        $em->persist($ligneDevis);

        $devis->setPrixTotal($devis->getPrixTotal() + $forfait->getPrix());
        $devis->setKmTotal($devis->getKmTotal() + $forfait->getKmMax());

        $em->persist($devis);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\Response(json_encode(array(
            'ligneDevis' => $ligneDevis->toArray(array('id', 'periode', 'prix', 'kmMax')),
            'removeLignesDevisLink' => $this->generateUrl(
                'devis_remove_line',
                array(
                    'id' => $devis->getId(),
                    'ligneDevis' => $ligneDevis->getId()
                )),
            'devis' => $devis->ToArray(array('id', 'prixTotal', 'kmTotal'))
        )));
    }

    /**
     * Suppression d'une ligne du devis
     * @Route("/{id}/removeline/{ligneDevis}", name="devis_remove_line")
     */
    public function removeLigneAction($id, $ligneDevis) {
        $em = $this->getDoctrine()->getEntityManager();

        $devis = $em->getRepository('EsimedCarLocationBackendBundle:devis')->find($id);
        $ligneDevis = $em->getRepository('EsimedCarLocationBackendBundle:ligneDevis')->find($ligneDevis);

        if (!$devis || !$ligneDevis) {
            throw $this->createNotFoundException('Impossible de supprimer une ligne au devis');
        }

        $devis->setPrixTotal($devis->getPrixTotal() - $ligneDevis->getPrix());
        $devis->setKmTotal($devis->getKmTotal() - $ligneDevis->getKmMax());

        $em->persist($devis);

        $em->remove($ligneDevis);
        $em->flush();

        return new \Symfony\Component\HttpFoundation\Response(json_encode(
            array('devis' => $devis->ToArray(array('id', 'prixTotal', 'kmTotal')))
        ));
    }

    /**
     * Suppression d'un devis
     *
     * @Route("/{id}/delete", name="devis_delete")
     * @Method("post")
     */
    public function deleteAction($id)
    {
        $form = $this->createDeleteForm($id);
        $request = $this->getRequest();

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('EsimedCarLocationBackendBundle:devis')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Location entity.');
            }

            if ($entity->isCanBeDeleted()) {
                foreach ($entity->getLignesDevis() as $ligne) {
                    $em->remove($ligne);
                }
                $em->remove($entity);
                $em->flush();

            } else {
                $this->get('session')->setFlash('error',"Ce devis ne peux être supprimée");
            }
        }

        return $this->redirect($this->generateUrl('location_show', array('id' => $entity->getLocation()->getId())));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
            ;
    }
}
