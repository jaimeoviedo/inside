<?php

namespace Jaime\Insided\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Post
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Jaime\Insided\FrontendBundle\Entity\PostRepository")
 */
class Post
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * 
     * @Groups({"group1"})
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;
    
    /**
     * @Groups({"group1"})
     */
    private $dia;
    
    /**
     * @Groups({"group1"})
     */
    private $hora;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     * 
     * @Groups({"group1"})
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="path_image", type="string", length=255)
     * 
     * @Groups({"group1"})
     */
    private $pathImage;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Post
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
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set pathImage
     *
     * @param string $pathImage
     * @return Post
     */
    public function setPathImage($pathImage)
    {
        $this->pathImage = $pathImage;

        return $this;
    }

    /**
     * Get pathImage
     *
     * @return string 
     */
    public function getPathImage()
    {
        return $this->pathImage;
    }
    
    public function getDia()
    {
    	$mi_dia = '';
    	 
    	if($this->date){
    		$mi_dia = $this->date->format('d/m/Y');
    	}
    
    	return $mi_dia;
    }
    
    public function getHora()
    {
    	$mi_hora = '';
    
    	if($this->date){
    		$mi_hora = $this->date->format('H:i:s');
    	}
    
    	return $mi_hora;
    }
}
