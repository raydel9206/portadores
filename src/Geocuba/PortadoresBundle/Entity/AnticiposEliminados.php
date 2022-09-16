<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnticiposEliminados
 *
 * @ORM\Table(name="datos.anticipos_eliminados", indexes={@ORM\Index(name="IDX_35DC8C13C5233BC6", columns={"npersonaid"}), @ORM\Index(name="IDX_35DC8C13AE90B786", columns={"tarjeta"}), @ORM\Index(name="IDX_35DC8C13FDD6B80A", columns={"trabajo"}), @ORM\Index(name="IDX_35DC8C13C9FA1603", columns={"vehiculo"}), @ORM\Index(name="IDX_35DC8C133ADCBF62", columns={"anticipo"})})
 * @ORM\Entity
 */
class AnticiposEliminados
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
     * @ORM\Column(name="no_vale", type="string", nullable=false)
     */
    private $noVale;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="float", precision=10, scale=0, nullable=false)
     */
    private $importe;

    /**
     * @var int|null
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

    /**
     * @var int|null
     *
     * @ORM\Column(name="mes", type="integer", nullable=true)
     */
    private $mes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="consecutivo", type="integer", nullable=true)
     */
    private $consecutivo;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=0, nullable=false)
     */
    private $cantidad;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="transito", type="boolean", nullable=true)
     */
    private $transito;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="terceros", type="boolean", nullable=true)
     */
    private $terceros;

    /**
     * @var bool
     *
     * @ORM\Column(name="to_restore", type="boolean", nullable=false)
     */
    private $toRestore = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="abierto", type="boolean", nullable=false)
     */
    private $abierto;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_cierre", type="datetime", nullable=true)
     */
    private $fechaCierre;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="excepcional", type="boolean", nullable=true)
     */
    private $excepcional;

    /**
     * @var string|null
     *
     * @ORM\Column(name="motivo", type="text", nullable=true)
     */
    private $motivo;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="npersonaid", referencedColumnName="id")
     * })
     */
    private $npersonaid;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tarjeta", referencedColumnName="id")
     * })
     */
    private $tarjeta;

    /**
     * @var \Trabajo
     *
     * @ORM\ManyToOne(targetEntity="Trabajo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="trabajo", referencedColumnName="id")
     * })
     */
    private $trabajo;

    /**
     * @var string
     *
     * @ORM\Column(name="anticipo", type="string", nullable=false)
     */
    private $anticipo;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehiculo", referencedColumnName="id")
     * })
     */
    private $vehiculo;



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
     * Set noVale.
     *
     * @param string $noVale
     *
     * @return AnticiposEliminados
     */
    public function setNoVale($noVale)
    {
        $this->noVale = $noVale;
    
        return $this;
    }

    /**
     * Get noVale.
     *
     * @return string
     */
    public function getNoVale()
    {
        return $this->noVale;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return AnticiposEliminados
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
     * Set importe.
     *
     * @param float $importe
     *
     * @return AnticiposEliminados
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;
    
        return $this;
    }

    /**
     * Get importe.
     *
     * @return float
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set anno.
     *
     * @param int|null $anno
     *
     * @return AnticiposEliminados
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
     * Set mes.
     *
     * @param int|null $mes
     *
     * @return AnticiposEliminados
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
     * Set consecutivo.
     *
     * @param int|null $consecutivo
     *
     * @return AnticiposEliminados
     */
    public function setConsecutivo($consecutivo = null)
    {
        $this->consecutivo = $consecutivo;
    
        return $this;
    }

    /**
     * Get consecutivo.
     *
     * @return int|null
     */
    public function getConsecutivo()
    {
        return $this->consecutivo;
    }

    /**
     * Set cantidad.
     *
     * @param float $cantidad
     *
     * @return AnticiposEliminados
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;
    
        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set transito.
     *
     * @param bool|null $transito
     *
     * @return AnticiposEliminados
     */
    public function setTransito($transito = null)
    {
        $this->transito = $transito;
    
        return $this;
    }

    /**
     * Get transito.
     *
     * @return bool|null
     */
    public function getTransito()
    {
        return $this->transito;
    }

    /**
     * Set terceros.
     *
     * @param bool|null $terceros
     *
     * @return AnticiposEliminados
     */
    public function setTerceros($terceros = null)
    {
        $this->terceros = $terceros;
    
        return $this;
    }

    /**
     * Get terceros.
     *
     * @return bool|null
     */
    public function getTerceros()
    {
        return $this->terceros;
    }

    /**
     * Set toRestore.
     *
     * @param bool $toRestore
     *
     * @return AnticiposEliminados
     */
    public function setToRestore($toRestore)
    {
        $this->toRestore = $toRestore;
    
        return $this;
    }

    /**
     * Get toRestore.
     *
     * @return bool
     */
    public function getToRestore()
    {
        return $this->toRestore;
    }

    /**
     * Set abierto.
     *
     * @param bool $abierto
     *
     * @return AnticiposEliminados
     */
    public function setAbierto($abierto)
    {
        $this->abierto = $abierto;
    
        return $this;
    }

    /**
     * Get abierto.
     *
     * @return bool
     */
    public function getAbierto()
    {
        return $this->abierto;
    }

    /**
     * Set fechaCierre.
     *
     * @param \DateTime|null $fechaCierre
     *
     * @return AnticiposEliminados
     */
    public function setFechaCierre($fechaCierre = null)
    {
        $this->fechaCierre = $fechaCierre;
    
        return $this;
    }

    /**
     * Get fechaCierre.
     *
     * @return \DateTime|null
     */
    public function getFechaCierre()
    {
        return $this->fechaCierre;
    }

    /**
     * Set excepcional.
     *
     * @param bool|null $excepcional
     *
     * @return AnticiposEliminados
     */
    public function setExcepcional($excepcional = null)
    {
        $this->excepcional = $excepcional;
    
        return $this;
    }

    /**
     * Get excepcional.
     *
     * @return bool|null
     */
    public function getExcepcional()
    {
        return $this->excepcional;
    }

    /**
     * Set motivo.
     *
     * @param string|null $motivo
     *
     * @return AnticiposEliminados
     */
    public function setMotivo($motivo = null)
    {
        $this->motivo = $motivo;
    
        return $this;
    }

    /**
     * Get motivo.
     *
     * @return string|null
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set npersonaid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona|null $npersonaid
     *
     * @return AnticiposEliminados
     */
    public function setNpersonaid(\Geocuba\PortadoresBundle\Entity\Persona $npersonaid = null)
    {
        $this->npersonaid = $npersonaid;
    
        return $this;
    }

    /**
     * Get npersonaid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona|null
     */
    public function getNpersonaid()
    {
        return $this->npersonaid;
    }

    /**
     * Set tarjeta.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta|null $tarjeta
     *
     * @return AnticiposEliminados
     */
    public function setTarjeta(\Geocuba\PortadoresBundle\Entity\Tarjeta $tarjeta = null)
    {
        $this->tarjeta = $tarjeta;
    
        return $this;
    }

    /**
     * Get tarjeta.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta|null
     */
    public function getTarjeta()
    {
        return $this->tarjeta;
    }

    /**
     * Set trabajo.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Trabajo|null $trabajo
     *
     * @return AnticiposEliminados
     */
    public function setTrabajo(\Geocuba\PortadoresBundle\Entity\Trabajo $trabajo = null)
    {
        $this->trabajo = $trabajo;
    
        return $this;
    }

    /**
     * Get trabajo.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Trabajo|null
     */
    public function getTrabajo()
    {
        return $this->trabajo;
    }

    /**
     * Set anticipo.
     *
     * @param string $anticipo
     *
     * @return AnticiposEliminados
     */
    public function setAnticipo($anticipo)
    {
        $this->anticipo = $anticipo;
    
        return $this;
    }

    /**
     * Get anticipo.
     *
     * @return string
     */
    public function getAnticipo()
    {
        return $this->anticipo;
    }

    /**
     * Set vehiculo.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo|null $vehiculo
     *
     * @return AnticiposEliminados
     */
    public function setVehiculo(\Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculo = null)
    {
        $this->vehiculo = $vehiculo;
    
        return $this;
    }

    /**
     * Get vehiculo.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo|null
     */
    public function getVehiculo()
    {
        return $this->vehiculo;
    }
}
