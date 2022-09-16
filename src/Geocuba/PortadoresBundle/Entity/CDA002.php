<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CDA002
 *
 * @ORM\Table(name="datos.cda002", indexes={@ORM\Index(name="IDX_524CD939B00B2B2D", columns={"moneda"})}, indexes={@ORM\Index(name="IDX_524CD9393C7CEFFE", columns={"nunidadid"}), @ORM\Index(name="IDX_524CD9398D824A80", columns={"nactividadid"}), @ORM\Index(name="IDX_524CD9393CEDFEDC", columns={"num_nivel_actividadid"}), @ORM\Index(name="IDX_524CD9396E1E0CD2", columns={"portador"}), @ORM\Index(name="IDX_524CD939E4616150", columns={"descuento_sobreconsumoid"}), @ORM\Index(name="IDX_524CD939DBDB9A27", columns={"descuento_deterioroid"}), @ORM\Index(name="IDX_524CD9397E860E87", columns={"descuento_bajoid"})})
 * @ORM\Entity
 */
class CDA002
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
     * @ORM\Column(name="codigo", type="string", length=255, nullable=true)
     */
    private $codigo;

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_actividad", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActividad;

    /**
     * @var float
     *
     * @ORM\Column(name="consumo", type="float", precision=10, scale=0, nullable=true)
     */
    private $consumo;

    /**
     * @var float
     *
     * @ORM\Column(name="indice", type="float", precision=10, scale=0, nullable=true)
     */
    private $indice;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", length=255, nullable=true)
     */
    private $mes;

    /**
     * @var float
     *
     * @ORM\Column(name="codigo_gae", type="float", precision=10, scale=0, nullable=true)
     */
    private $codigoGae;

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_actividad_acum", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActividadAcum;

    /**
     * @var float
     *
     * @ORM\Column(name="consumo_acum", type="float", precision=10, scale=0, nullable=true)
     */
    private $consumoAcum;

    /**
     * @var float
     *
     * @ORM\Column(name="indice_acum", type="float", precision=10, scale=0, nullable=true)
     */
    private $indiceAcum;

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_actividad_plan", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $nivelActividadPlan;

    /**
     * @var decimal
     *
     * @ORM\Column(name="consumo_plan", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $consumoPlan;

    /**
     * @var decimal
     *
     * @ORM\Column(name="indice_plan", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $indicePlan;

    /**
     * @var decimal
     *
     * @ORM\Column(name="indice_anual", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $indiceAnual = 0;

    /**
     * @var decimal
     *
     * @ORM\Column(name="relacion_real_plan", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $relacionRealPlan;

    /**
     * @var decimal
     *
     * @ORM\Column(name="relacion_acum_aprob", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $relacionAcumAprob = 0;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;

    /**
     * @var \Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nactividadid", referencedColumnName="id")
     * })
     */
    private $nactividadid;

    /**
     * @var \UMNivelActividad
     *
     * @ORM\ManyToOne(targetEntity="UMNivelActividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="num_nivel_actividadid", referencedColumnName="id")
     * })
     */
    private $numNivelActividadid;

    /**
     * @var \Portador
     *
     * @ORM\ManyToOne(targetEntity="Portador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portador", referencedColumnName="id")
     * })
     */
    private $portador;

    /**
     * @var \DescuentoSobreconsumo
     *
     * @ORM\ManyToOne(targetEntity="DescuentoSobreconsumo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="descuento_sobreconsumoid", referencedColumnName="id")
     * })
     */
    private $descuentoSobreconsumoid;

    /**
     * @var \DescuentoDeterioro
     *
     * @ORM\ManyToOne(targetEntity="DescuentoDeterioro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="descuento_deterioroid", referencedColumnName="id")
     * })
     */
    private $descuentoDeterioroid;

    /**
     * @var \DescuentoBajo
     *
     * @ORM\ManyToOne(targetEntity="DescuentoBajo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="descuento_bajoid", referencedColumnName="id")
     * })
     */
    private $descuentoBajoid;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

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
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return CDA002
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
     * Set nivelActividad
     *
     * @param float $nivelActividad
     *
     * @return CDA002
     */
    public function setNivelActividad($nivelActividad)
    {
        $this->nivelActividad = $nivelActividad;

        return $this;
    }

    /**
     * Get nivelActividad
     *
     * @return float
     */
    public function getNivelActividad()
    {
        return $this->nivelActividad;
    }

    /**
     * Set consumo
     *
     * @param float $consumo
     *
     * @return CDA002
     */
    public function setConsumo($consumo)
    {
        $this->consumo = $consumo;

        return $this;
    }

    /**
     * Get consumo
     *
     * @return float
     */
    public function getConsumo()
    {
        return $this->consumo;
    }

    /**
     * Set indice
     *
     * @param float $indice
     *
     * @return CDA002
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;

        return $this;
    }

    /**
     * Get indice
     *
     * @return float
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * Set mes
     *
     * @param string $mes
     *
     * @return CDA002
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set codigoGae
     *
     * @param float $codigoGae
     *
     * @return CDA002
     */
    public function setCodigoGae($codigoGae)
    {
        $this->codigoGae = $codigoGae;

        return $this;
    }

    /**
     * Get codigoGae
     *
     * @return float
     */
    public function getCodigoGae()
    {
        return $this->codigoGae;
    }

    /**
     * Set nivelActividadAcum
     *
     * @param float $nivelActividadAcum
     *
     * @return CDA002
     */
    public function setNivelActividadAcum($nivelActividadAcum)
    {
        $this->nivelActividadAcum = $nivelActividadAcum;

        return $this;
    }

    /**
     * Get nivelActividadAcum
     *
     * @return float
     */
    public function getNivelActividadAcum()
    {
        return $this->nivelActividadAcum;
    }

    /**
     * Set consumoAcum
     *
     * @param float $consumoAcum
     *
     * @return CDA002
     */
    public function setConsumoAcum($consumoAcum)
    {
        $this->consumoAcum = $consumoAcum;

        return $this;
    }

    /**
     * Get consumoAcum
     *
     * @return float
     */
    public function getConsumoAcum()
    {
        return $this->consumoAcum;
    }

    /**
     * Set indiceAcum
     *
     * @param float $indiceAcum
     *
     * @return CDA002
     */
    public function setIndiceAcum($indiceAcum)
    {
        $this->indiceAcum = $indiceAcum;

        return $this;
    }

    /**
     * Get indiceAcum
     *
     * @return float
     */
    public function getIndiceAcum()
    {
        return $this->indiceAcum;
    }

    /**
     * Set nivelActividadPlan
     *
     * @param decimal $nivelActividadPlan
     *
     * @return CDA002
     */
    public function setNivelActividadPlan($nivelActividadPlan)
    {
        $this->nivelActividadPlan = $nivelActividadPlan;

        return $this;
    }

    /**
     * Get nivelActividadPlan
     *
     * @return decimal
     */
    public function getNivelActividadPlan()
    {
        return $this->nivelActividadPlan;
    }

    /**
     * Set consumoPlan
     *
     * @param float $consumoPlan
     *
     * @return CDA002
     */
    public function setConsumoPlan($consumoPlan)
    {
        $this->consumoPlan = $consumoPlan;

        return $this;
    }

    /**
     * Get consumoPlan
     *
     * @return decimal
     */
    public function getConsumoPlan()
    {
        return $this->consumoPlan;
    }

    /**
     * Set indicePlan
     *
     * @param decimal $indicePlan
     *
     * @return CDA002
     */
    public function setIndicePlan($indicePlan)
    {
        $this->indicePlan = $indicePlan;

        return $this;
    }

    /**
     * Get indicePlan
     *
     * @return float
     */
    public function getIndicePlan()
    {
        return $this->indicePlan;
    }

    /**
     * Set indiceAnual
     *
     * @param decimal $indiceAnual
     *
     * @return CDA002
     */
    public function setIndiceAnual($indiceAnual)
    {
        $this->indiceAnual= $indiceAnual;

        return $this;
    }

    /**
     * Get indiceAnual
     *
     * @return float
     */
    public function getIndiceAnual()
    {
        return $this->indiceAnual;
    }

    /**
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return CDA002
     */
    public function setNunidadid(\Geocuba\PortadoresBundle\Entity\Unidad $nunidadid = null)
    {
        $this->nunidadid = $nunidadid;

        return $this;
    }

    /**
     * Get nunidadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getNunidadid()
    {
        return $this->nunidadid;
    }

    /**
     * Set nactividadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Actividad $nactividadid
     *
     * @return CDA002
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

    /**
     * Set numNivelActividadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\UMNivelActividad $numNivelActividadid
     *
     * @return CDA002
     */
    public function setNumNivelActividadid(\Geocuba\PortadoresBundle\Entity\UMNivelActividad $numNivelActividadid = null)
    {
        $this->numNivelActividadid = $numNivelActividadid;

        return $this;
    }

    /**
     * Get numNivelActividadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\UMNivelActividad
     */
    public function getNumNivelActividadid()
    {
        return $this->numNivelActividadid;
    }

    /**
     * Set portador
     *
     * @param \Geocuba\PortadoresBundle\Entity\Portador $portador
     *
     * @return CDA002
     */
    public function setPortador(\Geocuba\PortadoresBundle\Entity\Portador $portador = null)
    {
        $this->portador = $portador;

        return $this;
    }

    /**
     * Get portador
     *
     * @return \Geocuba\PortadoresBundle\Entity\Portador
     */
    public function getPortador()
    {
        return $this->portador;
    }

    /**
     * Set descuentoSobreconsumoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\DescuentoSobreconsumo $descuentoSobreconsumoid
     *
     * @return CDA002
     */
    public function setDescuentoSobreconsumoid(\Geocuba\PortadoresBundle\Entity\DescuentoSobreconsumo $descuentoSobreconsumoid = null)
    {
        $this->descuentoSobreconsumoid = $descuentoSobreconsumoid;

        return $this;
    }

    /**
     * Get descuentoSobreconsumoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\DescuentoSobreconsumo
     */
    public function getDescuentoSobreconsumoid()
    {
        return $this->descuentoSobreconsumoid;
    }

    /**
     * Set descuentoDeterioroid
     *
     * @param \Geocuba\PortadoresBundle\Entity\DescuentoDeterioro $descuentoDeterioroid
     *
     * @return CDA002
     */
    public function setDescuentoDeterioroid(\Geocuba\PortadoresBundle\Entity\DescuentoDeterioro $descuentoDeterioroid = null)
    {
        $this->descuentoDeterioroid = $descuentoDeterioroid;

        return $this;
    }

    /**
     * Get descuentoDeterioroid
     *
     * @return \Geocuba\PortadoresBundle\Entity\DescuentoDeterioro
     */
    public function getDescuentoDeterioroid()
    {
        return $this->descuentoDeterioroid;
    }

    /**
     * Set descuentoBajoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\DescuentoBajo $descuentoBajoid
     *
     * @return CDA002
     */
    public function setDescuentoBajoid(\Geocuba\PortadoresBundle\Entity\DescuentoBajo $descuentoBajoid = null)
    {
        $this->descuentoBajoid = $descuentoBajoid;

        return $this;
    }

    /**
     * Get descuentoBajoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\DescuentoBajo
     */
    public function getDescuentoBajoid()
    {
        return $this->descuentoBajoid;
    }



    /**
     * Set anno
     *
     * @param integer $anno
     *
     * @return CDA002
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return integer
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set relacionRealPlan
     *
     * @param decimal $relacionRealPlan
     *
     * @return CDA002
     */
    public function setRelacionRealPlan($relacionRealPlan)
    {
        $this->relacionRealPlan = $relacionRealPlan;

        return $this;
    }

    /**
     * Get relacionRealPlan
     *
     * @return decimal
     */
    public function getRelacionRealPlan()
    {
        return $this->relacionRealPlan;
    }

    /**
     * Set relacionAcumAprob
     *
     * @param decimal $relacionAcumAprob
     *
     * @return CDA002
     */
    public function setRelacionAcumAprob($relacionAcumAprob)
    {
        $this->relacionAcumAprob = $relacionAcumAprob;

        return $this;
    }

    /**
     * Get relacionAcumAprob
     *
     * @return decimal
     */
    public function getRelacionAcumAprob()
    {
        return $this->relacionAcumAprob;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return CDA002
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
