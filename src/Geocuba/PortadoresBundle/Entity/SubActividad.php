<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubActividad
 *
 * @ORM\Table(name="nomencladores.subactividad", indexes={@ORM\Index(name="IDX_6299E1FF8D824A80", columns={"nactividadid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\SubActividadRepository")
 */
class SubActividad
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
     * @var Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nactividadid", referencedColumnName="id")
     * })
     */
    private $nactividadid;



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
     * @return SubActividad
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
     * @return SubActividad
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
     * Set nactividadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Actividad $nactividadid
     *
     * @return SubActividad
     */
    public function setNactividadid(\Geocuba\PortadoresBundle\Entity\Actividad $nactividadid = null)
    {
        $this->nactividadid = $nactividadid;

        return $this;
    }

    /**
     * Get nactividadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Actividad
     */
    public function getNactividadid()
    {
        return $this->nactividadid;
    }
}
