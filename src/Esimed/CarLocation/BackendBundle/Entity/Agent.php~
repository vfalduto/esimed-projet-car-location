<?php

namespace Esimed\CarLocation\BackendBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Esimed\CarLocation\BackendBundle\Entity\Agent
 *
 * @ORM\Table(name="agent")
 * @ORM\Entity
 */
class Agent extends BaseUser
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var Agence
     *
     * @ORM\ManyToOne(targetEntity="Agence")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agence_id", referencedColumnName="id")
     * })
     */
    private $agence;



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
     * Set agence
     *
     * @param Esimed\CarLocation\BackendBundle\Entity\Agence $agence
     * @return Agent
     */
    public function setAgence(\Esimed\CarLocation\BackendBundle\Entity\Agence $agence = null)
    {
        $this->agence = $agence;
        return $this;
    }

    /**
     * Get agence
     *
     * @return Esimed\CarLocation\BackendBundle\Entity\Agence 
     */
    public function getAgence()
    {
        return $this->agence;
    }
}