<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * ligne_promotion
 *
 * @ORM\Table(name="ligne_promotion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ligne_promotionRepository")
 */
class ligne_promotion
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
     * @var float
     *
     * @ORM\Column(name="taux_reduction", type="float")
     */
    private $tauxReduction;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;

    /**
     * @var int
     *
     * @ORM\Column(name="quantite", type="integer")
     */
    private $quantite;


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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     *
     * @return ligne_promotion
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
     * @return ligne_promotion
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
     * Set tauxReduction
     *
     * @param float $tauxReduction
     *
     * @return ligne_promotion
     */
    public function setTauxReduction($tauxReduction)
    {
        $this->tauxReduction = $tauxReduction;

        return $this;
    }

    /**
     * Get tauxReduction
     *
     * @return float
     */
    public function getTauxReduction()
    {
        return $this->tauxReduction;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return ligne_promotion
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return ligne_promotion
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite
     *
     * @return int
     */
    public function getQuantite()
    {
        return $this->quantite;
    }
    /**
     * Many features have one product. This is the owning side.
     * @ManyToOne(targetEntity="Promotion", inversedBy="lignes_promotion")
     * @JoinColumn(name="Promotion_id", referencedColumnName="id")
     */
    private $promotion;
    /**
     *
     * @ManyToOne(targetEntity="Produit")
     * @JoinColumn(name="produit_id", referencedColumnName="id")
     */
    private $produit;

    /**
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param mixed $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return mixed
     */
    public function getProduit()
    {
        return $this->produit;
    }

    /**
     * @param mixed $produit
     */
    public function setProduit($produit)
    {
        $this->produit = $produit;
    }
}

