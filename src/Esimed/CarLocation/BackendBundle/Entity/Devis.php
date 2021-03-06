<?php

namespace Esimed\CarLocation\BackendBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;

/**
 * Esimed\CarLocation\BackendBundle\Entity\Devis
 *
 * @ORM\Table(name="devis")
 * @ORM\Entity
 */
class Devis extends EntityArray
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
     * @var float $prixTotal
     *
     * @ORM\Column(name="prix_total", type="float", nullable=false)
     */
    private $prixTotal;

    /**
     * @var integer $kmTotal
     *
     * @ORM\Column(name="km_total", type="integer", nullable=true)
     */
    private $kmTotal;

    /**
     * @var Location
     *
     * @ORM\ManyToOne(targetEntity="Location")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="location_id", referencedColumnName="id")
     * })
     */
    private $location;

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
     * @var LignesDevis
     * @ORM\OneToMany(targetEntity="LigneDevis", mappedBy="devis")
     *
     */
    private $lignesDevis;

    public function __toString() {
        return 'Devis : ' . $this->getId() . ' - ' . $this->getLocation();
    }

    public function __construct()
    {
        $this->lignesDevis = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set prixTotal
     *
     * @param float $prixTotal
     * @return Devis
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
        return $this->prixTotal;
    }

    /**
     * Set kmTotal
     *
     * @param integer $kmTotal
     * @return Devis
     */
    public function setKmTotal($kmTotal)
    {
        $this->kmTotal = $kmTotal;
        return $this;
    }

    /**
     * Get kmTotal
     *
     * @return integer 
     */
    public function getKmTotal()
    {
        return $this->kmTotal;
    }

    /**
     * Set location
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Location $location
     * @return Devis
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
     * Set created
     *
     * @param datetime $created
     * @return Devis
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
     * @return Devis
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

    /**
     * Add lignesDevis
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\LigneDevis $lignesDevis
     */
    public function addLigneDevis(\Esimed\CarLocation\BackendBundle\Entity\LigneDevis $lignesDevis)
    {
        $this->lignesDevis[] = $lignesDevis;
    }

    /**
     * Get lignesDevis
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getLignesDevis()
    {
        return $this->lignesDevis;
    }

    /**
     * Renvoie vrai si l'on peux supprimer le devis
     * @return bool
     */
    public function isCanBeDeleted() {
        return !$this->getLocation()->getFacture() || $this->getLocation()->getFacture()->getDevis() != $this;
    }

    /**
     * calcul les penalités par rapport au kilométre effectué
     * @return float
     */
    public function penalite($kmEffectue)  {
        if ($kmEffectue <= $this->getKmTotal()) {
           return 0;
        } else {
            $sup = $kmEffectue - $this->getKmTotal();
            return (($this->getPrixTotal() * $sup) / $this->getKmTotal()) * 1.25;
        }
    }
}