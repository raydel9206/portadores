<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Liquidacion
 *
 * @ORM\Table(name="datos.liquidacion", indexes={@ORM\Index(name="IDX_85CBEB034D489839", columns={"ntarjetaid"}), @ORM\Index(name="IDX_85CBEB038D824A80", columns={"nactividadid"}), @ORM\Index(name="IDX_85CBEB03EA6F2C9C", columns={"nvehiculoid"}), @ORM\Index(name="IDX_85CBEB0349F0FCB4", columns={"nservicentroid"}), @ORM\Index(name="IDX_85CBEB03C5233BC6", columns={"npersonaid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\LiquidacionRepository")
 */
class Liquidacion
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
     * @var integer
     *
     * @ORM\Column(name="nro_vale", type="bigint", nullable=false)
     */
    private $nroVale;

    /**
     * @var string
     *
     * @ORM\Column(name="importe", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $importe;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_inicial", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $importeInicial;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_final", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $importeFinal;

    /**
     * @var string
     *
     * @ORM\Column(name="cant_litros", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $cantLitros;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vale", type="datetime", nullable=false)
     */
    private $fechaVale;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_registro", type="date", nullable=false)
     */
    private $fechaRegistro;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ntarjetaid", referencedColumnName="id")
     * })
     */
    private $ntarjetaid;

    /**
     * @var Subactividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nactividadid", referencedColumnName="id")
     * })
     */
    private $nactividadid;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nvehiculoid", referencedColumnName="id")
     * })
     */
    private $nvehiculoid;

    /**
     * @var \Servicentro
     *
     * @ORM\ManyToOne(targetEntity="Servicentro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nservicentroid", referencedColumnName="id")
     * })
     */
    private $nservicentroid;

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
     * @var \Anticipo
     *
     * @ORM\ManyToOne(targetEntity="Anticipo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="anticipo", referencedColumnName="id")
     * })
     */
    private $anticipo;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;

    /**
     * @var CentroCosto
     *
     * @ORM\ManyToOne(targetEntity="CentroCosto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ncentrocostoid", referencedColumnName="id")
     * })
     */
    private $ncentrocostoid;

    /**
     * @var Familia
     *
     * @ORM\ManyToOne(targetEntity="Familia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nfamiliaid", referencedColumnName="id")
     * })
     */
    private $nfamiliaid;

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
     * Set nroVale
     *
     * @param integer $nroVale
     *
     * @return Liquidacion
     */
    public function setNroVale($nroVale)
    {
        $this->nroVale = $nroVale;

        return $this;
    }

    /**
     * Get nroVale
     *
     * @return integer
     */
    public function getNroVale()
    {
        return $this->nroVale;
    }

    /**
     * Set importe
     *
     * @param string $importe
     *
     * @return Liquidacion
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set importeInicial
     *
     * @param string $importeInicial
     *
     * @return Liquidacion
     */
    public function setImporteInicial($importeInicial)
    {
        $this->importeInicial = $importeInicial;

        return $this;
    }

    /**
     * Get importeInicial
     *
     * @return string
     */
    public function getImporteInicial()
    {
        return $this->importeInicial;
    }

    /**
     * Set importeFinal
     *
     * @param string $importeFinal
     *
     * @return Liquidacion
     */
    public function setImporteFinal($importeFinal)
    {
        $this->importeFinal = $importeFinal;

        return $this;
    }

    /**
     * Get importeFinal
     *
     * @return string
     */
    public function getImporteFinal()
    {
        return $this->importeFinal;
    }

    /**
     * Set cantLitros
     *
     * @param string $cantLitros
     *
     * @return Liquidacion
     */
    public function setCantLitros($cantLitros)
    {
        $this->cantLitros = $cantLitros;

        return $this;
    }

    /**
     * Get cantLitros
     *
     * @return string
     */
    public function getCantLitros()
    {
        return $this->cantLitros;
    }

    /**
     * Set fechaVale
     *
     * @param \DateTime $fechaVale
     *
     * @return Liquidacion
     */
    public function setFechaVale($fechaVale)
    {
        $this->fechaVale = $fechaVale;

        return $this;
    }

    /**
     * Get fechaVale
     *
     * @return \DateTime
     */
    public function getFechaVale()
    {
        return $this->fechaVale;
    }

    /**
     * Set fechaRegistro
     *
     * @param \DateTime $fechaRegistro
     *
     * @return Liquidacion
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    /**
     * Get fechaRegistro
     *
     * @return \DateTime
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set ntarjetaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid
     *
     * @return Liquidacion
     */
    public function setTarjetaid(\Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid = null)
    {
        $this->ntarjetaid = $ntarjetaid;

        return $this;
    }

    /**
     * Get ntarjetaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getTarjetaid()
    {
        return $this->ntarjetaid;
    }

    /**
     * Set nvehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid
     *
     * @return Liquidacion
     */
    public function setVehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid = null)
    {
        $this->nvehiculoid = $nvehiculoid;

        return $this;
    }

    /**
     * Get nvehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getVehiculoid()
    {
        return $this->nvehiculoid;
    }

    /**
     * Set nservicentroid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Servicentro $nservicentroid
     *
     * @return Liquidacion
     */
    public function setServicentroid(\Geocuba\PortadoresBundle\Entity\Servicentro $nservicentroid = null)
    {
        $this->nservicentroid = $nservicentroid;

        return $this;
    }

    /**
     * Get nservicentroid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Servicentro
     */
    public function getServicentroid()
    {
        return $this->nservicentroid;
    }

    /**
     * Set npersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $npersonaid
     *
     * @return Liquidacion
     */
    public function setPersonaid(\Geocuba\PortadoresBundle\Entity\Persona $npersonaid = null)
    {
        $this->npersonaid = $npersonaid;

        return $this;
    }

    /**
     * Get npersonaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getPersonaid()
    {
        return $this->npersonaid;
    }

    /**
     * Set anticipo
     *
     * @param \Geocuba\PortadoresBundle\Entity\Anticipo $anticipo
     *
     * @return Liquidacion
     */
    public function setAnticipo(\Geocuba\PortadoresBundle\Entity\Anticipo $anticipo = null)
    {
        $this->anticipo = $anticipo;

        return $this;
    }

    /**
     * Get anticipo
     *
     * @return \Geocuba\PortadoresBundle\Entity\Anticipo
     */
    public function getAnticipo()
    {
        return $this->anticipo;
    }

    /**
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return Liquidacion
     */
    public function setUnidadid(\Geocuba\PortadoresBundle\Entity\Unidad $nunidadid = null)
    {
        $this->nunidadid = $nunidadid;

        return $this;
    }

    /**
     * Get nunidadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getUnidadid()
    {
        return $this->nunidadid;
    }

    /**
     * Set ncentrocostoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\CentroCosto $ncentrocostoid
     *
     * @return Liquidacion
     */
    public function setNcentrocostoid(\Geocuba\PortadoresBundle\Entity\CentroCosto $ncentrocostoid = null)
    {
        $this->ncentrocostoid = $ncentrocostoid;

        return $this;
    }

    /**
     * Get ncentrocostoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\CentroCosto
     */
    public function getNcentrocostoid()
    {
        return $this->ncentrocostoid;
    }


    /**
     * Set ntarjetaid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta|null $ntarjetaid
     *
     * @return Liquidacion
     */
    public function setNtarjetaid(\Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid = null)
    {
        $this->ntarjetaid = $ntarjetaid;

        return $this;
    }

    /**
     * Get ntarjetaid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta|null
     */
    public function getNtarjetaid()
    {
        return $this->ntarjetaid;
    }

    /**
     * Set nactividadid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Actividad|null $nactividadid
     *
     * @return Liquidacion
     */
    public function setNactividadid(\Geocuba\PortadoresBundle\Entity\Actividad $nactividadid = null)
    {
        $this->nactividadid = $nactividadid;

        return $this;
    }

    /**
     * Get nactividadid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Actividad|null
     */
    public function getNactividadid()
    {
        return $this->nactividadid;
    }

    /**
     * Set nvehiculoid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo|null $nvehiculoid
     *
     * @return Liquidacion
     */
    public function setNvehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid = null)
    {
        $this->nvehiculoid = $nvehiculoid;

        return $this;
    }

    /**
     * Get nvehiculoid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo|null
     */
    public function getNvehiculoid()
    {
        return $this->nvehiculoid;
    }

    /**
     * Set nservicentroid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Servicentro|null $nservicentroid
     *
     * @return Liquidacion
     */
    public function setNservicentroid(\Geocuba\PortadoresBundle\Entity\Servicentro $nservicentroid = null)
    {
        $this->nservicentroid = $nservicentroid;

        return $this;
    }

    /**
     * Get nservicentroid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Servicentro|null
     */
    public function getNservicentroid()
    {
        return $this->nservicentroid;
    }

    /**
     * Set npersonaid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona|null $npersonaid
     *
     * @return Liquidacion
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
     * Set nunidadid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $nunidadid
     *
     * @return Liquidacion
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

    /**
     * Set visible.
     *
     * @param bool|null $visible
     *
     * @return Liquidacion
     */
    public function setVisible($visible = null)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool|null
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set nfamiliaid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Familia|null $nfamiliaid
     *
     * @return Liquidacion
     */
    public function setNfamiliaid(\Geocuba\PortadoresBundle\Entity\Familia $nfamiliaid = null)
    {
        $this->nfamiliaid = $nfamiliaid;

        return $this;
    }

    /**
     * Get nfamiliaid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Familia|null
     */
    public function getNfamiliaid()
    {
        return $this->nfamiliaid;
    }
}
