<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModeloVehiculo
 *
 * @ORM\Table(name="nomencladores.modelo_vehiculo")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ModeloVehiculoRepository")
 */
class ModeloVehiculo
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var \MarcaVehiculo
     *
     * @ORM\ManyToOne(targetEntity="MarcaVehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nmarca_vehiculoid", referencedColumnName="id")
     * })
     */
    private $marcaVehiculoid;



    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     *
     * @return ModeloVehiculo
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return ModeloVehiculo
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
     * Set marcaVehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\MarcaVehiculo $marcaVehiculoid
     *
     * @return ModeloVehiculo
     */
    public function setMarcaVehiculoid(\Geocuba\PortadoresBundle\Entity\MarcaVehiculo $marcaVehiculoid = null)
    {
        $this->marcaVehiculoid = $marcaVehiculoid;

        return $this;
    }

    /**
     * Get marcaVehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\MarcaVehiculo
     */
    public function getMarcaVehiculoid()
    {
        return $this->marcaVehiculoid;
    }
}
