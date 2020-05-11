<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * Produit
 *
 * @ORM\Table(name="produit")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProduitRepository")
 */
class Produit
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
     *  @Groups("produit")
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var int
     * @Groups("produit")
     * @Assert\GreaterThan(-1)
     * @ORM\Column(name="qte", type="integer")
     */
    private $qte;

    /**
     * @var int
     * @Groups("produit")
     * @Assert\GreaterThan(0)
     * @ORM\Column(name="prix", type="integer")
     */
    private $prix;
    /**
     * @var float
     * @Groups("produit")
     * @ORM\Column(name="$prix_promo", type="float",nullable=true)
     */
    private $prix_promo;


    /**
     *
     * @OneToMany(targetEntity="ligne_promotion", mappedBy="produit",cascade="remove")
     */
    private $lignes_promotion;



    public function __construct() {
        $this->lignes_promotion = new ArrayCollection();
    }

    /**
     * @var string
     * @Groups("produit")
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;
    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255)
     */

    private $photo;

    /**
     * @return mixed
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param mixed $photo
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;
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
     * @return float
     */
    public function getPrixPromo()
    {
        return $this->prix_promo;
    }

    /**
     * @param float $prix_promo
     */
    public function setPrixPromo($prix_promo)
    {
        $this->prix_promo = $prix_promo;
    }
    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Produit
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
     * @return ArrayCollection
     */
    public function getLignesPromotion()
    {
        return $this->lignes_promotion;
    }

    /**
     * @param ArrayCollection $lignes_promotion
     */
    public function setLignesPromotion($lignes_promotion)
    {
        $this->lignes_promotion = $lignes_promotion;
    }
    // ...
    /**
     * Set qte
     *
     * @param integer $qte
     *
     * @return Produit
     */
    public function setQte($qte)
    {
        $this->qte = $qte;

        return $this;
    }

    /**
     * Get qte
     *
     * @return int
     */
    public function getQte()
    {
        return $this->qte;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return Produit
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return int
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Produit
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
     * Many features have one product. This is the owning side.
     * @ManyToOne(targetEntity="categorie", inversedBy="produits")
     * @JoinColumn(name="categorie_id", referencedColumnName="id")
     */
    private $categorie;

    /**
     * @return mixed
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * @param mixed $categorie
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;
    }
    // ...
    public function getWebPath()
    {
        return null===$this->photo ? null : $this->getUploadDir().'/'.$this->photo;
    }
    protected function getUploadRootDir()
    {
        return dirname(__FILE__).'/../../../web/'.$this->getUploadDir();
    }
    protected function getUploadDir()
    {
        return 'images';
    }
    public function UploadProfilePicture()
    {
        $this->photo->move($this->getUploadRootDir(),$this->photo->getClientOriginalName());
        $this->photo=$this->photo->getClientOriginalName();
        $this->file=null;
    }
}

