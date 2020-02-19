<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

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
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_livraison", type="date",nullable=true)
     */
    private $dateLivraison;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=true)
     */
    private $adresse;

    /**
     * @var float
     *
     * @ORM\Column(name="prix_total", type="float",nullable=true)
     */
    private $prixTotal;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="commandes")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;
    /**
     * @OneToMany(targetEntity="ligne_commande", mappedBy="commande",orphanRemoval=true)
     */
    private $lignes_commande;
    /**
     * @ORM\Column(name="charge_id", type="string", length=255, nullable=true)
     */
    protected $chargeId;



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
     * @param \DateTime $date
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
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set dateLivraison
     *
     * @param \DateTime $dateLivraison
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
     * @return \DateTime
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

