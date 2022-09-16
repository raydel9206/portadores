<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Portador
 *
 * @ORM\Table(name="nomencladores.portador", indexes={@ORM\Index(name="IDX_9B3292C97DA31363", columns={"unidad_medida"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\PortadorRepository")
 */
class Portador
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

    /**
     * @var \UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_medida", referencedColumnName="id")
     * })
     */
    private $unidadMedida;

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
     * @return Portador
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
     * @return Portador
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
     * Set unidadMedida
     *
     * @param \Geocuba\PortadoresBundle\Entity\UnidadMedida $unidadMedida
     *
     * @return Portador
     */
    public function setUnidadMedida(\Geocuba\PortadoresBundle\Entity\UnidadMedida $unidadMedida = null)
    {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }

    /**
     * Get unidadMedida
     *
     * @return \Geocuba\PortadoresBundle\Entity\UnidadMedida
     */
    public function getUnidadMedida()
    {
        return $this->unidadMedida;
    }
}
