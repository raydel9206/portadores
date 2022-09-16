<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AreaMedida
 *
 * @ORM\Table(name="nomencladores.area_medida", indexes={@ORM\Index(name="IDX_783F5D8E7FFA30B8", columns={"nlista_areaid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\AreaMedidaRepository")
 */
class AreaMedida
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
     * @ORM\Column(name="nombre", type="string", length=500, nullable=true)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var boolean
     *
     * @ORM\Column(name="invalidante", type="boolean", nullable=true)
     */
    private $invalidante;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="Area")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nlista_areaid", referencedColumnName="id")
     * })
     */
    private $nlistaAreaid;



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
     * @return AreaMedida
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
     * Set invalidante
     *
     * @param boolean $invalidante
     *
     * @return AreaMedida
     */
    public function setInvalidante($invalidante)
    {
        $this->invalidante = $invalidante;

        return $this;
    }

    /**
     * Get invalidante
     *
     * @return boolean
     */
    public function getInvalidante()
    {
        return $this->invalidante;
    }

    /**
     * Set nlistaAreaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Area $nlistaAreaid
     *
     * @return AreaMedida
     */
    public function setNlistaAreaid(\Geocuba\PortadoresBundle\Entity\Area $nlistaAreaid = null)
    {
        $this->nlistaAreaid = $nlistaAreaid;

        return $this;
    }

    /**
     * Get nlistaAreaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Area
     */
    public function getNlistaAreaid()
    {
        return $this->nlistaAreaid;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return AreaMedida
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
