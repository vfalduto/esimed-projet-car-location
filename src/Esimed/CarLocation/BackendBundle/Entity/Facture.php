<?php

namespace Esimed\CarLocation\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * Esimed\CarLocation\BackendBundle\Entity\Location
 *
 * @ORM\Table(name="facture")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 *
 * @Assert\Callback(methods={"isDateValid"})
 */
class Facture
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var datetime $dateArriveEffectue
     *
     * @ORM\Column(name="date_arrive_effectue", type="datetime", nullable=true)
     */
    private $dateArriveEffectue;

    /**
     * @var integer $kmEffectue
     * @Assert\Min(limit = "0", message = "veuillez ne pas mentir sur le nombre de kilométres")
     * @ORM\Column(name="km_effectue", type="integer", nullable=false)
     */
    private $kmEffectue;

    /**
     * @var float $prixTotal
     *
     * @ORM\Column(name="prix_total", type="float", nullable=false)
     */
    private $prixTotal;

    /**
     * @var Devis
     *
     * @ORM\ManyToOne(targetEntity="Devis")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="devis_id", referencedColumnName="id")
     * })
     */
    private $devis;

    /**
     * @var Location
     * @ORM\OneToOne(targetEntity="Location", inversedBy="facture")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     *
     */
    private $location;

    public function __toString() {
        return "Facture numéro " . $this->getId() . ' devis : ' . $this->getDevis()->getId();
    }

    /**
     * Set dateArriveEffectue
     *
     * @param datetime $dateArriveEffectue
     * @return Location
     */
    public function setDateArriveEffectue($dateArriveEffectue)
    {
        $this->dateArriveEffectue = $dateArriveEffectue;
        return $this;
    }

    /**
     * Get dateArriveEffectue
     *
     * @return datetime
     */
    public function getDateArriveEffectue()
    {
        return $this->dateArriveEffectue;
    }

    /**
     * Set kmEffectue
     *
     * @param integer $kmEffectue
     * @return Location
     */
    public function setKmEffectue($kmEffectue)
    {
        $this->kmEffectue = $kmEffectue;
        return $this;
    }

    /**
     * Get kmEffectue
     *
     * @return integer
     */
    public function getKmEffectue()
    {
        return $this->kmEffectue;
    }

    /**
     * Set prixTotal
     *
     * @param float $prixTotal
     * @return Location
     */
    public function setPrixTotal($prixTotal)
    {
        $this->prixTotal = $prixTotal;
        return $this;
    }

    /**
     * Get prixTotal
     *
     * @return float
     */
    public function getPrixTotal()
    {
        return round($this->prixTotal, 2);
    }

    /**
     * Set devis
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Devis $devis
     * @return Location
     */
    public function setFacture(\Esimed\CarLocation\BackendBundle\Entity\Devis $devis = null)
    {
        $this->devis = $devis;
        return $this;
    }

    /**
     * Get devis
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Devis
     */
    public function getDevis()
    {
        return $this->devis;
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
     * Set devis
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Devis $devis
     * @return Facture
     */
    public function setDevis(\Esimed\CarLocation\BackendBundle\Entity\Devis $devis = null)
    {
        $this->devis = $devis;
        return $this;
    }

    /**
     * Set location
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Location $location
     * @return Facture
     */
    public function setLocation(\Esimed\CarLocation\BackendBundle\Entity\Location $location = null)
    {
        $this->location = $location;
        return $this;
    }

    /**
     * Get location
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Location 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * renvoie vrai si il y a un dépassement
     * @return bool
     */
    public function isDepassement() {
        return $this->getKmEffectue() > $this->getDevis()->getKmTotal();
    }


    /**
     * calcul le prix de la facture prend en compte le dépassement
     * @ORM\prePersist
     * @ORM\preUpdate
     */
    public function calculPrixAvecDepassement() {
        $this->setPrixTotal(
            $this->getDevis()->getPrixTotal() + $this->getDevis()->penalite($this->getKmEffectue()));
    }

    /**
     * vérification de la date
     * @param \Symfony\Component\Validator\ExecutionContext $context
     * @return bool
     */
    public function isDateValid(ExecutionContext $context) {
        if ($this->getDateArriveEffectue() < $this->getDevis()->getLocation()->getDateDepart()) {
            $context->addViolation(
                'La date d\'arrivée doit au moins être égal à la date de départ',
                array(),
                null
            );
        }
    }
}