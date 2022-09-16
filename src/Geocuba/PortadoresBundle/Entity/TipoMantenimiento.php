<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoMantenimiento
 *
 * @ORM\Table(name="nomencladores.tipo_mantenimiento", indexes={@ORM\Index(name="IDX_343C058FE9938171", columns={"clasificacion"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TipoMantenimientoRepository")
 */
class TipoMantenimiento
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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible = true;

    /**
     * @var TipoMantenimientoClasificacion
     *
     * @ORM\ManyToOne(targetEntity="TipoMantenimientoClasificacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="clasificacion", referencedColumnName="id")
     * })
     */
    private $clasificacion;



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
     * @return TipoMantenimiento
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
     * @return TipoMantenimiento
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
     * Set clasificacion
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoMantenimientoClasificacion $clasificacion
     *
     * @return TipoMantenimiento
     */
    public function setClasificacion(\Geocuba\PortadoresBundle\Entity\TipoMantenimientoClasificacion $clasificacion = null)
    {
        $this->clasificacion = $clasificacion;

        return $this;
    }

    /**
     * Get clasificacion
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoMantenimientoClasificacion
     */
    public function getClasificacion()
    {
        return $this->clasificacion;
    }
}
