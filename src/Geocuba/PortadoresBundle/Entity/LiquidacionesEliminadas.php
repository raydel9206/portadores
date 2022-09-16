<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LiquidacionesEliminadas
 *
 * @ORM\Table(name="datos.liquidaciones_eliminadas", indexes={@ORM\Index(name="IDX_7A58C2058D824A80", columns={"nactividadid"}), @ORM\Index(name="IDX_7A58C2051B1370EF", columns={"ncentrocostoid"}), @ORM\Index(name="IDX_7A58C205771F8174", columns={"nfamiliaid"}), @ORM\Index(name="IDX_7A58C205C5233BC6", columns={"npersonaid"}), @ORM\Index(name="IDX_7A58C20549F0FCB4", columns={"nservicentroid"}), @ORM\Index(name="IDX_7A58C205EA6F2C9C", columns={"nvehiculoid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\LiquidacionesEliminadasRepository")
 */
class LiquidacionesEliminadas
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
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ntarjetaid", referencedColumnName="id")
     * })
     */
    private $ntarjetaid;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_vale", type="string", length=30, nullable=false)
     */
    private $nroVale;

    /**
     * @var string|null
     *
     * @ORM\Column(name="importe", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $importe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="importe_inicial", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $importeInicial;

    /**
     * @var string|null
     *
     * @ORM\Column(name="importe_final", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $importeFinal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cant_litros", type="decimal", precision=12, scale=2, nullable=true)
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
     * @var string|null
     *
     * @ORM\Column(name="anticipo", type="string", nullable=true)
     */
    private $anticipo;

    /**
     * @var bool
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(name="nunidadid", type="string", length=255, nullable=false)
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
     * @var \CentroCosto
     *
     * @ORM\ManyToOne(targetEntity="CentroCosto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ncentrocostoid", referencedColumnName="id")
     * })
     */
    private $ncentrocostoid;

    /**
     * @var \Familia
     *
     * @ORM\ManyToOne(targetEntity="Familia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nfamiliaid", referencedColumnName="id")
     * })
     */
    private $nfamiliaid;

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
     * @var \Servicentro
     *
     * @ORM\ManyToOne(targetEntity="Servicentro")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nservicentroid", referencedColumnName="id")
     * })
     */
    private $nservicentroid;

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
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ntarjetaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid
     *
     * @return LiquidacionesEliminadas
     */
    public function setNTarjetaid(\Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid = null)
    {
        $this->ntarjetaid = $ntarjetaid;

        return $this;
    }

    /**
     * Get ntarjetaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getNTarjetaid()
    {
        return $this->ntarjetaid;
    }

    /**
     * Set nroVale.
     *
     * @param string $nroVale
     *
     * @return LiquidacionesEliminadas
     */
    public function setNroVale($nroVale)
    {
        $this->nroVale = $nroVale;
    
        return $this;
    }

    /**
     * Get nroVale.
     *
     * @return string
     */
    public function getNroVale()
    {
        return $this->nroVale;
    }

    /**
     * Set importe.
     *
     * @param string|null $importe
     *
     * @return LiquidacionesEliminadas
     */
    public function setImporte($importe = null)
    {
        $this->importe = $importe;
    
        return $this;
    }

    /**
     * Get importe.
     *
     * @return string|null
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set importeInicial.
     *
     * @param string|null $importeInicial
     *
     * @return LiquidacionesEliminadas
     */
    public function setImporteInicial($importeInicial = null)
    {
        $this->importeInicial = $importeInicial;
    
        return $this;
    }

    /**
     * Get importeInicial.
     *
     * @return string|null
     */
    public function getImporteInicial()
    {
        return $this->importeInicial;
    }

    /**
     * Set importeFinal.
     *
     * @param string|null $importeFinal
     *
     * @return LiquidacionesEliminadas
     */
    public function setImporteFinal($importeFinal = null)
    {
        $this->importeFinal = $importeFinal;
    
        return $this;
    }

    /**
     * Get importeFinal.
     *
     * @return string|null
     */
    public function getImporteFinal()
    {
        return $this->importeFinal;
    }

    /**
     * Set cantLitros.
     *
     * @param string|null $cantLitros
     *
     * @return LiquidacionesEliminadas
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
     * Set fechaVale.
     *
     * @param \DateTime $fechaVale
     *
     * @return LiquidacionesEliminadas
     */
    public function setFechaVale($fechaVale)
    {
        $this->fechaVale = $fechaVale;
    
        return $this;
    }

    /**
     * Get fechaVale.
     *
     * @return \DateTime
     */
    public function getFechaVale()
    {
        return $this->fechaVale;
    }

    /**
     * Set fechaRegistro.
     *
     * @param \DateTime $fechaRegistro
     *
     * @return LiquidacionesEliminadas
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;
    
        return $this;
    }

    /**
     * Get fechaRegistro.
     *
     * @return \DateTime
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set anticipo.
     *
     * @param string|null $anticipo
     *
     * @return LiquidacionesEliminadas
     */
    public function setAnticipo($anticipo = null)
    {
        $this->anticipo = $anticipo;
    
        return $this;
    }

    /**
     * Get anticipo.
     *
     * @return string|null
     */
    public function getAnticipo()
    {
        return $this->anticipo;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return LiquidacionesEliminadas
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
     * @param string $nunidadid
     *
     * @return LiquidacionesEliminadas
     */
    public function setNunidadid($nunidadid)
    {
        $this->nunidadid = $nunidadid;
    
        return $this;
    }

    /**
     * Get nunidadid.
     *
     * @return string
     */
    public function getNunidadid()
    {
        return $this->nunidadid;
    }

    /**
     * Set nactividadid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Actividad|null $nactividadid
     *
     * @return LiquidacionesEliminadas
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
     * Set ncentrocostoid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\CentroCosto|null $ncentrocostoid
     *
     * @return LiquidacionesEliminadas
     */
    public function setNcentrocostoid(\Geocuba\PortadoresBundle\Entity\CentroCosto $ncentrocostoid = null)
    {
        $this->ncentrocostoid = $ncentrocostoid;
    
        return $this;
    }

    /**
     * Get ncentrocostoid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\CentroCosto|null
     */
    public function getNcentrocostoid()
    {
        return $this->ncentrocostoid;
    }

    /**
     * Set nfamiliaid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Familia|null $nfamiliaid
     *
     * @return LiquidacionesEliminadas
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

    /**
     * Set npersonaid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona|null $npersonaid
     *
     * @return LiquidacionesEliminadas
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
     * Set nservicentroid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Servicentro|null $nservicentroid
     *
     * @return LiquidacionesEliminadas
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
     * Set nvehiculoid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo|null $nvehiculoid
     *
     * @return LiquidacionesEliminadas
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
}
