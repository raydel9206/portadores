<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mantenimiento
 *
 * @ORM\Table(name="datos.mantenimiento", indexes={@ORM\Index(name="IDX_D388EBDB11925AB3", columns={"tipo_mantenimientoid"}), @ORM\Index(name="IDX_D388EBDBEA6F2C9C", columns={"nvehiculoid"})})
 * @ORM\Entity
 */
class Mantenimiento
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="kilometraje", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $kilometraje;

    /**
     * @var string|null
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="ultimo", type="boolean", nullable=true, options={"default"="1"})
     */
    private $ultimo = true;

    /**
     * @var \TipoMantenimiento
     *
     * @ORM\ManyToOne(targetEntity="TipoMantenimiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_mantenimientoid", referencedColumnName="id")
     * })
     */
    private $tipoMantenimientoid;

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
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Mantenimiento
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
     * Set kilometraje.
     *
     * @param string $kilometraje
     *
     * @return Mantenimiento
     */
    public function setKilometraje($kilometraje)
    {
        $this->kilometraje = $kilometraje;
    
        return $this;
    }

    /**
     * Get kilometraje.
     *
     * @return string
     */
    public function getKilometraje()
    {
        return $this->kilometraje;
    }

    /**
     * Set observaciones.
     *
     * @param string|null $observaciones
     *
     * @return Mantenimiento
     */
    public function setObservaciones($observaciones = null)
    {
        $this->observaciones = $observaciones;
    
        return $this;
    }

    /**
     * Get observaciones.
     *
     * @return string|null
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set ultimo.
     *
     * @param bool|null $ultimo
     *
     * @return Mantenimiento
     */
    public function setUltimo($ultimo = null)
    {
        $this->ultimo = $ultimo;
    
        return $this;
    }

    /**
     * Get ultimo.
     *
     * @return bool|null
     */
    public function getUltimo()
    {
        return $this->ultimo;
    }

    /**
     * Set tipoMantenimientoid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoMantenimiento|null $tipoMantenimientoid
     *
     * @return Mantenimiento
     */
    public function setTipoMantenimientoid(\Geocuba\PortadoresBundle\Entity\TipoMantenimiento $tipoMantenimientoid = null)
    {
        $this->tipoMantenimientoid = $tipoMantenimientoid;
    
        return $this;
    }

    /**
     * Get tipoMantenimientoid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoMantenimiento|null
     */
    public function getTipoMantenimientoid()
    {
        return $this->tipoMantenimientoid;
    }

    /**
     * Set nvehiculoid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo|null $nvehiculoid
     *
     * @return Mantenimiento
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
