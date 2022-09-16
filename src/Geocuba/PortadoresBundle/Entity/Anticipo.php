<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Anticipo
 *
 * @ORM\Table(name="datos.anticipo", indexes={@ORM\Index(name="IDX_7B7CA5FAC9FA1603", columns={"vehiculo"}), @ORM\Index(name="IDX_7B7CA5FAAE90B786", columns={"tarjeta"}), @ORM\Index(name="IDX_7B7CA5FAFDD6B80A", columns={"trabajo"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\AnticipoRepository")
 */
class Anticipo
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=150, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="no_vale", type="string", nullable=true)
     */
    private $noVale;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cierre", type="datetime", nullable=true)
     */
    private $fechaCierre;

    /**
     * @var float
     *
     * @ORM\Column(name="importe", type="float", precision=10, scale=0, nullable=true)
     */
    private $importe = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=0, nullable=true)
     */
    private $cantidad = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="transito", type="boolean", nullable=true)
     */
    private $transito;

    /**
     * @var boolean
     *
     * @ORM\Column(name="terceros", type="boolean", nullable=true)
     */
    private $terceros;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var boolean
     *
     * @ORM\Column(name="abierto", type="boolean", nullable=true)
     */
    private $abierto = true;

    /**
     * @var Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="npersonaid", referencedColumnName="id")
     * })
     */
    private $npersonaid;

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
     * @var boolean
     *
     * @ORM\Column(name="excepcional", type="boolean", nullable=true)
     */
    private $excepcional;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="text", nullable=true)
     */
    private $motivo;

//    /**
//     * @var Liquidacion
//     *
//     * @ORM\OneToMany(targetEntity="Liquidacion", mappedBy="anticipo")
//     */
//    private $liquidacion;

    /**
     * Get id
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set noVale
     *
     * @param string $noVale
     *
     * @return Anticipo
     */
    public function setNoVale($noVale): Anticipo
    {
        $this->noVale = $noVale;

        return $this;
    }

    /**
     * Get noVale
     *
     * @return string
     */
    public function getNoVale(): string
    {
        return $this->noVale;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Anticipo
     */
    public function setFecha($fecha): Anticipo
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha(): \DateTime
    {
        return $this->fecha;
    }

    /**
     * Set importe
     *
     * @param float $importe
     *
     * @return Anticipo
     */
    public function setImporte($importe): Anticipo
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return float
     */
    public function getImporte(): float
    {
        return $this->importe;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return Anticipo
     */
    public function setCantidad($cantidad): Anticipo
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad(): float
    {
        return $this->cantidad;
    }

    /**
     * Set transito
     *
     * @param boolean $transito
     *
     * @return Anticipo
     */
    public function setTransito($transito): Anticipo
    {
        $this->transito = $transito;

        return $this;
    }

    /**
     * Get transito
     *
     * @return boolean
     */
    public function getTransito(): bool
    {
        return $this->transito;
    }

    /**
     * Set terceros
     *
     * @param boolean $terceros
     *
     * @return Anticipo
     */
    public function setTerceros($terceros): Anticipo
    {
        $this->terceros = $terceros;

        return $this;
    }

    /**
     * Get terceros
     *
     * @return boolean
     */
    public function getTerceros(): bool
    {
        return $this->terceros;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Anticipo
     */
    public function setVisible($visible): Anticipo
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Set vehiculo
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculo
     *
     * @return Anticipo
     */
    public function setVehiculo(\Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculo = null): Anticipo
    {
        $this->vehiculo = $vehiculo;

        return $this;
    }

    /**
     * Get vehiculo
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getVehiculo(): Vehiculo
    {
        return $this->vehiculo;
    }

    /**
     * Set tarjeta
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $tarjeta
     *
     * @return Anticipo
     */
    public function setTarjeta(\Geocuba\PortadoresBundle\Entity\Tarjeta $tarjeta = null): Anticipo
    {
        $this->tarjeta = $tarjeta;

        return $this;
    }

    /**
     * Get tarjeta
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getTarjeta(): Tarjeta
    {
        return $this->tarjeta;
    }

    /**
     * Set trabajo
     *
     * @param \Geocuba\PortadoresBundle\Entity\Trabajo $trabajo
     *
     * @return Anticipo
     */
    public function setTrabajo(\Geocuba\PortadoresBundle\Entity\Trabajo $trabajo = null): Anticipo
    {
        $this->trabajo = $trabajo;

        return $this;
    }

    /**
     * Get trabajo
     *
     * @return \Geocuba\PortadoresBundle\Entity\Trabajo
     */
    public function getTrabajo()
    {
        return $this->trabajo;
    }

    /**
     * Set abierto
     *
     * @param boolean $abierto
     *
     * @return Anticipo
     */
    public function setAbierto($abierto): Anticipo
    {
        $this->abierto = $abierto;

        return $this;
    }

    /**
     * Get abierto
     *
     * @return boolean
     */
    public function getAbierto(): bool
    {
        return $this->abierto;
    }

    /**
     * Set npersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $npersonaid
     *
     * @return Anticipo
     */
    public function setNpersonaid(\Geocuba\PortadoresBundle\Entity\Persona $npersonaid = null): Anticipo
    {
        $this->npersonaid = $npersonaid;

        return $this;
    }

    /**
     * Get npersonaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getNpersonaid(): Persona
    {
        return $this->npersonaid;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->liquidacion = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add liquidacion
     *
     * @param \Geocuba\PortadoresBundle\Entity\Liquidacion $liquidacion
     *
     * @return Anticipo
     */
    public function addLiquidacion(\Geocuba\PortadoresBundle\Entity\Liquidacion $liquidacion): Anticipo
    {
        $this->liquidacion[] = $liquidacion;

        return $this;
    }

    /**
     * Remove liquidacion
     *
     * @param \Geocuba\PortadoresBundle\Entity\Liquidacion $liquidacion
     */
    public function removeLiquidacion(\Geocuba\PortadoresBundle\Entity\Liquidacion $liquidacion): void
    {
        $this->liquidacion->removeElement($liquidacion);
    }

    /**
     * Get liquidacion
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLiquidacion()
    {
        return $this->liquidacion;
    }

    /**
     * Set fechaCierre
     *
     * @param \DateTime $fechaCierre
     *
     * @return Anticipo
     */
    public function setFechaCierre($fechaCierre): Anticipo
    {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }

    /**
     * Get fechaCierre
     *
     * @return \DateTime
     */
    public function getFechaCierre(): \DateTime
    {
        return $this->fechaCierre;
    }

    /**
     * Set excepcional.
     *
     * @param bool|null $excepcional
     *
     * @return Anticipo
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
     * @return Anticipo
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
}
