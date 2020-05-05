<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\commandeRepository")
 */
class commande
{
    /**
     * @var int
     * @Groups("commande")
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var DateTime
     *@Groups("commande")
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var DateTime
     *@Groups("commande")
     * @ORM\Column(name="date_livraison", type="datetime",nullable=true)
     */
    private $dateLivraison;

    /**
     * @var string
     *@Groups("commande")
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     * @Assert\NotBlank
     * @Groups("commande")
     * @Assert\Length(min="12")
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;
    /**
     * @var string
     * @Assert\NotBlank
     * @Groups("commande")
     * @Assert\Length(min="8",max="8")
     * @ORM\Column(name="tel", type="string", length=255, nullable=true)
     */
    private $tel;


    /**
     * @var float
     *@Groups("commande")
     * @ORM\Column(name="prix_total", type="float",nullable=true)
     */
    private $prixTotal;

    /**
     * @Groups("commande")
     * @ManyToOne(targetEntity="User", inversedBy="commandes")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    /**
     * @Groups("commande")
     * @OneToMany(targetEntity="ligne_commande", mappedBy="commande",orphanRemoval=true)
     */
    private $lignes_commande;
    /**
     * @Groups("commande")
     * @ORM\Column(name="charge_id", type="string", length=255, nullable=true)
     */
    protected $chargeId;


    /**
     * @return string
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @param string $tel
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    }

    /**
     * @return mixed
     */
    public function getChargeId()
    {
        return $this->chargeId;
    }

    /**
     * @param mixed $chargeId
     */
    public function setChargeId($chargeId)
    {
        $this->chargeId = $chargeId;
    }


    public function __construct() {
        $this->lignes_commande = new ArrayCollection();
        $this->paid=false;
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
     * Set date
     *
     * @param DateTime $date
     *
     * @return commande
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set dateLivraison
     *
     * @param DateTime $dateLivraison
     *
     * @return commande
     */
    public function setDateLivraison($dateLivraison)
    {
        $this->dateLivraison = $dateLivraison;

        return $this;
    }

    /**
     * Get dateLivraison
     *
     * @return DateTime
     */
    public function getDateLivraison()
    {
        return $this->dateLivraison;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return commande
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     *
     * @return commande
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set prixTotal
     *
     * @param float $prixTotal
     *
     * @return commande
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
    public function getLignesCommande()
    {
        return $this->lignes_commande;
    }

    /**
     * @param ArrayCollection $lignes_commande
     */
    public function setLignesCommande($lignes_commande)
    {
        $this->lignes_commande = $lignes_commande;
    }

}

