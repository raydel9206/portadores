<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Geocuba\AdminBundle\EventListener\DoctrineEventsSuscriber;

/**
 * Vehiculo
 *
 * @ORM\Table(name="nomencladores.vehiculo", indexes={@ORM\Index(name="IDX_3CD688188DF2BD06", columns={"actividad"}),
 *     @ORM\Index(name="IDX_3CD68818E30E3949", columns={"ndenominacion_vehiculoid"}),
 *     @ORM\Index(name="IDX_3CD6881892E8D99F", columns={"nestado_tecnicoid"}),
 *     @ORM\Index(name="IDX_3CD6881867417F7E", columns={"ntipo_combustibleid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\VehiculoRepository")
 */
class Vehiculo
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
     * @ORM\Column(name="matricula", type="string", length=255, nullable=false)
     */
    private $matricula;

    /**
     * @var string
     *
     * @ORM\Column(name="norma", type="decimal", precision=30, scale=16, nullable=false)
     */
    private $norma;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nro_inventario", type="string", length=255, nullable=true)
     */
    private $nroInventario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nro_serie_carreceria", type="string", length=255, nullable=true)
     */
    private $nroSerieCarreceria;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nro_serie_motor", type="string", length=255, nullable=true)
     */
    private $nroSerieMotor;

    /**
     * @var string|null
     *
     * @ORM\Column(name="color", type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_expiracion_circulacion", type="date", nullable=true)
     */
    private $fechaExpiracionCirculacion;

    /**
     * @var int|null
     *
     * @ORM\Column(name="anno_fabricacion", type="integer", nullable=true)
     */
    private $annoFabricacion;

    /**
     * @var bool
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nro_circulacion", type="string", nullable=true)
     */
    private $nroCirculacion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="norma_far", type="decimal", precision=30, scale=16, nullable=true)
     */
    private $normaFar = '0';

    /**
     * @var string|null
     *
     * @ORM\Column(name="norma_lubricante", type="decimal", precision=19, scale=4, nullable=true)
     */
    private $normaLubricante = '0';

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_expiracion_licencia_operativa", type="date", nullable=true)
     */
    private $fechaExpiracionLicenciaOperativa;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_expiracion_somaton", type="date", nullable=true)
     */
    private $fechaExpiracionSomaton;

    /**
     * @var int|null
     *
     * @ORM\Column(name="nro_orden", type="integer", nullable=true)
     */
    private $nroOrden;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="paralizado", type="boolean", nullable=true)
     */
    private $paralizado;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="odometro", type="boolean", nullable=true, options={"default"="1"})
     */
    private $odometro = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="norma_fabricante", type="decimal", precision=30, scale=16, nullable=true)
     */
    private $normaFabricante;

    /**
     * @var string|null
     *
     * @ORM\Column(name="factor", type="decimal", precision=3, scale=2, nullable=true)
     */
    private $factor;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="embarcacion", type="boolean", nullable=true)
     */
    private $embarcacion = false;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="equipo_tecn", type="boolean", nullable=true)
     */
    private $equipoTecn = false;

    /**
     * @var \Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="actividad", referencedColumnName="id")
     * })
     */
    private $actividad;

    /**
     * @var \ModeloVehiculo
     *
     * @ORM\ManyToOne(targetEntity="ModeloVehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nmodeloid", referencedColumnName="id")
     * })
     */
    private $nmodeloid;

    /**
     * @var \DenominacionVehiculo
     *
     * @ORM\ManyToOne(targetEntity="DenominacionVehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndenominacion_vehiculoid", referencedColumnName="id")
     * })
     */
    private $ndenominacionVehiculoid;

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
     * @var \EstadoTecnico
     *
     * @ORM\ManyToOne(targetEntity="EstadoTecnico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nestado_tecnicoid", referencedColumnName="id")
     * })
     */
    private $nestadoTecnicoid;

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
     * @var \Area
     *
     * @ORM\ManyToOne(targetEntity="Area")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="area", referencedColumnName="id")
     * })
     */
    private $area;

    /**
     * @var TarjetaVehiculo
     *
     * @ORM\OneToMany(targetEntity="TarjetaVehiculo", mappedBy="nvehiculoid")
     */
    private $tarjetas;

    /**
     * @var VehiculoPersona
     *
     * @ORM\OneToMany(targetEntity="VehiculoPersona", mappedBy="idvehiculo")
     */
    private $personas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->tarjetas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->personas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->paralizado = false;
    }


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
     * Set matricula.
     *
     * @param string $matricula
     *
     * @return Vehiculo
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get matricula.
     *
     * @return string
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set norma.
     *
     * @param string $norma
     *
     * @return Vehiculo
     */
    public function setNorma($norma)
    {
        $this->norma = $norma;

        return $this;
    }

    /**
     * Get norma.
     *
     * @return string
     */
    public function getNorma()
    {
        return $this->norma;
    }

    /**
     * Set nroInventario.
     *
     * @param string|null $nroInventario
     *
     * @return Vehiculo
     */
    public function setNroInventario($nroInventario = null)
    {
        $this->nroInventario = $nroInventario;

        return $this;
    }

    /**
     * Get nroInventario.
     *
     * @return string|null
     */
    public function getNroInventario()
    {
        return $this->nroInventario;
    }

    /**
     * Set nroSerieCarreceria.
     *
     * @param string|null $nroSerieCarreceria
     *
     * @return Vehiculo
     */
    public function setNroSerieCarreceria($nroSerieCarreceria = null)
    {
        $this->nroSerieCarreceria = $nroSerieCarreceria;

        return $this;
    }

    /**
     * Get nroSerieCarreceria.
     *
     * @return string|null
     */
    public function getNroSerieCarreceria()
    {
        return $this->nroSerieCarreceria;
    }

    /**
     * Set nroSerieMotor.
     *
     * @param string|null $nroSerieMotor
     *
     * @return Vehiculo
     */
    public function setNroSerieMotor($nroSerieMotor = null)
    {
        $this->nroSerieMotor = $nroSerieMotor;

        return $this;
    }

    /**
     * Get nroSerieMotor.
     *
     * @return string|null
     */
    public function getNroSerieMotor()
    {
        return $this->nroSerieMotor;
    }

    /**
     * Set color.
     *
     * @param string|null $color
     *
     * @return Vehiculo
     */
    public function setColor($color = null)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color.
     *
     * @return string|null
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set fechaExpiracionCirculacion.
     *
     * @param \DateTime|null $fechaExpiracionCirculacion
     *
     * @return Vehiculo
     */
    public function setFechaExpiracionCirculacion($fechaExpiracionCirculacion = null)
    {
        $this->fechaExpiracionCirculacion = $fechaExpiracionCirculacion;

        return $this;
    }

    /**
     * Get fechaExpiracionCirculacion.
     *
     * @return \DateTime|null
     */
    public function getFechaExpiracionCirculacion()
    {
        return $this->fechaExpiracionCirculacion;
    }

    /**
     * Set annoFabricacion.
     *
     * @param int|null $annoFabricacion
     *
     * @return Vehiculo
     */
    public function setAnnoFabricacion($annoFabricacion = null)
    {
        $this->annoFabricacion = $annoFabricacion;

        return $this;
    }

    /**
     * Get annoFabricacion.
     *
     * @return int|null
     */
    public function getAnnoFabricacion()
    {
        return $this->annoFabricacion;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return Vehiculo
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
     * Set nroCirculacion.
     *
     * @param string|null $nroCirculacion
     *
     * @return Vehiculo
     */
    public function setNroCirculacion($nroCirculacion = null)
    {
        $this->nroCirculacion = $nroCirculacion;

        return $this;
    }

    /**
     * Get nroCirculacion.
     *
     * @return string|null
     */
    public function getNroCirculacion()
    {
        return $this->nroCirculacion;
    }

    /**
     * Set normaFar.
     *
     * @param string|null $normaFar
     *
     * @return Vehiculo
     */
    public function setNormaFar($normaFar = null)
    {
        $this->normaFar = $normaFar;

        return $this;
    }

    /**
     * Get normaFar.
     *
     * @return string|null
     */
    public function getNormaFar()
    {
        return $this->normaFar;
    }

    /**
     * Set normaLubricante.
     *
     * @param string|null $normaLubricante
     *
     * @return Vehiculo
     */
    public function setNormaLubricante($normaLubricante = null)
    {
        $this->normaLubricante = $normaLubricante;

        return $this;
    }

    /**
     * Get normaLubricante.
     *
     * @return string|null
     */
    public function getNormaLubricante()
    {
        return $this->normaLubricante;
    }

    /**
     * Set fechaExpiracionLicenciaOperativa.
     *
     * @param \DateTime|null $fechaExpiracionLicenciaOperativa
     *
     * @return Vehiculo
     */
    public function setFechaExpiracionLicenciaOperativa($fechaExpiracionLicenciaOperativa = null)
    {
        $this->fechaExpiracionLicenciaOperativa = $fechaExpiracionLicenciaOperativa;

        return $this;
    }

    /**
     * Get fechaExpiracionLicenciaOperativa.
     *
     * @return \DateTime|null
     */
    public function getFechaExpiracionLicenciaOperativa()
    {
        return $this->fechaExpiracionLicenciaOperativa;
    }

    /**
     * Set fechaExpiracionSomaton.
     *
     * @param \DateTime|null $fechaExpiracionSomaton
     *
     * @return Vehiculo
     */
    public function setFechaExpiracionSomaton($fechaExpiracionSomaton = null)
    {
        $this->fechaExpiracionSomaton = $fechaExpiracionSomaton;

        return $this;
    }

    /**
     * Get fechaExpiracionSomaton.
     *
     * @return \DateTime|null
     */
    public function getFechaExpiracionSomaton()
    {
        return $this->fechaExpiracionSomaton;
    }

    /**
     * Set nroOrden.
     *
     * @param int|null $nroOrden
     *
     * @return Vehiculo
     */
    public function setNroOrden($nroOrden = null)
    {
        $this->nroOrden = $nroOrden;

        return $this;
    }

    /**
     * Get nroOrden.
     *
     * @return int|null
     */
    public function getNroOrden()
    {
        return $this->nroOrden;
    }

    /**
     * Set paralizado.
     *
     * @param bool|null $paralizado
     *
     * @return Vehiculo
     */
    public function setParalizado($paralizado = null)
    {
        $this->paralizado = $paralizado;

        return $this;
    }

    /**
     * Get paralizado.
     *
     * @return bool|null
     */
    public function getParalizado()
    {
        return $this->paralizado;
    }

    /**
     * Set odometro.
     *
     * @param bool|null $odometro
     *
     * @return Vehiculo
     */
    public function setOdometro($odometro = null)
    {
        $this->odometro = $odometro;

        return $this;
    }

    /**
     * Get odometro.
     *
     * @return bool|null
     */
    public function getOdometro()
    {
        return $this->odometro;
    }

    /**
     * Set normaFabricante.
     *
     * @param string|null $normaFabricante
     *
     * @return Vehiculo
     */
    public function setNormaFabricante($normaFabricante = null)
    {
        $this->normaFabricante = $normaFabricante;

        return $this;
    }

    /**
     * Get normaFabricante.
     *
     * @return string|null
     */
    public function getNormaFabricante()
    {
        return $this->normaFabricante;
    }


    /**
     * Set factor.
     *
     * @param string|null $factor
     *
     * @return Vehiculo
     */
    public function setFactor($factor = null)
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * Get factor.
     *
     * @return string|null
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set embarcacion.
     *
     * @param bool|null $embarcacion
     *
     * @return Vehiculo
     */
    public function setEmbarcacion($embarcacion = null)
    {
        $this->embarcacion = $embarcacion;

        return $this;
    }

    /**
     * Get embarcacion.
     *
     * @return bool|null
     */
    public function getEmbarcacion()
    {
        return $this->embarcacion;
    }

    /**
     * Set equipoTecn.
     *
     * @param bool|null $equipoTecn
     *
     * @return Vehiculo
     */
    public function setEquipoTecn($equipoTecn = null)
    {
        $this->equipoTecn = $equipoTecn;

        return $this;
    }

    /**
     * Get equipoTecn.
     *
     * @return bool|null
     */
    public function getEquipoTecn()
    {
        return $this->equipoTecn;
    }

    /**
     * Set actividad.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Actividad|null $actividad
     *
     * @return Vehiculo
     */
    public function setActividad(\Geocuba\PortadoresBundle\Entity\Actividad $actividad = null)
    {
        $this->actividad = $actividad;

        return $this;
    }

    /**
     * Get actividad.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Actividad|null
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set nmodeloid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\ModeloVehiculo|null $nmodeloid
     *
     * @return Vehiculo
     */
    public function setNmodeloid(\Geocuba\PortadoresBundle\Entity\ModeloVehiculo $nmodeloid = null)
    {
        $this->nmodeloid = $nmodeloid;

        return $this;
    }

    /**
     * Get nmodeloid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\ModeloVehiculo|null
     */
    public function getNmodeloid()
    {
        return $this->nmodeloid;
    }

    /**
     * Set ndenominacionVehiculoid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\DenominacionVehiculo|null $ndenominacionVehiculoid
     *
     * @return Vehiculo
     */
    public function setNdenominacionVehiculoid(\Geocuba\PortadoresBundle\Entity\DenominacionVehiculo $ndenominacionVehiculoid = null)
    {
        $this->ndenominacionVehiculoid = $ndenominacionVehiculoid;

        return $this;
    }

    /**
     * Get ndenominacionVehiculoid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\DenominacionVehiculo|null
     */
    public function getNdenominacionVehiculoid()
    {
        return $this->ndenominacionVehiculoid;
    }

    /**
     * Set nunidadid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $nunidadid
     *
     * @return Vehiculo
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
     * Set nestadoTecnicoid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\EstadoTecnico|null $nestadoTecnicoid
     *
     * @return Vehiculo
     */
    public function setNestadoTecnicoid(\Geocuba\PortadoresBundle\Entity\EstadoTecnico $nestadoTecnicoid = null)
    {
        $this->nestadoTecnicoid = $nestadoTecnicoid;

        return $this;
    }

    /**
     * Get nestadoTecnicoid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\EstadoTecnico|null
     */
    public function getNestadoTecnicoid()
    {
        return $this->nestadoTecnicoid;
    }

    /**
     * Set ntipoCombustibleid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible|null $ntipoCombustibleid
     *
     * @return Vehiculo
     */
    public function setNtipoCombustibleid(\Geocuba\PortadoresBundle\Entity\TipoCombustible $ntipoCombustibleid = null)
    {
        $this->ntipoCombustibleid = $ntipoCombustibleid;

        return $this;
    }

    /**
     * Get ntipoCombustibleid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoCombustible|null
     */
    public function getNtipoCombustibleid()
    {
        return $this->ntipoCombustibleid;
    }

    /**
     * Set area.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Area|null $area
     *
     * @return Vehiculo
     */
    public function setArea(\Geocuba\PortadoresBundle\Entity\Area $area = null)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Area|null
     */
    public function getArea()
    {
        return $this->area;
    }


    /**
     * Add tarjeta.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TarjetaVehiculo $tarjeta
     *
     * @return Vehiculo
     */
    public function addTarjeta(\Geocuba\PortadoresBundle\Entity\TarjetaVehiculo $tarjeta)
    {
        $this->tarjetas[] = $tarjeta;

        return $this;
    }

    /**
     * Remove tarjeta.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TarjetaVehiculo $tarjeta
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeTarjeta(\Geocuba\PortadoresBundle\Entity\TarjetaVehiculo $tarjeta)
    {
        return $this->tarjetas->removeElement($tarjeta);
    }

    /**
     * Get tarjetas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarjetas()
    {
        return $this->tarjetas;
    }

    /**
     * Add persona.
     *
     * @param \Geocuba\PortadoresBundle\Entity\VehiculoPersona $persona
     *
     * @return Vehiculo
     */
    public function addPersona(\Geocuba\PortadoresBundle\Entity\VehiculoPersona $persona)
    {
        $this->personas[] = $persona;

        return $this;
    }

    /**
     * Remove persona.
     *
     * @param \Geocuba\PortadoresBundle\Entity\VehiculoPersona $persona
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePersona(\Geocuba\PortadoresBundle\Entity\VehiculoPersona $persona)
    {
        return $this->personas->removeElement($persona);
    }

    /**
     * Get personas.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPersonas()
    {
        return $this->personas;
    }
}
