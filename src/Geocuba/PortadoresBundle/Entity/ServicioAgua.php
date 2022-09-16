<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServicioAgua
 *
 * @ORM\Table(name="nomencladores.servicio_agua", indexes={@ORM\Index(name="IDX_A20524FF9D01464C", columns={"unidad_id"})})
 * @ORM\Entity
 */
class ServicioAgua
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=50, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="text", nullable=false)
     */
    private $direccion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="metrado", type="boolean", nullable=false)
     */
    private $metrado;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", nullable=true)
     */
    private $codigo;

    /**
     * @var float
     *
     * @ORM\Column(name="lectura_inicial", type="float", precision=10, scale=0, nullable=true)
     */
    private $lecturaInicial;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    private $unidad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return ServicioAgua
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
     * Set direccion
     *
     * @param string $direccion
     *
     * @return ServicioAgua
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set metrado
     *
     * @param boolean $metrado
     *
     * @return ServicioAgua
     */
    public function setMetrado($metrado)
    {
        $this->metrado = $metrado;

        return $this;
    }

    /**
     * Get metrado
     *
     * @return boolean
     */
    public function getMetrado()
    {
        return $this->metrado;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return ServicioAgua
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set lecturaInicial
     *
     * @param float $lecturaInicial
     *
     * @return ServicioAgua
     */
    public function setLecturaInicial($lecturaInicial)
    {
        $this->lecturaInicial = $lecturaInicial;

        return $this;
    }

    /**
     * Get lecturaInicial
     *
     * @return float
     */
    public function getLecturaInicial()
    {
        return $this->lecturaInicial;
    }

    /**
     * Set unidad
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $unidad
     *
     * @return ServicioAgua
     */
    public function setUnidad(\Geocuba\PortadoresBundle\Entity\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getUnidad()
    {
        return $this->unidad;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return ServicioAgua
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
