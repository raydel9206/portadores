<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Norma
 *
 * @ORM\Table(name="nomencladores.norma", indexes={@ORM\Index(name="IDX_C69B11698A5C6513", columns={"ndenominacion_vehiculo"}), @ORM\Index(name="IDX_C69B11693DF3408B", columns={"tipo_mantenimiento"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\NormaRepository")
 */
class Norma
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
     * @var int
     *
     * @ORM\Column(name="cant_horas", type="integer", nullable=false)
     */
    private $cantHoras;

    /**
     * @var \MarcaVehiculo
     *
     * @ORM\ManyToOne(targetEntity="MarcaVehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marca", referencedColumnName="id")
     * })
     */
    private $marca;

    /**
     * @var \TipoMantenimiento
     *
     * @ORM\ManyToOne(targetEntity="TipoMantenimiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_mantenimiento", referencedColumnName="id")
     * })
     */
    private $tipoMantenimiento;



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
     * Set cantHoras.
     *
     * @param int $cantHoras
     *
     * @return Norma
     */
    public function setCantHoras($cantHoras)
    {
        $this->cantHoras = $cantHoras;
    
        return $this;
    }

    /**
     * Get cantHoras.
     *
     * @return int
     */
    public function getCantHoras()
    {
        return $this->cantHoras;
    }

    /**
     * Set marca.
     *
     * @param \Geocuba\PortadoresBundle\Entity\MarcaVehiculo|null $marca
     *
     * @return Norma
     */
    public function setMarca(\Geocuba\PortadoresBundle\Entity\MarcaVehiculo $marca = null)
    {
        $this->marca = $marca;
    
        return $this;
    }

    /**
     * Get marca.
     *
     * @return \Geocuba\PortadoresBundle\Entity\MarcaVehiculo|null
     */
    public function getMarca()
    {
        return $this->marca;
    }

    /**
     * Set tipoMantenimiento.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoMantenimiento|null $tipoMantenimiento
     *
     * @return Norma
     */
    public function setTipoMantenimiento(\Geocuba\PortadoresBundle\Entity\TipoMantenimiento $tipoMantenimiento = null)
    {
        $this->tipoMantenimiento = $tipoMantenimiento;
    
        return $this;
    }

    /**
     * Get tipoMantenimiento.
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoMantenimiento|null
     */
    public function getTipoMantenimiento()
    {
        return $this->tipoMantenimiento;
    }
}
