<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * Evenement
 *
 * @ORM\Table(name="evenement")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EvenementRepository")
 */
class Evenement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_debut", type="date")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_fin", type="date")
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="validite", type="string", length=255)
     */
    private $validite;

    /**
     * @var int
     *
     * @ORM\Column(name="nbre_places", type="integer")
     */
    private $nbrePlaces;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Evenement
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
     * Set description
     *
     * @param string $description
     *
     * @return Evenement
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return Evenement
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     *
     * @return Evenement
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set validite
     *
     * @param string $validite
     *
     * @return Evenement
     */
    public function setValidite($validite)
    {
        $this->validite = $validite;

        return $this;
    }

    /**
     * Get validite
     *
     * @return string
     */
    public function getValidite()
    {
        return $this->validite;
    }

    /**
     * Set nbrePlaces
     *
     * @param integer $nbrePlaces
     *
     * @return Evenement
     */
    public function setNbrePlaces($nbrePlaces)
    {
        $this->nbrePlaces = $nbrePlaces;

        return $this;
    }

    /**
     * Get nbrePlaces
     *
     * @return int
     */
    public function getNbrePlaces()
    {
        return $this->nbrePlaces;
    }
    /**
     * Many features have one product. This is the owning side.
     * @ManyToOne(targetEntity="User", inversedBy="evenements")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    /**
     *
     * @OneToMany(targetEntity="liste_participants", mappedBy="evenement")
     */
    private $listes_participants;
    // ...

    public function __construct() {
        $this->listes_participants = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return ArrayCollection
     */
    public function getListesParticipants()
    {
        return $this->listes_participants;
    }

    /**
     * @param ArrayCollection $listes_participants
     */
    public function setListesParticipants($listes_participants)
    {
        $this->listes_participants = $listes_participants;
    }
}

