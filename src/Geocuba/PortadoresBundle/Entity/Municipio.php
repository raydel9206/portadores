<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Municipio
 *
 * @ORM\Table(name="nomencladores.municipio", indexes={@ORM\Index(name="IDX_1C3EFA0A8D656668", columns={"provinciaid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\MunicipioRepository")
 */
class Municipio
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var int|null
     *
     * @ORM\Column(name="codigo", type="integer", nullable=true)
     */
    private $codigo;

    /**
     * @var \Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="provinciaid", referencedColumnName="id")
     * })
     */
    private $provinciaid;



    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Municipio
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set visible.
     *
     * @param bool|null $visible
     *
     * @return Municipio
     */
    public function setVisible($visible = null)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool|null
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set codigo.
     *
     * @param int|null $codigo
     *
     * @return Municipio
     */
    public function setCodigo($codigo = null)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return int|null
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set provinciaid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Provincia|null $provinciaid
     *
     * @return Municipio
     */
    public function setProvinciaid(\Geocuba\PortadoresBundle\Entity\Provincia $provinciaid = null)
    {
        $this->provinciaid = $provinciaid;

        return $this;
    }

    /**
     * Get provinciaid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Provincia|null
     */
    public function getProvinciaid()
    {
        return $this->provinciaid;
    }
}
