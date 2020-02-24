<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var DateTime
     * @Assert\DateTime()
     * @Assert\Type(type="DateTime")
     * @ORM\Column(name="date_debut", type="datetime")
     */
    private $dateDebut;

    /**
     * @var DateTime
     * @Assert\DateTime()
     * @Assert\Type(type="DateTime")
     * @Assert\GreaterThan(propertyPath="dateDebut")
     * @ORM\Column(name="date_fin", type="datetime")
     */
    private $dateFin;

    /**
     * @var string
     *
     * @ORM\Column(name="validite", type="string", length=255 , nullable=true)
     */
    private $validite;

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255)
     */
    private $photo;


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
     * @param DateTime $dateDebut
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
     * @return DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param DateTime $dateFin
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
     * @return DateTime
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
     * @OneToMany(targetEntity="liste_participants", mappedBy="evenement",cascade="remove")
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
    public function getWebPath()
    {
        return null === $this->photo ? null : $this->getUploadDir().'/'.$this->photo;
    }
    protected function getUploadRootDir()
    {
        //return __DIR__.'/../../../../web/'.$this->getUploadDir();
        return dirname(__FILE__).'/../../../web'.$this->getUploadDir();
    }
    protected function getUploadDir(){
        return 'images';
    }
    public function UploaderProfilePicture()
    {
        $this->photo->move($this->getUploadRootDir(),$this->photo->getClientOriginalName());
        $this->photo=$this->photo->getClientOriginalName();
        $this->file=null;
    }
}

