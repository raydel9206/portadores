<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CentroCosto
 *
 * @ORM\Table(name="nomencladores.centro_costo", indexes={@ORM\Index(name="IDX_F0E520973C7CEFFE", columns={"nunidadid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\CentroCostoRepository")
 */
class CentroCosto
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
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", nullable=false)
     */
    private $codigo;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;



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
     * @return CentroCosto
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
     * Set codigo
     *
     * @param string $codigo
     *
     * @return NcentroCosto
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
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return CentroCosto
     */
    public function setNunidadid(\Geocuba\PortadoresBundle\Entity\Unidad $nunidadid = null)
    {
        $this->nunidadid = $nunidadid;

        return $this;
    }

    /**
     * Get nunidadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getNunidadid()
    {
        return $this->nunidadid;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return CentroCosto
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
