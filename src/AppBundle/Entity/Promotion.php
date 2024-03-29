<?php

namespace AppBundle\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Promotion
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PromotionRepository")
 */
class Promotion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @Groups("Promotion")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var DateTime
     * @Assert\DateTime()
     * @Groups("Promotion")
     * @Assert\Type(type="DateTime")
     * @ORM\Column(name="date_debut", type="datetime")
     */
    private $dateDebut;

    /**
     * @var DateTime
     * @Assert\DateTime()
     * @Groups("Promotion")
     * @Assert\Type(type="DateTime")
     * @Assert\GreaterThan(propertyPath="dateDebut")
     * @ORM\Column(name="date_fin", type="datetime")
     */
    private $dateFin;

    /**
     * @var float
     *@Groups("Promotion")
     * @ORM\Column(name="taux_reduction", type="float")
     */
    private $tauxReduction;
    /**
     * @var string
     *@Groups("Promotion")
     * @ORM\Column(name="nom", type="string")
     */
    private $nom;
    private $valid;

   
    public function getValid()
    {
        $date=new DateTime('now');
        if ($this->getDateDebut()<$date && $this->getDateFin()>$date)
        return true;
        return false;
    }

    /**
     * @param mixed $valid
     */
    public function setValid($valid)
    {
        $this->valid = $valid;
    }

    /**
     *
     * @OneToMany(targetEntity="ligne_promotion", mappedBy="promotion",cascade="remove")
     */
    private $lignes_promotion;
    // ...

    public function __construct() {
        $this->lignes_promotion = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }



    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
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

    /**
     * @return \DateTime
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * @param \DateTime $dateDebut
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;
    }

    /**
     * @return \DateTime
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * @param \DateTime $dateFin
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;
    }

    /**
     * @return float
     */
    public function getTauxReduction()
    {
        return $this->tauxReduction;
    }

    /**
     * @param float $tauxReduction
     */
    public function setTauxReduction($tauxReduction)
    {
        $this->tauxReduction = $tauxReduction;
    }


}

