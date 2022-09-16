<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DemandaCombustible
 *
 * @ORM\Table(name="datos.solicitud_compra_desglose", indexes={@ORM\Index(name="IDX_48B37B074EC221B2", columns={"tipo_combustible"}), @ORM\Index(name="IDX_48B37B07F3E6D02F", columns={"unidad"})})
 * @ORM\Entity
 */
class SolicitudCompraDesglose
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
     * @var int
     *
     * @ORM\Column(name="cant_litros", type="integer", nullable=false)
     */
    private $cantLitros;

    /**
     * @var int
     *
     * @ORM\Column(name="monto", type="float", nullable=false)
     */
    private $monto;

    /**
     * @var int|null
     *
     * @ORM\Column(name="comb_planificado", type="float", nullable=true)
     */
    private $combDistribuido;

    /**
     * @var int|null
     *
     * @ORM\Column(name="disponible_fincimex", type="float", nullable=true)
     */
    private $disponibleFincimex;

    /**
     * @var int|null
     *
     * @ORM\Column(name="saldo_fincimex", type="float", nullable=true)
     */
    private $saldoFincimex;

    /**
     * @var int|null
     *
     * @ORM\Column(name="saldo_caja", type="float", nullable=true)
     */
    private $saldoCaja;



    /**
     * @var \TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible", referencedColumnName="id")
     * })
     */
    private $tipoCombustible;

    /**
     * @var \SolicitudCompra
     *
     * @ORM\ManyToOne(targetEntity="SolicitudCompra")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="solicitud", referencedColumnName="id")
     * })
     */
    private $solicitud;

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
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cantLitros.
     *
     * @param int $cantLitros
     *
     * @return SolicitudCompraDesglose
     */
    public function setCantLitros($cantLitros)
    {
        $this->cantLitros = $cantLitros;
    
        return $this;
    }

    /**
     * Get cantLitros.
     *
     * @return int
     */
    public function getCantLitros()
    {
        return $this->cantLitros;
    }

    /**
     * Set monto.
     *
     * @param float $monto
     *
     * @return SolicitudCompraDesglose
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
    
        return $this;
    }

    /**
     * Get monto.
     *
     * @return float
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set combDistribuido.
     *
     * @param float|null $combDistribuido
     *
     * @return SolicitudCompraDesglose
     */
    public function setCombDistribuido($combDistribuido = null)
    {
        $this->combDistribuido = $combDistribuido;
    
        return $this;
    }

    /**
     * Get combDistribuido.
     *
     * @return float|null
     */
    public function getCombDistribuido()
    {
        return $this->combDistribuido;
    }

    /**
     * Set disponibleFincimex.
     *
     * @param float|null $disponibleFincimex
     *
     * @return SolicitudCompraDesglose
     */
    public function setDisponibleFincimex($disponibleFincimex = null)
    {
        $this->disponibleFincimex = $disponibleFincimex;
    
        return $this;
    }

    /**
     * Get disponibleFincimex.
     *
     * @return float|null
     */
    public function getDisponibleFincimex()
    {
        return $this->disponibleFincimex;
    }

    /**
     * Set saldoFincimex.
     *
     * @param float|null $saldoFincimex
     *
     * @return SolicitudCompraDesglose
     */
    public function setSaldoFincimex($saldoFincimex = null)
    {
        $this->saldoFincimex = $saldoFincimex;
    
        return $this;
    }

    /**
     * Get saldoFincimex.
     *
     * @return float|null
     */
    public function getSaldoFincimex()
    {
        return $this->saldoFincimex;
    }

    /**
     * Set saldoCaja.
     *
     * @param float|null $saldoCaja
     *
     * @return SolicitudCompraDesglose
     */
    public function setSaldoCaja($saldoCaja = null)
    {
        $this->saldoCaja = $saldoCaja;
    
        return $this;
    }

    /**
     * Get saldoCaja.
     *
     * @return float|null
     */
    public function getSaldoCaja()
    {
        return $this->saldoCaja;
    }

    /**
     * Set tipoCombustible.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible|null $tipoCombustible
     *
     * @return SolicitudCompraDesglose
     */
    public function setTipoCombustible(\Geocuba\PortadoresBundle\Entity\TipoCombustible $tipoCombustible = null)
    {
        $this->tipoCombustible = $tipoCombustible;
    
        return $this;
    }

    /**
     * Get tipoCombustible.
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoCombustible|null
     */
    public function getTipoCombustible()
    {
        return $this->tipoCombustible;
    }

    /**
     * Set solicitud.
     *
     * @param \Geocuba\PortadoresBundle\Entity\SolicitudCompra|null $solicitud
     *
     * @return SolicitudCompraDesglose
     */
    public function setSolicitud(\Geocuba\PortadoresBundle\Entity\SolicitudCompra $solicitud = null)
    {
        $this->solicitud = $solicitud;
    
        return $this;
    }

    /**
     * Get solicitud.
     *
     * @return \Geocuba\PortadoresBundle\Entity\SolicitudCompra|null
     */
    public function getSolicitud()
    {
        return $this->solicitud;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return SolicitudCompraDesglose
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
}
