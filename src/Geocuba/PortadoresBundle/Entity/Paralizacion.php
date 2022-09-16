<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paralizacion
 *
 * @ORM\Table(name="datos.paralizacion", indexes={@ORM\Index(name="IDX_4269B99560C04838", columns={"vehiculoid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ParalizacionRepository")
 */
class Paralizacion
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
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="text", nullable=true)
     */
    private $motivo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="en_sasa", type="boolean", nullable=true)
     */
    private $enSasa = false;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_pedido", type="string", length=20, nullable=true)
     */
    private $nroPedido;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehiculoid", referencedColumnName="id")
     * })
     */
    private $vehiculoid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_marcha", type="date", nullable=true)
     */
    private $fechaMarcha;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @var string
     *
     * @ORM\Column(name="taller", type="string", length=255, nullable=true)
     */
    private $taller;



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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Paralizacion
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
     * Set motivo
     *
     * @param string $motivo
     *
     * @return Paralizacion
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set enSasa
     *
     * @param boolean $enSasa
     *
     * @return Paralizacion
     */
    public function setEnSasa($enSasa)
    {
        $this->enSasa = $enSasa;

        return $this;
    }

    /**
     * Get enSasa
     *
     * @return boolean
     */
    public function getEnSasa()
    {
        return $this->enSasa;
    }

    /**
     * Set nroPedido
     *
     * @param string $nroPedido
     *
     * @return Paralizacion
     */
    public function setNroPedido($nroPedido)
    {
        $this->nroPedido = $nroPedido;

        return $this;
    }

    /**
     * Get nroPedido
     *
     * @return string
     */
    public function getNroPedido()
    {
        return $this->nroPedido;
    }

    /**
     * Set vehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculoid
     *
     * @return Paralizacion
     */
    public function setVehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculoid = null)
    {
        $this->vehiculoid = $vehiculoid;

        return $this;
    }

    /**
     * Get vehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getVehiculoid()
    {
        return $this->vehiculoid;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Paralizacion
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
     * Set fechaMarcha
     *
     * @param \DateTime $fechaMarcha
     *
     * @return Paralizacion
     */
    public function setFechaMarcha($fechaMarcha)
    {
        $this->fechaMarcha = $fechaMarcha;

        return $this;
    }

    /**
     * Get fechaMarcha
     *
     * @return \DateTime
     */
    public function getFechaMarcha()
    {
        return $this->fechaMarcha;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return Paralizacion
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set taller
     *
     * @param string $taller
     *
     * @return Paralizacion
     */
    public function setTaller($taller)
    {
        $this->taller = $taller;

        return $this;
    }

    /**
     * Get nroPedido
     *
     * @return string
     */
    public function getTaller()
    {
        return $this->taller;
    }
}
