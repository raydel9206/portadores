<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HojaRutaConductor
 *
 * @ORM\Table(name="datos.hoja_ruta_conductor", indexes={@ORM\Index(name="IDX_9686A342EAFAB9A3", columns={"hojarutaid"}), @ORM\Index(name="IDX_9686A342D5F7F18A", columns={"conductor"})})
 * @ORM\Entity
 */
class HojaRutaConductor
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
     * @ORM\Column(name="licencia", type="string", length=255, nullable=true)
     */
    private $licencia;

    /**
     * @var HojaRuta
     *
     * @ORM\ManyToOne(targetEntity="HojaRuta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="hojarutaid", referencedColumnName="id")
     * })
     */
    private $hojarutaid;

    /**
     * @var Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="conductor", referencedColumnName="id")
     * })
     */
    private $conductor;



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
     * Set hojarutaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\HojaRuta $hojarutaid
     *
     * @return HojaRutaConductor
     */
    public function setHojarutaid(\Geocuba\PortadoresBundle\Entity\HojaRuta $hojarutaid = null)
    {
        $this->hojarutaid = $hojarutaid;

        return $this;
    }

    /**
     * Get hojarutaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\HojaRuta
     */
    public function getHojarutaid()
    {
        return $this->hojarutaid;
    }

    /**
     * Set conductor
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $conductor
     *
     * @return HojaRutaConductor
     */
    public function setConductor(\Geocuba\PortadoresBundle\Entity\Persona $conductor = null)
    {
        $this->conductor = $conductor;

        return $this;
    }

    /**
     * Get conductor
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getConductor()
    {
        return $this->conductor;
    }

    /**
     * @return string
     */
    public function getLicencia()
    {
        return $this->licencia;
    }

    /**
     * @param string $licencia
     */
    public function setLicencia($licencia)
    {
        $this->licencia = $licencia;
    }
}
