<?php

namespace Esimed\CarLocation\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Esimed\CarLocation\BackendBundle\Entity\LigneDevis
 *
 * @ORM\Table(name="ligne_devis")
 * @ORM\Entity
 */
class LigneDevis extends EntityArray
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
     * @var float $prix
     *
     * @ORM\Column(name="prix", type="float", nullable=false)
     */
    private $prix;

    /**
     * @var integer $kmMax
     *
     * @ORM\Column(name="km_max", type="integer", nullable=false)
     */
    private $kmMax;

    /**
     * @var string $periode
     *
     * @ORM\Column(name="periode", type="string", nullable=true)
     */
    private $periode;

    /**
     * @var Categorie
     *
     * @ORM\ManyToOne(targetEntity="Categorie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categorie_id", referencedColumnName="id")
     * })
     */
    private $categorie;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set prix
     *
     * @param float $prix
     * @return LigneDevis
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;
        return $this;
    }

    /**
     * Get prix
     *
     * @return float 
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set categorie
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Categorie $categorie
     * @return LigneDevis
     */
    public function setCategorie(\Esimed\CarLocation\BackendBundle\Entity\Categorie $categorie = null)
    {
        $this->categorie = $categorie;
        return $this;
    }

    /**
     * Get categorie
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Categorie 
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set devis
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Devis $devis
     * @return LigneDevis
     */
    public function setDevis(\Esimed\CarLocation\BackendBundle\Entity\Devis $devis = null)
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
     * Set kmMax
     *
     * @param integer $kmMax
     * @return LigneDevis
     */
    public function setKmMax($kmMax)
    {
        $this->kmMax = $kmMax;
        return $this;
    }

    /**
     * Get kmMax
     *
     * @return integer 
     */
    public function getKmMax()
    {
        return $this->kmMax;
    }

    /**
     * Set periode
     *
     * @param string $periode
     * @return LigneDevis
     */
    public function setPeriode($periode)
    {
        $this->periode = $periode;
        return $this;
    }

    /**
     * Get periode
     *
     * @return string 
     */
    public function getPeriode()
    {
        return $this->periode;
    }
}