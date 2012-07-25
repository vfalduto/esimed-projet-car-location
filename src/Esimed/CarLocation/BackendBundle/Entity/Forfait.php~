<?php

namespace Esimed\CarLocation\BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Esimed\CarLocation\BackendBundle\Entity\Forfait
 *
 * @ORM\Table(name="forfait")
 * @ORM\Entity
 *
 * @ORM\Entity(repositoryClass="ForfaitRepository")
 */
class Forfait
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
     * @var integer $kmMax
     *
     * @ORM\Column(name="km_max", type="integer", nullable=false)
     */
    private $kmMax;

    /**
     * @var float $prix
     *
     * @ORM\Column(name="prix", type="float", nullable=false)
     */
    private $prix;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set kmMax
     *
     * @param integer $kmMax
     * @return Forfait
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
     * Set prix
     *
     * @param float $prix
     * @return Forfait
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
     * Set periode
     *
     * @param string $periode
     * @return Forfait
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

    /**
     * Set categorie
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Categorie $categorie
     * @return Forfait
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

    public function __toString() {
        return $this->getCategorie() . ' ' . $this->getPeriode() . ' ' . $this->getPrix() . ' € ' . $this->getKmMax() . ' Km';
    }
}