<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DetalleGasto
 *
 * @ORM\Table(name="nomencladores.detalle_gasto")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\DetalleGastoRepository")
 */
class DetalleGasto
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
     * @ORM\Column(name="codigo", type="string", nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    /**
     * @var \Moneda
     *
     * @ORM\ManyToOne(targetEntity="Moneda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="moneda", referencedColumnName="id")
     * })
     */
    private $moneda;

    /**
     * @var \TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ntipo_combustibleid", referencedColumnName="id")
     * })
     */
    private $ntipoCombustibleid;

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
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

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
     * Set descripcion
     *
     * @param string $nombre
     *
     * @return DetalleGasto
     */
    public function setNombre($nombre)
    {
        $this->descripcion = $nombre;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->descripcion;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return DetalleGasto
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set ntipoCombustibleid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible|null $ntipoCombustibleid
     *
     * @return DetalleGasto
     */
    public function setNtipoCombustibleid(\Geocuba\PortadoresBundle\Entity\TipoCombustible $ntipoCombustibleid = null)
    {
        $this->ntipoCombustibleid = $ntipoCombustibleid;

        return $this;
    }

    /**
     * Get elementogasto.
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoCombustible|null
     */
    public function getNtipoCombustibleid()
    {
        return $this->ntipoCombustibleid;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return DetalleGasto
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
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return DetalleGasto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set elementogasto.
     *
     * @param \Geocuba\PortadoresBundle\Entity\ElementoGasto|null $elementogasto
     *
     * @return DetalleGasto
     */
    public function setElementogasto(\Geocuba\PortadoresBundle\Entity\ElementoGasto $elementogasto = null)
    {
        $this->elementogasto = $elementogasto;

        return $this;
    }

    /**
     * Get elementogasto.
     *
     * @return \Geocuba\PortadoresBundle\Entity\ElementoGasto|null
     */
    public function getElementogasto()
    {
        return $this->elementogasto;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return ElementoGasto
     */
    public function setMoneda(\Geocuba\PortadoresBundle\Entity\Moneda $moneda = null)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Moneda|null
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set unidad.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $unidad
     *
     * @return DetalleGasto
     */
    public function setUnidad(\Geocuba\PortadoresBundle\Entity\Unidad $unidad = null)
    {
        $this->unidad = $unidad;
    
        return $this;
    }

    /**
     * Get unidad.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
}
