<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Traslado
 *
 * @ORM\Table(name="nomencladores.traslado", indexes={@ORM\Index(name="IDX_B11902EECC96CCDC", columns={"desde"}), @ORM\Index(name="IDX_B11902EE28E1AFAE", columns={"hacia"}), @ORM\Index(name="IDX_B11902EEC9FA1603", columns={"vehiculo"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TrasladoRepository")
 */
class Traslado
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
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var string|null
     *
     * @ORM\Column(name="motivos", type="string", length=255, nullable=true)
     */
    private $motivos;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="aceptado", type="boolean", nullable=true)
     */
    private $aceptado = false;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="desde", referencedColumnName="id")
     * })
     */
    private $desde;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="hacia", referencedColumnName="id")
     * })
     */
    private $hacia;

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
     * Set fecha.
     *
     * @param \DateTime|null $fecha
     *
     * @return Traslado
     */
    public function setFecha($fecha = null)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime|null
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set motivos.
     *
     * @param string|null $motivos
     *
     * @return Traslado
     */
    public function setMotivos($motivos = null)
    {
        $this->motivos = $motivos;
    
        return $this;
    }

    /**
     * Get motivos.
     *
     * @return string|null
     */
    public function getMotivos()
    {
        return $this->motivos;
    }

    /**
     * Set aceptado.
     *
     * @param bool|null $aceptado
     *
     * @return Traslado
     */
    public function setAceptado($aceptado = null)
    {
        $this->aceptado = $aceptado;
    
        return $this;
    }

    /**
     * Get aceptado.
     *
     * @return bool|null
     */
    public function getAceptado()
    {
        return $this->aceptado;
    }

    /**
     * Set desde.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $desde
     *
     * @return Traslado
     */
    public function setDesde(\Geocuba\PortadoresBundle\Entity\Unidad $desde = null)
    {
        $this->desde = $desde;
    
        return $this;
    }

    /**
     * Get desde.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getDesde()
    {
        return $this->desde;
    }

    /**
     * Set hacia.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $hacia
     *
     * @return Traslado
     */
    public function setHacia(\Geocuba\PortadoresBundle\Entity\Unidad $hacia = null)
    {
        $this->hacia = $hacia;
    
        return $this;
    }

    /**
     * Get hacia.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getHacia()
    {
        return $this->hacia;
    }

    /**
     * Set vehiculo.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo|null $vehiculo
     *
     * @return Traslado
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
