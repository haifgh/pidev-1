<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

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
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pts_fidelite", type="string", length=255)
     */
    private $ptsFidelite;


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
     * Set ptsFidelite
     *
     * @param string $ptsFidelite
     *
     * @return Promotion
     */
    public function setPtsFidelite($ptsFidelite)
    {
        $this->ptsFidelite = $ptsFidelite;

        return $this;
    }

    /**
     * Get ptsFidelite
     *
     * @return string
     */
    public function getPtsFidelite()
    {
        return $this->ptsFidelite;
    }
    /**
     *
     * @OneToMany(targetEntity="ligne_promotion", mappedBy="promotion")
     */
    private $lignes_promotion;
    // ...

    public function __construct() {
        $this->lignes_promotion = new ArrayCollection();
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
}

