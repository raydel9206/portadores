<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DistribucionCombustibleDesglose
 *
 * @ORM\Table(name="datos.distribucion_combustible_desglose", indexes={@ORM\Index(name="IDX_A0101016F09392FB", columns={"distribucion_combustible"}), @ORM\Index(name="IDX_A010101651E5B69B", columns={"persona"}), @ORM\Index(name="IDX_A0101016AE90B786", columns={"tarjeta"}), @ORM\Index(name="IDX_A0101016C9FA1603", columns={"vehiculo"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\DistribucionCombustibleDesgloseRepository")
 */
class DistribucionCombustibleDesglose
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
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=0, nullable=false)
     */
    private $cantidad = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="precio_combustible", type="float", precision=10, scale=0, nullable=false)
     */
    private $precioCombustible = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="nota", type="text", nullable=true)
     */
    private $nota;

    /**
     * @var distribucionCombustible
     *
     * @ORM\ManyToOne(targetEntity="DistribucionCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="distribucion_combustible", referencedColumnName="id")
     * })
     */
    private $distribucionCombustible;

    /**
     * @var persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="persona", referencedColumnName="id")
     * })
     */
    private $persona;

    /**
     * @var tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tarjeta", referencedColumnName="id")
     * })
     */
    private $tarjeta;

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
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return DistribucionCombustibleDesglose
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set precioCombustible
     *
     * @param float $precioCombustible
     *
     * @return DistribucionCombustibleDesglose
     */
    public function setPrecioCombustible($precioCombustible)
    {
        $this->precioCombustible = $precioCombustible;

        return $this;
    }

    /**
     * Get precioCombustible
     *
     * @return float
     */
    public function getPrecioCombustible()
    {
        return $this->precioCombustible;
    }

    /**
     * Set nota
     *
     * @param string $nota
     *
     * @return DistribucionCombustibleDesglose
     */
    public function setNota($nota)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set distribucionCombustible
     *
     * @param \Geocuba\PortadoresBundle\Entity\DistribucionCombustible $distribucionCombustible
     *
     * @return DistribucionCombustibleDesglose
     */
    public function setDistribucionCombustible(\Geocuba\PortadoresBundle\Entity\DistribucionCombustible $distribucionCombustible = null)
    {
        $this->distribucionCombustible = $distribucionCombustible;

        return $this;
    }

    /**
     * Get distribucionCombustible
     *
     * @return \Geocuba\PortadoresBundle\Entity\DistribucionCombustible
     */
    public function getDistribucionCombustible()
    {
        return $this->distribucionCombustible;
    }

    /**
     * Set persona
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $persona
     *
     * @return DistribucionCombustibleDesglose
     */
    public function setPersona(\Geocuba\PortadoresBundle\Entity\Persona $persona = null)
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * Get persona
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getPersona()
    {
        return $this->persona;
    }

    /**
     * Set tarjeta
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $tarjeta
     *
     * @return DistribucionCombustibleDesglose
     */
    public function setTarjeta(\Geocuba\PortadoresBundle\Entity\Tarjeta $tarjeta = null)
    {
        $this->tarjeta = $tarjeta;

        return $this;
    }

    /**
     * Get tarjeta
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getTarjeta()
    {
        return $this->tarjeta;
    }

    /**
     * Set vehiculo
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculo
     *
     * @return DistribucionCombustibleDesglose
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
}
