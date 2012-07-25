<?php

namespace Esimed\CarLocation\BackendBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\ExecutionContext;

use Doctrine\ORM\Mapping as ORM;

/**
 * Esimed\CarLocation\BackendBundle\Entity\Client
 *
 * @ORM\Table(name="client")
 * @ORM\Entity(repositoryClass="ClientRepository")
 *
 * @UniqueEntity(fields={"nom", "prenom", "dateNaissance"}, message="Ce client existe déja dans la base")
 * @Assert\Callback(methods={"isPermisValid"})
 */
class Client extends EntityArray
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
     * @var string $nom
     * @Assert\MinLength(
     *     limit=2,
     *     message="Le nom du client doit comporter au moins {{ limit }} caractéres."
     * )
     *
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string $prenom
     * @Assert\MinLength(
     *     limit=2,
     *     message="Le prénom du client doit comporter au moins {{ limit }} caractéres."
     * )
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=false)
     * @Assert\NotBlank()
     */
    private $prenom;

    /**
     * @var string $dateNaissance
     *
     * @ORM\Column(name="date_naissance", type="date", nullable=false)
     */
    private $dateNaissance;

    /**
     * @var date $datePermis
     *
     * @ORM\Column(name="date_permis", type="date", nullable=true)
     */
    private $datePermis;

    /**
     * @var Location
     * @ORM\OneToMany(targetEntity="Location", mappedBy="client")
     *
     */
    private $location;

    public function __construct()
    {
        $this->location = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nom
     *
     * @param string $nom
     * @return Client
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Client
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set datePermis
     *
     * @param date $datePermis
     * @return Client
     */
    public function setDatePermis($datePermis)
    {
        $this->datePermis = $datePermis;
        return $this;
    }

    /**
     * Get datePermis
     *
     * @return date 
     */
    public function getDatePermis()
    {
        return $this->datePermis;
    }

    /**
     * Set dateNaissance
     *
     * @param string $dateNaissance
     * @return Client
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;
        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return string
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Retourne la date de naissance pour la date courante
     *
     * @return int
     */
    public function getAge($current = null) {
        !$current && $current = new \DateTime();

        if ($current >= $this->getDateNaissance()) {
            $interval = date_diff( $this->getDateNaissance(), $current);
            return $interval->y;
        }

        return 0;
    }

    /**
     * Retourne le nombre d'année du permis pour la date courante
     *
     * @return int|null
     */
    public function getAgePermis($current = null) {
        !$current && $current = new \DateTime();

        if ($this->getDatePermis() && $current >= $this->getDatePermis()) {
            $interval = date_diff( $this->getDatePermis(), $current);
            return $interval->y;
        }

        return null;
    }

    /**
     * Add location
     * @param Esimed\CarLocation\BackendBundle\Entity\Location $location
     */
    public function addLocation(\Esimed\CarLocation\BackendBundle\Entity\Location $location)
    {
        $this->location[] = $location;
    }

    /**
     * Get location
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * retourne faux si l'utilisateur ne peux pas être supprimée
     * @return bool
     */
    public function canBeDeleted() {
        return count($this->getLocation()) == 0;
    }

    /**
     * vérifie si le permis est valide
     * @param \Symfony\Component\Validator\ExecutionContext $context
     * @return bool
     */
    public function isPermisValid(ExecutionContext $context) {

        if ($this->getDateNaissance() >= $this->getDatePermis()) {
            $context->addViolation(
                'Le client doit être né pour avoir un permis',
                array(),
                null
            );
            return;
        }

        $interval = date_diff( $this->getDateNaissance(),  $this->getDatePermis());

        if ( $interval->y < 18) {
            $context->addViolation(
                'Le client ne peux pas avoir obtenu son permis avant ses 18 ans',
                array(),
                null
            );
        }
    }

    /**
     *  renvoie true si les imformations du clients sont complétes
     * @return bool
     */
    public function isComplete() {
        return $this->getDatePermis() != null;
    }

    public function __toString() {

        return strtoupper($this->getNom()) . ' ' . ucfirst($this->getPrenom()) . ' ' .  $this->getDateNaissance()->format('d/m/Y');
    }
}