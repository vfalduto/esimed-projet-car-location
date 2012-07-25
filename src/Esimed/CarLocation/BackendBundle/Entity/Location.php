<?php

namespace Esimed\CarLocation\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * Esimed\CarLocation\BackendBundle\Entity\Location
 *
 * @ORM\Table(name="location")
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="LocationRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @Assert\Callback(methods={"isVehiculeValid"})
 * @Assert\Callback(methods={"isPeriodeValide"})
 *
 */
class Location extends EntityArray
{
    public static $ETAT_RESERVATION     = "réservée";
    public static $ETAT_DEVIS_VALIDE    = "devis validé";
    public static $ETAT_FACTURE         = "facturée";
    public static $ETAT_CLOTURE         = "cloturée";
    public static $ETAT_ARCHIVE         = "archivée";


    /**
     * @var EntityManager $em
     */
    private $em = null;

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var datetime $dateDepart
     * @Assert\NotNull(message="le champ date départ ne peux pas être vide")
     *
     * @ORM\Column(name="date_depart", type="datetime", nullable=false)
     */
    private $dateDepart;

    /**
     * @var datetime $dateArrivee
     * @Assert\NotNull(message="le champ date d'arrivée ne peux pas être vide")
     *
     * @ORM\Column(name="date_arrivee", type="datetime", nullable=false)
     */
    private $dateArrivee;

    /**
     * @var string $etat
     *
     * @ORM\Column(name="etat", type="string", nullable=true)
     */
    private $etat;

    /**
     * @var Agence
     *
     * @ORM\ManyToOne(targetEntity="Agence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agence_depart_id", referencedColumnName="id")
     * })
     */
    private $agenceDepart;

    /**
     * @var Agence
     *
     * @ORM\ManyToOne(targetEntity="Agence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agence_arrive_id", referencedColumnName="id")
     * })
     */
    private $agenceArrive;

    /**
     * @var Client
     *
     * @ORM\ManyToOne(targetEntity="Client")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="client_id", referencedColumnName="id")
     * })
     */
    private $client;

    /**
     * @var Voiture
     *
     * @ORM\ManyToOne(targetEntity="Voiture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="voiture_id", referencedColumnName="id")
     * })
     */
    private $voiture;

    /**
     * @var Devis
     * @ORM\OneToMany(targetEntity="Devis", mappedBy="location")
     *
     */
    private $devis;

    /**
     * @var facture
     * @ORM\OneToOne(targetEntity="Facture", mappedBy="location")
     *
     */
    private $facture;

    /**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;

    /**
     * @var datetime $updated
     *
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     *
     */
    public function __construct(\Doctrine\ORM\EntityManager $em = null)
    {
        $this->em = $em;
        $this->devis = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setEtat(self::$ETAT_RESERVATION);
    }

    public function setEntityManager(\Doctrine\ORM\EntityManager $em) {
        $this->em = $em;
    }

    public function __toString() {
        return "Location : " . $this->getClient() . ' ' . $this->getVoiture();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateDepart
     *
     * @param datetime $dateDepart
     * @return Location
     */
    public function setDateDepart($dateDepart)
    {
        $this->dateDepart = $dateDepart;
        return $this;
    }

    /**
     * Get dateDepart
     *
     * @return datetime 
     */
    public function getDateDepart()
    {
        return $this->dateDepart;
    }

    /**
     * Set dateArrivee
     *
     * @param datetime $dateArrivee
     * @return Location
     */
    public function setDateArrivee($dateArriveePrevu)
    {
        $this->dateArrivee = $dateArriveePrevu;
        return $this;
    }

    /**
     * Get dateArrivee
     *
     * @return datetime 
     */
    public function getDateArrivee()
    {
        return $this->dateArrivee;
    }


    /**
     * Set etat
     *
     * @param string $etat
     * @return Location
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
        return $this;
    }

    /**
     * Get etat
     *
     * @return string 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set agenceDepart
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Agence $agenceDepart
     * @return Location
     */
    public function setAgenceDepart(\Esimed\CarLocation\BackendBundle\Entity\Agence $agenceDepart = null)
    {
        $this->agenceDepart = $agenceDepart;
        return $this;
    }

    /**
     * Get agenceDepart
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Agence 
     */
    public function getAgenceDepart()
    {
        return $this->agenceDepart;
    }

    /**
     * Set agenceArrive
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Agence $agenceArrive
     * @return Location
     */
    public function setAgenceArrive(\Esimed\CarLocation\BackendBundle\Entity\Agence $agenceArrive = null)
    {
        $this->agenceArrive = $agenceArrive;
        return $this;
    }

    /**
     * Get agenceArrive
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Agence 
     */
    public function getAgenceArrive()
    {
        return $this->agenceArrive;
    }

    /**
     * Set client
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Client $client
     * @return Location
     */
    public function setClient(\Esimed\CarLocation\BackendBundle\Entity\Client $client = null)
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Get client
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Client 
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set voiture
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Voiture $voiture
     * @return Location
     */
    public function setVoiture(\Esimed\CarLocation\BackendBundle\Entity\Voiture $voiture = null)
    {
        $this->voiture = $voiture;
        return $this;
    }

    /**
     * Get voiture
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Voiture 
     */
    public function getVoiture()
    {
        return $this->voiture;
    }

    /**
     * Add devis
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Devis $devis
     */
    public function addDevis(\Esimed\CarLocation\BackendBundle\Entity\Devis $devis)
    {
        $this->devis[] = $devis;
    }

    /**
     * Get devis
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getDevis()
    {
        return $this->devis;
    }

    /**
     * Set facture
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Facture $facture
     * @return Location
     */
    public function setFacture(\Esimed\CarLocation\BackendBundle\Entity\Facture $facture = null)
    {
        $this->facture = $facture;
        return $this;
    }

    /**
     * Get facture
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Facture 
     */
    public function getFacture()
    {
        return $this->facture;
    }


    /**
     * Set created
     *
     * @param datetime $created
     * @return Location
     */
    public function setCreated($created)
    {
        $this->created = $created;
        return $this;
    }

    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     * @return Location
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * Get updated
     *
     * @return datetime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    public function setEntittyManager(\Doctrine\ORM\EntityManager $manager) {
        $this->em = $manager;
    }

    public function getDuree() {
       return $this->getDateDepart()->diff($this->getDateArrivee())->format('%d');
    }

    /************************************************************************************************************/

    /**
     * vérifie si le permis est valide
     * @param \Symfony\Component\Validator\ExecutionContext $context
     * @return bool
     */
    public function isPeriodeValide(ExecutionContext $context) {
        if ($this->getDateArrivee() < $this->getDateDepart()) {
            $context->addViolation(
                "Date de location invlide - Désolé nous ne louons pas de DeLorean DMC-12 :/",
                array(),
                null
            );
            return false;
        }
        return true;
    }

    /**
     * Renvoie true si la location peux être supprimée
     * @return bool
     */
    public function isCanBeDeleted() {
        return in_array($this->getEtat(), array(self::$ETAT_RESERVATION))
            && count($this->getDevis()) == 0;
    }

    /**
     * Renvoie true si la location peux être supprimée
     * @return bool
     */
    public function isCanBeModified() {
        return in_array($this->getEtat(), array(self::$ETAT_RESERVATION))
            && count($this->getDevis()) == 0
            && $this->getDateDepart() > new \DateTime();
    }

    /**
     * Renvoie true si on peux ajouter ou modifier un devis
     * @return bool
     */
    public function isCanBeCreateOrEditDevis() {
        return in_array($this->getEtat(), array(self::$ETAT_RESERVATION));
    }

    /**
     * renvoie vrai si l'on peux cloturé la location
     * une location cloturé en état de réservation est considéré comme annulé
     * @return bool
     */
    public function isCanBeCloture() {
        return in_array($this->getEtat(), array(self::$ETAT_DEVIS_VALIDE, self::$ETAT_FACTURE, self::$ETAT_RESERVATION));
    }

    /**
     * renvoie vraie si l'on peux archiver la location
     * @return bool
     */
    public function isCanBeArchive() {
        return in_array($this->getEtat(), array(self::$ETAT_CLOTURE));
    }

    /**
     * renvoie true si le client est valide pour une location
     * @return bool
     */
    public function isClientValid() {
        if ($this->getEtat() == self::$ETAT_RESERVATION) {
            return $this->getClient()->isComplete();
        }
        return true;
    }

    /**
     * renvoie vrai si la location peux être facturé
     * @return bool
     */
    public function isCanBeFacture() {
        return in_array($this->getEtat(), array(self::$ETAT_DEVIS_VALIDE));
    }

    /**
     * renvoie vrai si le véhicule est valide pour le client sélectionné
     * @return bool
     */
    public function isVehiculeValid() {
        if ($this->getEtat() == self::$ETAT_RESERVATION) {
            // vérification de l'age
            return
                $this->getClient()->isComplete()
                && $this->getVoiture()
                && $this->getClient()->getAge($this->getDateDepart()) > $this->getVoiture()->getAgeMinimum()
                && $this->getClient()->getAgePermis() >  $this->getVoiture()->getNbAnneePermis();
        }
        return true;
    }

    /**
     * calcul le prix de la facture prend en compte le dépassement
     * @ORM\postPersist
     */
    public function postPersist() {
    }

    /**
     * calcul le prix de la facture prend en compte le dépassement
     * @ORM\postUpdate
     */
    public function postUpdate() {
        if($this->em && $this->getEtat() == Location::$ETAT_FACTURE) {

            $this->getVoiture()->setStationneAgence($this->getAgenceArrive());
            $this->em->persist($this->getVoiture());
            $this->em->flush();
        }
    }


}