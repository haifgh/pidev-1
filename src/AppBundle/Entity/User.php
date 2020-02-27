<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\MessageBundle\Model\ParticipantInterface;


/**
 * User
 *
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser implements ParticipantInterface
{

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\Column(name="nom", type="string",nullable=true)
    */
    private $nom;
    /**
     * @ORM\Column(name="prenom", type="string",nullable=true)
     */
    private $prenom;
    /**
     * @ORM\Column(name="tel", type="string",nullable=true)
     */
    private $tel;
    /**
     * @ORM\Column(name="adresse", type="string",nullable=true)
     */
    private $adresse;
    /**
     * @var int
     *
     * @ORM\Column(name="followers", type="integer",options={"default" : 0},nullable=true)
     */
    private $follower;

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param mixed $prenom
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    }


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
     * One product has many features. This is the inverse side.
     * @OneToMany(targetEntity="commande", mappedBy="user",cascade="remove")
     */
    private $commandes;

    /**
     * One product has many features. This is the inverse side.
     * @OneToMany(targetEntity="Evenement", mappedBy="user",cascade="remove")
     */
    private $evenements;
    /**
     * One product has many features. This is the inverse side.
     * @OneToMany(targetEntity="reclamation", mappedBy="user",cascade="remove")
     */
    private $reclamations;
    /**
     * One product has many features. This is the inverse side.
     * @OneToMany(targetEntity="Post", mappedBy="user",cascade="remove")
     */

    private $posts;

    /**
     *
     * @OneToMany(targetEntity="liste_participants", mappedBy="user",cascade="remove")
     */
    private $participations;

    public function __construct() {
        parent::__construct();
        $this->commandes = new ArrayCollection();
        $this->evenements = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->reclamations = new ArrayCollection();
        $this->participations = new ArrayCollection();
    }

    /**
     * @return ArrayCollection
     */
    public function getCommandes()
    {
        return $this->commandes;
    }

    /**
     * @param ArrayCollection $commandes
     */
    public function setCommandes($commandes)
    {
        $this->commandes = $commandes;
    }

    /**
     * @return ArrayCollection
     */
    public function getEvenements()
    {
        return $this->evenements;
    }

    /**
     * @return ArrayCollection
     */
    public function getParticipations()
    {
        return $this->participations;
    }

    /**
     * @param ArrayCollection $participations
     */
    public function setParticipations($participations)
    {
        $this->participations = $participations;
    }

    /**
     * @param ArrayCollection $evenements
     */
    public function setEvenements($evenements)
    {
        $this->evenements = $evenements;
    }

    /**
     * @return ArrayCollection
     */
    public function getReclamations()
    {
        return $this->reclamations;
    }

    /**
     * @param ArrayCollection $reclamations
     */
    public function setReclamations($reclamations)
    {
        $this->reclamations = $reclamations;
    }

    /**
     * @return ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param ArrayCollection $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    /**
     * @return int
     */
    public function getFollower()
    {
        return $this->follower;
    }

    /**
     * @param int $follower
     */
    public function setFollower($follower)
    {
        $this->follower = $follower;
    }


}

