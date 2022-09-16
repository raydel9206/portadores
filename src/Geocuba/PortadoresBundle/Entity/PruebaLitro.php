<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PruebaLitro
 *
 * @ORM\Table(name="datos.prueba_litro", indexes={@ORM\Index(name="IDX_F21BDBDDEA6F2C9C", columns={"nvehiculoid"})})
 * @ORM\Entity
 */
class PruebaLitro
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_prueba", type="date", nullable=false)
     */
    private $fechaPrueba;

    /**
     * @var string
     *
     * @ORM\Column(name="responsable", type="string", length=255, nullable=false)
     */
    private $responsable;

    /**
     * @var float
     *
     * @ORM\Column(name="indice", type="float", precision=10, scale=0, nullable=false)
     */
    private $indice;

    /**
     * @var float
     *
     * @ORM\Column(name="indice_far", type="float", precision=10, scale=0, nullable=true)
     */
    private $indiceFar;

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
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fechaPrueba
     *
     * @param \DateTime $fechaPrueba
     *
     * @return PruebaLitro
     */
    public function setFechaPrueba($fechaPrueba)
    {
        $this->fechaPrueba = $fechaPrueba;

        return $this;
    }

    /**
     * Get fechaPrueba
     *
     * @return \DateTime
     */
    public function getFechaPrueba()
    {
        return $this->fechaPrueba;
    }

    /**
     * Set responsable
     *
     * @param string $responsable
     *
     * @return PruebaLitro
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return string
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set indice
     *
     * @param float $indice
     *
     * @return PruebaLitro
     */
    public function setIndice($indice)
    {
        $this->indice = $indice;

        return $this;
    }

    /**
     * Get indice
     *
     * @return float
     */
    public function getIndice()
    {
        return $this->indice;
    }

    /**
     * @return float
     */
    public function getIndiceFar()
    {
        return $this->indiceFar;
    }

    /**
     * @param float $indiceFar
     */
    public function setIndiceFar($indiceFar)
    {
        $this->indiceFar = $indiceFar;
    }

    /**
     * Set nvehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid
     *
     * @return PruebaLitro
     */
    public function setNvehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid = null)
    {
        $this->nvehiculoid = $nvehiculoid;

        return $this;
    }

    /**
     * Get nvehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getNvehiculoid()
    {
        return $this->nvehiculoid;
    }
}
