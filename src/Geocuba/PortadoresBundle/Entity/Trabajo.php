<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trabajo
 *
 * @ORM\Table(name="nomencladores.trabajo")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TrabajoRepository")
 */
class Trabajo
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
     * @ORM\Column(name="codigo", type="string", length=255)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_inicio", type="date", nullable=true)
     */
    private $fechaInicio;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_fin", type="date", nullable=true)
     */
    private $fechaFin;

    /**
     * @var Destino
     *
     * @ORM\ManyToOne(targetEntity="Destino")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ndestinoid", referencedColumnName="id")
     * })
     */
    private $ndestinoid;

    /**
     * @var CentroCosto
     *
     * @ORM\ManyToOne(targetEntity="CentroCosto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ncentrocostoid", referencedColumnName="id")
     * })
     */
    private $ncentrocostoid;

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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Trabajo
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
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * @param string $codigo
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Trabajo
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
     * Set fechaInicio
     *
     * @param \DateTime $fechaInicio
     *
     * @return Trabajo
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin
     *
     * @param \DateTime $fechaFin
     *
     * @return Trabajo
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set ndestinoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Destino $ndestinoid
     *
     * @return Trabajo
     */
    public function setNdestinoid(\Geocuba\PortadoresBundle\Entity\Destino $ndestinoid = null)
    {
        $this->ndestinoid = $ndestinoid;

        return $this;
    }

    /**
     * Get ndestinoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Destino
     */
    public function getNdestinoid()
    {
        return $this->ndestinoid;
    }

    /**
     * Get ncentrocostoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\CentroCosto
     */
    public function getNcentrocostoid()
    {
        return $this->ncentrocostoid;
    }

    /**
     * Set ncentrocostoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\CentroCosto $ncentrocostoid
     *
     * @return Trabajo
     */
    public function setNcentrocostoid(\Geocuba\PortadoresBundle\Entity\CentroCosto $ncentrocostoid = null)
    {
        $this->ncentrocostoid = $ncentrocostoid;

        return $this;
    }
}
