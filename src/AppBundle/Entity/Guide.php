<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Guide
 *
 * @ORM\Table(name="guide")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GuideRepository")
 */
class Guide
{
    /**
     * @var int
     * @Groups("guide")
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @Groups("guide")
     * @Assert\NotBlank()
     * @ORM\Column(name="titre", type="string", length=255)
     */
    private $titre;



    /**
     * @var \DateTime
     *@Groups("guide")
     * @ORM\Column(name="date_creation", type="date")
     */
    private $dateCreation;

    /**
     * @var string
     * @Groups("guide")
     * @Assert\NotBlank()
     * @ORM\Column(name="categorie", type="string", length=255)
     */
    private $categorie;

    /**
     * @var string
     * @Groups("guide")
     * @Assert\NotBlank()
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     * @Groups("guide")
     * @Assert\NotBlank()
     * @ORM\Column(name="lien", type="string", length=255)
     */
    private $lien;

    /**
     * @var double
     *@Groups("guide")
     * @ORM\Column(name="note", type="float" )
     */
    private $note;

    /**
     * @var string
     *@Groups("guide")
     * @ORM\Column(name="photo", type="string", length=255)
     */
    private $photo ;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Likes", mappedBy="guide",cascade="remove")
     */
    private $likes;

    /**
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param string $photo
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
     * Set titre
     *
     * @param string $titre
     *
     * @return Guide
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Get titre
     *
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }


    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     *
     * @return Guide
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set categorie
     *
     * @param string $categorie
     *
     * @return Guide
     */
    public function setCategorie($categorie)
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * Get categorie
     *
     * @return string
     */
    public function getCategorie()
    {
        return $this->categorie;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Guide
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
     * Set lien
     *
     * @param string $lien
     *
     * @return Guide
     */
    public function setLien($lien)
    {
        $this->lien = $lien;

        return $this;
    }

    /**
     * Get lien
     *
     * @return string
     */
    public function getLien()
    {
        return $this->lien;
    }
    /**
     * One product has many features. This is the inverse side.
     * @OneToMany(targetEntity="Commentaire", mappedBy="guide",cascade="remove")
     */
    private $commentaires;
    // ...

    public function getCountLikes()
    {
        return count($this->getLikes());
    }
    public function __construct() {
        $this->commentaires = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->rating = new ArrayCollection();

    }

    /**
     * @return ArrayCollection
     */
    public function getCommentaires()
    {
        return $this->commentaires;
    }

    /**
     * @param ArrayCollection $commentaires
     */
    public function setCommentaires($commentaires)
    {
        $this->commentaires = $commentaires;
    }

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

    /**
     * @return float
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param float $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return ArrayCollection
     */
    public function getLikes()
    {
        return $this->likes;
    }

    /**
     * @param ArrayCollection $likes
     */
    public function setLikes($likes)
    {
        $this->likes = $likes;
    }





}

