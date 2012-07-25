<?php

namespace Esimed\CarLocation\BackendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\Response;

/**
 * API controller.
 *
 * @Route("/api")
 */
class ApiController extends Controller {

    /**
     * @Method("GET")
     * @Route("/version", name="api_version", defaults={"_format" = "json"},  requirements={"_format"="json"})
     */
    public function getVersionAction(){
        return $this->successResponse(array(
            "version" => "1.0",
            "api" => "API - Location de voiture - Esimed",
            "author" => "Vincent Falduto"
        ));
    }

    /****************************************************************************************************************/
    /* location */
    /****************************************************************************************************************/

    /**
     * renvoie la liste des agences
     * @Method("GET")
     * @Route("/agences", name="api_agences_list", defaults={"_format" = "json"},  requirements={"_format"="json"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getAgencesAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $entities = $em->getRepository('EsimedCarLocationBackendBundle:Agence')->findAll();

        $responses = array();
        foreach($entities as $agence) {
            $responses[] = $agence->toArray(array(
                'id', 'nom', 'ville'));
        }
        return $this->successResponse($responses);
    }

    /**
     * renvoie les vehicules disponible pour une location
     * @Method("GET")
     * @Route("/voitures", name="api_voitures_list", defaults={"_format" = "json"},  requirements={"_format"="json"})
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function getVoituresAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        //vérification de la location
        $location = new \Esimed\CarLocation\BackendBundle\Entity\Location();
        $response = $this->validateLocation($request, $em, $location);
        if ($response !== true) {
            return $response;
        }

        //filtrer les voitures
        $entities = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->filter($location);

        $responses = array();
        foreach($entities as $voiture) {
            $responses[] = $voiture->toArray(array(
                'id', 'modele', 'marque', 'boiteVitesse', 'nbPorte', 'nbPassager', 'climatisation',
                'equipement', 'moteur', 'directionAssistee', 'nbAnneePermis', 'ageMinimum', 'categorie',
                array('getId', 'agence'), array('getId', 'stationneAgence'),
            ));
        }

        return $this->successResponse($responses);
    }

    /**
     * @Method("POST")
     * @Route("/location", name="api_location", defaults={"_format" = "json"},  requirements={"_format"="json"})
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    public function postLocationAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();

        //vérification du client
        $client = new \Esimed\CarLocation\BackendBundle\Entity\Client();
        $response = $this->validateClient($request, $em, $client);
        if ($response !== true) {
            return $response;
        }

        //vérification de la location
        $location = new \Esimed\CarLocation\BackendBundle\Entity\Location();
        $response =  $this->validateLocation($request, $em, $location);
        if ($response !== true) {
            return $response;
        }

        //vérification de la voiture
        $voiture = new \Esimed\CarLocation\BackendBundle\Entity\Voiture();
        $response = $this->validateVoitureId($request, $em, $voiture);
        if ($response !== true) {
            return $response;
        }

        //sauvegarde
        $location->setClient($client);
        $location->setVoiture($voiture);

        try {
            //sauvegarde des champs
            $em->persist($location);
            $em->flush();

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), $e->getCode()) ;
        }


        return $this->successResponse($location->toArray(
            array("id"))) ;
    }

    /****************************************************************************************************************/
    /* refactor */
    /****************************************************************************************************************/

    /**
     * valide les paramêtres du client
     * @param $request
     * @param \Doctrine\ORM\EntityManager $em
     * @param $client
     * @return null|object|\Symfony\Component\HttpFoundation\Response
     */
    protected function validateClient($request, \Doctrine\ORM\EntityManager $em,
                                      \Esimed\CarLocation\BackendBundle\Entity\Client &$client) {

        if (!$request->get('nom', null)) {
            return $this->errorResponse("Il manque le parametre nom", 400);
        }

        if (!$request->get('prenom', null)) {
            return $this->errorResponse("Il manque le parametre prenom", 400);
        }

        //date fin
        if (!$request->get('dateNaissance', null)) {
            return $this->errorResponse("Il manque la date de naissance", 400);
        }
        $dateNaissance =new \DateTime();
        $dateNaissance->setTimestamp($request->get('dateNaissance'));

        //recherche si le client existe déja
        $clients = $em->getRepository('EsimedCarLocationBackendBundle:Client')->findBy(array(
            'prenom' => $request->get('nom'),
            'nom' => $request->get('prenom'),
            'dateNaissance' => $dateNaissance

        ));

        if (count($clients) == 1) {
            $client = reset($clients);
        }  else  {

            //si il n'existe pas on le crée
            $client
                ->setNom($request->get('nom'))
                ->setPrenom($request->get('prenom'))
                ->setDateNaissance($dateNaissance);

            $em->persist($client);
        }

        return true;
    }

    /**
     * valide les parametres d'une location
     * @param $request
     * @param \Doctrine\ORM\EntityManager $em
     * @param $location
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    protected function validateLocation($request, \Doctrine\ORM\EntityManager $em, &$location) {

        $location = new \Esimed\CarLocation\BackendBundle\Entity\Location();

        //vérification de chacun des champs

        if ($request->get('agenceDepart', null)) {
            $agence = $em->getRepository('EsimedCarLocationBackendBundle:Agence')->find($request->get('agenceDepart'));
            if (!$agence) {
                return $this->errorResponse("l'id de l'agence de départ est invalide", 400);
            }
        } else {
            return $this->errorResponse("agence de départ manquante", 400);
        }
        $location->setAgenceDepart($agence);

        if ($request->get('agenceArrivee', null)) {
            $agence = $em->getRepository('EsimedCarLocationBackendBundle:Agence')->find($request->get('agenceArrivee'));
            if (!$agence) {
                return $this->errorResponse("l'id de l'agence d'arrivée est invalide", 400);
            }
        } else {
            return $this->errorResponse("agence d'arrivée manquante", 400);
        }
        $location->setAgenceArrive($agence);

        //date depart
        if (!$request->get('dateDepart', null)) {
            return $this->errorResponse("Il manque la date de départ", 400);
        }
        $dateDeb = new \DateTime();
        $dateDeb->setTimestamp($request->get('dateDepart', null));
        $location->setDateDepart($dateDeb);

        //date fin
        if (!$request->get('dateArrivee', null)) {
            return $this->errorResponse("Il manque la date d'arrivée", 400);
        }
        $dateFin = new \DateTime();
        $dateFin->setTimestamp($request->get('dateArrivee', null));
        $location->setDateArrivee($dateFin);
                                                                            
        return true;
    }

    /**
     * valide l'id de la voiture
     * @param $request
     * @param \Doctrine\ORM\EntityManager $em
     * @param \Esimed\CarLocation\BackendBundle\Entity\Voiture $voiture
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    protected function validateVoitureId($request, \Doctrine\ORM\EntityManager $em,
                                        \Esimed\CarLocation\BackendBundle\Entity\Voiture & $voiture) {

        if ($request->get('voiture', null)) {
            $voiture = $em->getRepository('EsimedCarLocationBackendBundle:Voiture')->find($request->get('voiture'));
            if (!$voiture) {
                return $this->errorResponse("l'id de la voiture est invalide", 400);
            }
        } else {
            return $this->errorResponse("Il manque l'id de la voiture", 400);
        }
        return true;
    }


    /****************************************************************************************************************/
    /* helper */
    /****************************************************************************************************************/

    /**
     * Renvoie une erreur formatté pour être interprété par l'application mobile
     * @param $error
     * @param $code
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function errorResponse($error, $code) {
        return new Response(json_encode(
            array("error" => array("response" =>
                array("message" => $error, "status" => $code, "params" => $this->getRequest()->all()) )
        )), 201);
    }


    /**
     * Renvoie une reponse valide formatté pour être interprété par l'application mobile
     * @param $response
     * @internal param $error
     * @internal param $code
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function successResponse($response) {
        return new Response(json_encode(
            array("success" => array("response" => $response))
        ), 200);
    }

}
