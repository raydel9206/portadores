<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DemandaCombustible
 *
 * @ORM\Table(name="datos.demanda_combustible", indexes={@ORM\Index(name="IDX_48B37B07B00B2B2D", columns={"moneda"}), @ORM\Index(name="IDX_48B37B07F3E6D02F", columns={"unidad"}), @ORM\Index(name="IDX_48B37B074EC221B2", columns={"tipo_combustible"})})
 * @ORM\Entity
 */
class DemandaCombustible
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
     * @var int|null
     *
     * @ORM\Column(name="mes", type="integer", nullable=true)
     */
    private $mes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cant_litros", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $cantLitros = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="disponible_fincimex", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $disponibleFincimex = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="saldo_fincimex", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $saldoFincimex = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="saldo_caja", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $saldoCaja = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false, options={"default"="1"})
     */
    private $visible = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comb_planificado", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $combPlanificado = '0';

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
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad", referencedColumnName="id")
     * })
     */
    private $unidad;

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
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mes.
     *
     * @param int|null $mes
     *
     * @return DemandaCombustible
     */
    public function setMes($mes = null)
    {
        $this->mes = $mes;
    
        return $this;
    }

    /**
     * Get mes.
     *
     * @return int|null
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set anno.
     *
     * @param int|null $anno
     *
     * @return DemandaCombustible
     */
    public function setAnno($anno = null)
    {
        $this->anno = $anno;
    
        return $this;
    }

    /**
     * Get anno.
     *
     * @return int|null
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set cantLitros.
     *
     * @param string|null $cantLitros
     *
     * @return DemandaCombustible
     */
    public function setCantLitros($cantLitros = null)
    {
        $this->cantLitros = $cantLitros;
    
        return $this;
    }

    /**
     * Get cantLitros.
     *
     * @return string|null
     */
    public function getCantLitros()
    {
        return $this->cantLitros;
    }

    /**
     * Set disponibleFincimex.
     *
     * @param string|null $disponibleFincimex
     *
     * @return DemandaCombustible
     */
    public function setDisponibleFincimex($disponibleFincimex = null)
    {
        $this->disponibleFincimex = $disponibleFincimex;
    
        return $this;
    }

    /**
     * Get disponibleFincimex.
     *
     * @return string|null
     */
    public function getDisponibleFincimex()
    {
        return $this->disponibleFincimex;
    }

    /**
     * Set saldoFincimex.
     *
     * @param string|null $saldoFincimex
     *
     * @return DemandaCombustible
     */
    public function setSaldoFincimex($saldoFincimex = null)
    {
        $this->saldoFincimex = $saldoFincimex;
    
        return $this;
    }

    /**
     * Get saldoFincimex.
     *
     * @return string|null
     */
    public function getSaldoFincimex()
    {
        return $this->saldoFincimex;
    }

    /**
     * Set saldoCaja.
     *
     * @param string|null $saldoCaja
     *
     * @return DemandaCombustible
     */
    public function setSaldoCaja($saldoCaja = null)
    {
        $this->saldoCaja = $saldoCaja;
    
        return $this;
    }

    /**
     * Get saldoCaja.
     *
     * @return string|null
     */
    public function getSaldoCaja()
    {
        return $this->saldoCaja;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return DemandaCombustible
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
     * Set combPlanificado.
     *
     * @param string|null $combPlanificado
     *
     * @return DemandaCombustible
     */
    public function setCombPlanificado($combPlanificado = null)
    {
        $this->combPlanificado = $combPlanificado;
    
        return $this;
    }

    /**
     * Get combPlanificado.
     *
     * @return string|null
     */
    public function getCombPlanificado()
    {
        return $this->combPlanificado;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return DemandaCombustible
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
     * @return DemandaCombustible
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

    /**
     * Set tipoCombustible.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible|null $tipoCombustible
     *
     * @return DemandaCombustible
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
}
