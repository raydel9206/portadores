<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DistribucionCombustible
 *
 * @ORM\Table(name="datos.solicitud_compra", indexes={@ORM\Index(name="IDX_343BE3934EC221B2", columns={"moneda"}), @ORM\Index(name="IDX_343BE3933C7CEFFE", columns={"nunidadid"})})
 * @ORM\Entity
 */
class SolicitudCompra
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="monto_cup", type="float", precision=10, scale=0, nullable=false)
     */
    private $monto_cup;

    /**
     * @var float
     *
     * @ORM\Column(name="monto_cuc", type="float", precision=10, scale=0, nullable=false)
     */
    private $monto_cuc;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aprobado", type="boolean", nullable=false)
     */
    private $aprobado;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;


    /**
     * @var unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;

    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return SolicitudCompra
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set montoCup.
     *
     * @param float $montoCup
     *
     * @return SolicitudCompra
     */
    public function setMontoCup($montoCup)
    {
        $this->monto_cup = $montoCup;
    
        return $this;
    }

    /**
     * Get montoCup.
     *
     * @return float
     */
    public function getMontoCup()
    {
        return $this->monto_cup;
    }

    /**
     * Set montoCuc.
     *
     * @param float $montoCuc
     *
     * @return SolicitudCompra
     */
    public function setMontoCuc($montoCuc)
    {
        $this->monto_cuc = $montoCuc;
    
        return $this;
    }

    /**
     * Get montoCuc.
     *
     * @return float
     */
    public function getMontoCuc()
    {
        return $this->monto_cuc;
    }

    /**
     * Set aprobado.
     *
     * @param bool $aprobado
     *
     * @return SolicitudCompra
     */
    public function setAprobado($aprobado)
    {
        $this->aprobado = $aprobado;
    
        return $this;
    }

    /**
     * Get aprobado.
     *
     * @return bool
     */
    public function getAprobado()
    {
        return $this->aprobado;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return SolicitudCompra
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set nunidadid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $nunidadid
     *
     * @return SolicitudCompra
     */
    public function setNunidadid(\Geocuba\PortadoresBundle\Entity\Unidad $nunidadid = null)
    {
        $this->nunidadid = $nunidadid;
    
        return $this;
    }

    /**
     * Get nunidadid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getNunidadid()
    {
        return $this->nunidadid;
    }
}
