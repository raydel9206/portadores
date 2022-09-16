<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarjetaVehiculo
 *
 * @ORM\Table(name="nomencladores.tarjeta_vehiculo", indexes={@ORM\Index(name="IDX_353B4F124D489839", columns={"ntarjetaid"}), @ORM\Index(name="IDX_353B4F12EA6F2C9C", columns={"nvehiculoid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TarjetaVehiculoRepository")
 */
class TarjetaVehiculo
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
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ntarjetaid", referencedColumnName="id")
     * })
     */
    private $ntarjetaid;

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
     * Set ntarjetaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid
     *
     * @return TarjetaVehiculo
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
     * @return TarjetaVehiculo
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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return TarjetaVehiculo
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
}
