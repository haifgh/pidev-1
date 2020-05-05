<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * ligne_commande
 *
 * @ORM\Table(name="ligne_commande")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ligne_commandeRepository")
 */
class ligne_commande
{
    /**
     * @var int
     *@Groups("commande")
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *@Groups("l_commande")
     * @ORM\Column(name="prix", type="float")
     */
    private $prix;

    /**
     * @var int
     * @Groups("l_commande")
     * @Assert\LessThan(propertyPath="Produit.qte")
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
     * Set prix
     *
     * @param float $prix
     *
     * @return ligne_commande
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
     * Set quantite
     *
     * @param integer $quantite
     *
     * @return ligne_commande
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
     * @Groups("commande_details")
     * @ManyToOne(targetEntity="commande", inversedBy="lignes_commande")
     * @JoinColumn(name="commande_id", referencedColumnName="id")
     */
    private $commande;
    /**
     *
     * @ManyToOne(targetEntity="Produit")
     * @Groups("commande_produit")
     * @JoinColumn(name="produit_id", referencedColumnName="id")
     */
    private $produit;

    /**
     * @return mixed
     */
    public function getCommande()
    {
        return $this->commande;
    }

    /**
     * @param mixed $commande
     */
    public function setCommande($commande)
    {
        $this->commande = $commande;
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

