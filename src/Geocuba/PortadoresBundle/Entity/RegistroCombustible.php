<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistroCombustible
 *
 * @ORM\Table(name="datos.registro_combustible", indexes={@ORM\Index(name="IDX_BD07E1C60C04838", columns={"vehiculoid"})})
 * @ORM\Entity
 */
class RegistroCombustible
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehiculoid", referencedColumnName="id")
     * })
     */
    private $vehiculoid;

    /**
     * @var string
     *
     * @ORM\Column(name="norma_plan", type="decimal", precision=19, scale=2, nullable=false)
     */
    private $normaPlan;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return RegistroCombustible
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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return RegistroCombustible
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
     * Set vehiculoid
     *
     * @param Vehiculo $vehiculoid
     *
     * @return RegistroCombustible
     */
    public function setVehiculoid($vehiculoid = null)
    {
        $this->vehiculoid = $vehiculoid;

        return $this;
    }

    /**
     * Get vehiculoid
     *
     * @return Vehiculo
     */
    public function getVehiculoid()
    {
        return $this->vehiculoid;
    }

    /**
     * Set normaPlan.
     *
     * @param string $normaPlan
     *
     * @return RegistroCombustible
     */
    public function setNormaPlan($normaPlan)
    {
        $this->normaPlan = $normaPlan;
    
        return $this;
    }

    /**
     * Get normaPlan.
     *
     * @return string
     */
    public function getNormaPlan()
    {
        return $this->normaPlan;
    }
}
