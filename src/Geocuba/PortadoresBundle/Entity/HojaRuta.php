<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HojaRuta
 *
 * @ORM\Table(name="datos.hoja_ruta", indexes={@ORM\Index(name="IDX_422B86B7C9FA1603", columns={"vehiculo"}), @ORM\Index(name="IDX_422B86B784F2C0B1", columns={"habilitadapor"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\HojaRutaRepository")
 */
class HojaRuta
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
     * @ORM\Column(name="numerohoja", type="string", length=20, nullable=false)
     */
    private $numerohoja;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var integer
     *
     * @ORM\Column(name="capacidad", type="integer", nullable=true)
     */
    private $capacidad;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=20, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="entidad", type="string", length=255, nullable=true)
     */
    private $entidad;

    /**
     * @var string
     *
     * @ORM\Column(name="organismo", type="string", length=255, nullable=true)
     */
    private $organismo;

    /**
     * @var string
     *
     * @ORM\Column(name="lugarparqueo", type="string", length=255, nullable=true)
     */
    private $lugarparqueo;

    /**
     * @var string
     *
     * @ORM\Column(name="servicioautorizado", type="string", length=255, nullable=true)
     */
    private $servicioautorizado;

    /**
     * @var string
     *
     * @ORM\Column(name="kmsdisponible", type="string", length=255, nullable=true)
     */
    private $kmsdisponible;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible = true;

    /**
     * @var Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehiculo", referencedColumnName="id")
     * })
     */
    private $vehiculo;

    /**
     * @var Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="habilitadapor", referencedColumnName="id")
     * })
     */
    private $habilitadapor;



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
     * Set numerohoja
     *
     * @param string $numerohoja
     *
     * @return HojaRuta
     */
    public function setNumerohoja($numerohoja)
    {
        $this->numerohoja = $numerohoja;

        return $this;
    }

    /**
     * Get numerohoja
     *
     * @return string
     */
    public function getNumerohoja()
    {
        return $this->numerohoja;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return HojaRuta
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set capacidad
     *
     * @param integer $capacidad
     *
     * @return HojaRuta
     */
    public function setCapacidad($capacidad)
    {
        $this->capacidad = $capacidad;

        return $this;
    }

    /**
     * Get capacidad
     *
     * @return integer
     */
    public function getCapacidad()
    {
        return $this->capacidad;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return HojaRuta
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set entidad
     *
     * @param string $entidad
     *
     * @return HojaRuta
     */
    public function setEntidad($entidad)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get entidad
     *
     * @return string
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set organismo
     *
     * @param string $organismo
     *
     * @return HojaRuta
     */
    public function setOrganismo($organismo)
    {
        $this->organismo = $organismo;

        return $this;
    }

    /**
     * Get organismo
     *
     * @return string
     */
    public function getOrganismo()
    {
        return $this->organismo;
    }

    /**
     * Set lugarparqueo
     *
     * @param string $lugarparqueo
     *
     * @return HojaRuta
     */
    public function setLugarparqueo($lugarparqueo)
    {
        $this->lugarparqueo = $lugarparqueo;

        return $this;
    }

    /**
     * Get lugarparqueo
     *
     * @return string
     */
    public function getLugarparqueo()
    {
        return $this->lugarparqueo;
    }

    /**
     * Set servicioautorizado
     *
     * @param string $servicioautorizado
     *
     * @return HojaRuta
     */
    public function setServicioautorizado($servicioautorizado)
    {
        $this->servicioautorizado = $servicioautorizado;

        return $this;
    }

    /**
     * Get servicioautorizado
     *
     * @return string
     */
    public function getServicioautorizado()
    {
        return $this->servicioautorizado;
    }

    /**
     * Set kmsdisponible
     *
     * @param string $kmsdisponible
     *
     * @return HojaRuta
     */
    public function setKmsdisponible($kmsdisponible)
    {
        $this->kmsdisponible = $kmsdisponible;

        return $this;
    }

    /**
     * Get kmsdisponible
     *
     * @return string
     */
    public function getKmsdisponible()
    {
        return $this->kmsdisponible;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Mantenimiento
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return HojaRuta
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
     * Set vehiculo
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculo
     *
     * @return HojaRuta
     */
    public function setVehiculo(\Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculo = null)
    {
        $this->vehiculo = $vehiculo;

        return $this;
    }

    /**
     * Get vehiculo
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getVehiculo()
    {
        return $this->vehiculo;
    }

    /**
     * Set habilitadapor
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $habilitadapor
     *
     * @return HojaRuta
     */
    public function setHabilitadapor(\Geocuba\PortadoresBundle\Entity\Persona $habilitadapor = null)
    {
        $this->habilitadapor = $habilitadapor;

        return $this;
    }

    /**
     * Get habilitadapor
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getHabilitadapor()
    {
        return $this->habilitadapor;
    }
}
