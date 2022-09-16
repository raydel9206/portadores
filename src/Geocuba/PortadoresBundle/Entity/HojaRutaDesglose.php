<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HojaRutaDesglose
 *
 * @ORM\Table(name="datos.hoja_ruta_desglose", indexes={@ORM\Index(name="IDX_5DA31F74EAFAB9A3", columns={"hojarutaid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\HojaRutaDesgloseRepository")
 */
class HojaRutaDesglose
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
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horasalida", type="time", nullable=false)
     */
    private $horasalida;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="horallegada", type="time", nullable=false)
     */
    private $horallegada;

    /**
     * @var float
     *
     * @ORM\Column(name="kmssalida", type="float", precision=10, scale=0, nullable=false)
     */
    private $kmssalida = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="kmsllegada", type="float", precision=10, scale=0, nullable=false)
     */
    private $kmsllegada = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="kmstotales", type="float", precision=10, scale=0, nullable=false)
     */
    private $kmstotales = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="boleta", type="string", length=255, nullable=true)
     */
    private $boleta;

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
     * @return HojaRutaDesglose
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
     * Set horasalida
     *
     * @param \DateTime $horasalida
     *
     * @return HojaRutaDesglose
     */
    public function setHorasalida($horasalida)
    {
        $this->horasalida = $horasalida;

        return $this;
    }

    /**
     * Get horasalida
     *
     * @return \DateTime
     */
    public function getHorasalida()
    {
        return $this->horasalida;
    }

    /**
     * Set horallegada
     *
     * @param \DateTime $horallegada
     *
     * @return HojaRutaDesglose
     */
    public function setHorallegada($horallegada)
    {
        $this->horallegada = $horallegada;

        return $this;
    }

    /**
     * Get horallegada
     *
     * @return \DateTime
     */
    public function getHorallegada()
    {
        return $this->horallegada;
    }

    /**
     * Set kmssalida
     *
     * @param float $kmssalida
     *
     * @return HojaRutaDesglose
     */
    public function setKmssalida($kmssalida)
    {
        $this->kmssalida = $kmssalida;

        return $this;
    }

    /**
     * Get kmssalida
     *
     * @return float
     */
    public function getKmssalida()
    {
        return $this->kmssalida;
    }

    /**
     * Set kmsllegada
     *
     * @param float $kmsllegada
     *
     * @return HojaRutaDesglose
     */
    public function setKmsllegada($kmsllegada)
    {
        $this->kmsllegada = $kmsllegada;

        return $this;
    }

    /**
     * Get kmsllegada
     *
     * @return float
     */
    public function getKmsllegada()
    {
        return $this->kmsllegada;
    }

    /**
     * Set kmstotales
     *
     * @param float $kmstotales
     *
     * @return HojaRutaDesglose
     */
    public function setKmstotales($kmstotales)
    {
        $this->kmstotales = $kmstotales;

        return $this;
    }

    /**
     * Get kmstotales
     *
     * @return float
     */
    public function getKmstotales()
    {
        return $this->kmstotales;
    }

    /**
     * Set boleta
     *
     * @param string $boleta
     *
     * @return HojaRutaDesglose
     */
    public function setBoleta($boleta)
    {
        $this->boleta = $boleta;

        return $this;
    }

    /**
     * Get boleta
     *
     * @return string
     */
    public function getBoleta()
    {
        return $this->boleta;
    }

    /**
     * Set hojarutaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\HojaRuta $hojarutaid
     *
     * @return HojaRutaDesglose
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
}
