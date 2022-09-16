<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlanRecape
 *
 * @ORM\Table(name="datos.plan_recape", indexes={@ORM\Index(name="IDX_F53848C7F3E6D02F", columns={"unidad"})})
 * @ORM\Entity
 */
class PlanRecape
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad", referencedColumnName="id")
     * })
     */
    private $unidad;



    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return PlanRecape
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return PlanRecape
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set unidad
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $unidad
     *
     * @return PlanRecape
     */
    public function setUnidad(\Geocuba\PortadoresBundle\Entity\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
}
