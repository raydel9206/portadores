<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Producto
 *
 * @ORM\Table(name="nomencladores.producto")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ProductoRepository")
 */
class Producto
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
     * @ORM\Column(name="fila", type="string", nullable=true)
     */
    private $fila;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var \UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_medida", referencedColumnName="id")
     * })
     */
    private $um;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="enblanco", type="boolean", nullable=true)
     */
    private $enblanco;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
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
     * @return Producto
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
     * @return Producto
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
     * Set fila
     *
     * @param string $fila
     *
     * @return Producto
     */
    public function setFila($fila): Producto
    {
        $this->fila = $fila;

        return $this;
    }

    /**
     * Get fila
     *
     * @return string
     */
    public function getFila()
    {
        return $this->fila;
    }

    /**
     * Set enblanco.
     *
     * @param bool|null $enblanco
     *
     * @return Producto
     */
    public function setEnblanco($enblanco = null)
    {
        $this->enblanco = $enblanco;
    
        return $this;
    }

    /**
     * Get enblanco.
     *
     * @return bool|null
     */
    public function getEnblanco()
    {
        return $this->enblanco;
    }

    /**
     * Set um.
     *
     * @param \Geocuba\PortadoresBundle\Entity\UnidadMedida|null $um
     *
     * @return Producto
     */
    public function setUm(\Geocuba\PortadoresBundle\Entity\UnidadMedida $um = null)
    {
        $this->um = $um;
    
        return $this;
    }

    /**
     * Get um.
     *
     * @return \Geocuba\PortadoresBundle\Entity\UnidadMedida|null
     */
    public function getUm()
    {
        return $this->um;
    }
}
