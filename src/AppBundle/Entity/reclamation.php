<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * reclamation
 *
 * @ORM\Table(name="reclamation")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\reclamationRepository")
 */
class reclamation
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
     * @Assert\Length(
     *      min = 5,
     *      max = 255,
     *      minMessage = "Your object must be at least 5 characters long",
     *      maxMessage = "Your object cannot be longer than 255 characters"
     * )
     * @ORM\Column(name="objet", type="string", length=255)
     */
    private $objet;

    /**
     * @var string
     * Assert\Length(
     *      min = 5,
     *      max = 400,
     *      minMessage = "Your object must be at least 5 characters long",
     *      maxMessage = "Your object cannot be longer than 400 characters"
     * )
     *
     * @ORM\Column(name="contenu", type="string", length=400)
     */
    private $contenu;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;


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
     * Set objet
     *
     * @param string $objet
     *
     * @return reclamation
     */
    public function setObjet($objet)
    {
        $this->objet = $objet;

        return $this;
    }

    /**
     * Get objet
     *
     * @return string
     */
    public function getObjet()
    {
        return $this->objet;
    }

    /**
     * Set contenu
     *
     * @param string $contenu
     *
     * @return reclamation
     */
    public function setContenu($contenu)
    {
        $this->contenu = $contenu;

        return $this;
    }

    /**
     * Get contenu
     *
     * @return string
     */
    public function getContenu()
    {
        return $this->contenu;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return reclamation
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
     *
     * @ManyToOne(targetEntity="User", inversedBy="reclamations")
     * @JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     *
     * @ManyToOne(targetEntity="User", inversedBy="reclamations")
     * @JoinColumn(name="reclamer", referencedColumnName="id")
     */
    private $reclamer;

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
     * @return mixed
     */
    public function getReclamer()
    {
        return $this->reclamer;
    }

    /**
     * @param mixed $reclamer
     */
    public function setReclamer($reclamer)
    {
        $this->reclamer = $reclamer;
    }

}

