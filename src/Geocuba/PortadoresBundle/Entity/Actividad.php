<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Actividad
 *
 * @ORM\Table(name="nomencladores.actividad", indexes={@ORM\Index(name="IDX_76258747F989FD9", columns={"um_actividad"}), @ORM\Index(name="IDX_7625874581E2F8", columns={"portadorid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ActividadRepository")
 */
class Actividad
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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    //TODO Codigo MEP y codigo GAE esto es una particularidad del GAE?
    /**
     * @var string
     *
     * @ORM\Column(name="codigogae", type="string", length=255, nullable=true)
     */
    private $codigogae;

    /**
     * @var string
     *
     * @ORM\Column(name="codigomep", type="string", length=255, nullable=true)
     */
    private $codigomep;

    /**
     * @var boolean
     *
     * @ORM\Column(name="administrativa", type="boolean", nullable=true)
     */
    private $administrativa;

    /**
     * @var boolean
     *
     * @ORM\Column(name="inversiones", type="boolean", nullable=true)
     */
    private $inversiones;

    /**
     * @var boolean
     *
     * @ORM\Column(name="trafico", type="boolean", nullable=true)
     */
    private $trafico;

    /**
     * @var UMNivelActividad
     *
     * @ORM\ManyToOne(targetEntity="UMNivelActividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="um_actividad", referencedColumnName="id")
     * })
     */
    private $umActividad;

    /**
     * @var Portador
     *
     * @ORM\ManyToOne(targetEntity="Portador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portadorid", referencedColumnName="id")
     * })
     */
    private $portadorid;

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
     * @return Actividad
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
     * @return Actividad
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
     * Set codigogae
     *
     * @param string $codigogae
     *
     * @return Actividad
     */
    public function setCodigogae($codigogae)
    {
        $this->codigogae = $codigogae;

        return $this;
    }

    /**
     * Get codigogae
     *
     * @return string
     */
    public function getCodigogae()
    {
        return $this->codigogae;
    }

    /**
     * Set codigomep
     *
     * @param string $codigomep
     *
     * @return Actividad
     */
    public function setCodigomep($codigomep)
    {
        $this->codigomep = $codigomep;

        return $this;
    }

    /**
     * Get codigomep
     *
     * @return string
     */
    public function getCodigomep()
    {
        return $this->codigomep;
    }

    /**
     * Set administrativa
     *
     * @param boolean $administrativa
     *
     * @return Actividad
     */
    public function setAdministrativa($administrativa)
    {
        $this->administrativa = $administrativa;

        return $this;
    }

    /**
     * Get administrativa
     *
     * @return boolean
     */
    public function getAdministrativa()
    {
        return $this->administrativa;
    }

    /**
     * Set inversiones
     *
     * @param boolean $inversiones
     *
     * @return Actividad
     */
    public function setInversiones($inversiones)
    {
        $this->inversiones = $inversiones;

        return $this;
    }

    /**
     * Get inversiones
     *
     * @return boolean
     */
    public function getInversiones()
    {
        return $this->inversiones;
    }

    /**
     * Set trafico
     *
     * @param boolean $trafico
     *
     * @return Actividad
     */
    public function setTrafico($trafico)
    {
        $this->trafico = $trafico;

        return $this;
    }

    /**
     * Get trafico
     *
     * @return boolean
     */
    public function getTrafico()
    {
        return $this->trafico;
    }

    /**
     * Set umActividad
     *
     * @param \Geocuba\PortadoresBundle\Entity\UMNivelActividad $umActividad
     *
     * @return Actividad
     */
    public function setUmActividad(\Geocuba\PortadoresBundle\Entity\UMNivelActividad $umActividad = null)
    {
        $this->umActividad = $umActividad;

        return $this;
    }

    /**
     * Get umActividad
     *
     * @return \Geocuba\PortadoresBundle\Entity\UMNivelActividad
     */
    public function getUmActividad()
    {
        return $this->umActividad;
    }

    /**
     * Set portadorid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Portador $portadorid
     *
     * @return Actividad
     */
    public function setPortadorid(\Geocuba\PortadoresBundle\Entity\Portador $portadorid = null)
    {
        $this->portadorid = $portadorid;

        return $this;
    }

    /**
     * Get portadorid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Portador
     */
    public function getPortadorid()
    {
        return $this->portadorid;
    }
}
