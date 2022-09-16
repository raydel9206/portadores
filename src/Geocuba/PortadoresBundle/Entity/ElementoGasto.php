<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ElementoGasto
 *
 * @ORM\Table(name="nomencladores.elemento_gasto")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ElementoGastoRepository")
 */
class ElementoGasto
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
     * @ORM\Column(name="codigo", type="string", nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    /**
     * @ORM\ManyToMany(targetEntity="Portador", inversedBy="elementos")
     * @ORM\JoinTable(name="nomencladores.elemento_portador")
     **/
    private $portadores;

    /**
     * @var \Moneda
     *
     * @ORM\ManyToOne(targetEntity="Moneda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="moneda", referencedColumnName="id")
     * })
     */
    private $moneda;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad", referencedColumnName="id")
     * })
     */
    private $unidad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    public function __construct() {
        $this->portadores = new \Doctrine\Common\Collections\ArrayCollection();
    }

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
     * Set descripcion
     *
     * @param string $nombre
     *
     * @return ElementoGasto
     */
    public function setNombre($nombre)
    {
        $this->descripcion = $nombre;

        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->descripcion;
    }

    /**
     * Set codigo
     *
     * @param string $codigo
     *
     * @return ElementoGasto
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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return ElementoGasto
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
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return ElementoGasto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Add portadore.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Portador $portadore
     *
     * @return ElementoGasto
     */
    public function addPortadore(\Geocuba\PortadoresBundle\Entity\Portador $portadore)
    {
        $this->portadores[] = $portadore;

        return $this;
    }

    /**
     * Remove portadore.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Portador $portadore
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removePortadore(\Geocuba\PortadoresBundle\Entity\Portador $portadore)
    {
        return $this->portadores->removeElement($portadore);
    }

    /**
     * Get portadores.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPortadores()
    {
        return $this->portadores;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return ElementoGasto
     */
    public function setMoneda(\Geocuba\PortadoresBundle\Entity\Moneda $moneda = null)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Moneda|null
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set unidad.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $unidad
     *
     * @return ElementoGasto
     */
    public function setUnidad(\Geocuba\PortadoresBundle\Entity\Unidad $unidad = null)
    {
        $this->unidad = $unidad;
    
        return $this;
    }

    /**
     * Get unidad.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
}
