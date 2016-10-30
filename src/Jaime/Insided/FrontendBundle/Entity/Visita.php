<?php

namespace Jaime\Insided\FrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Visita
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Visita
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="contador", type="integer")
     */
    private $contador;


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
     * Set contador
     *
     * @param integer $contador
     * @return Visita
     */
    public function setContador($contador)
    {
        $this->contador = $contador;

        return $this;
    }

    /**
     * Get contador
     *
     * @return integer 
     */
    public function getContador()
    {
        return $this->contador;
    }
}
