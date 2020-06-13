<?php

namespace AppBundle\Entity;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * Likes
 *
 * @ORM\Table(name="likes")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LikesRepository")
 */
class Likes
{
    /**
     * @var int
     *@Groups("likes")
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @Groups("likes")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="id_user",referencedColumnName="id")
     */
    private $user;
    /**
     * @Groups("likes")
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Guide", inversedBy="likes")
     * @ORM\JoinColumn(name="id_guide",referencedColumnName="id")
     */
    private $guide;

    /**
     * @return int
     */
    public function getGuide()
    {
        return $this->guide;
    }


    public function setGuide($guide)
    {
        $this->guide = $guide;
    }

    public function getUser()
    {
        return $this->user;
    }


    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getId()
    {
        return $this->id;
    }

}

