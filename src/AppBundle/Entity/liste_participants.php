<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

/**
 * liste_participants
 *
 * @ORM\Table(name="liste_participants")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\liste_participantsRepository")
 */
class liste_participants
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
     * @ORM\Column(name="date_participation", type="date")
     */
    private $dateParticipation;


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
     * Set dateParticipation
     *
     * @param \DateTime $dateParticipation
     *
     * @return liste_participants
     */
    public function setDateParticipation($dateParticipation)
    {
        $this->dateParticipation = $dateParticipation;

        return $this;
    }

    /**
     * Get dateParticipation
     *
     * @return \DateTime
     */
    public function getDateParticipation()
    {
        return $this->dateParticipation;
    }
    /**
     * Many features have one product. This is the owning side.
     * @ManyToOne(targetEntity="Evenement", inversedBy="listes_participants")
     * @JoinColumn(name="evenement_id", referencedColumnName="id")
     */
    private $evenement;

    /**
     * @return mixed
     */
    public function getEvenement()
    {
        return $this->evenement;
    }

    /**
     * @param mixed $evenement
     */
    public function setEvenement($evenement)
    {
        $this->evenement = $evenement;
    }
}

