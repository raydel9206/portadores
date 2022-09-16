<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Unidad
 *
 * @ORM\Table(name="nomencladores.unidad", indexes={@ORM\Index(name="IDX_4E95D8ACFE98F5E0", columns={"municipio"}), @ORM\Index(name="IDX_4E95D8ACAFD41654", columns={"padreid"})})
 * @ORM\Entity
 */
class Unidad
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
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", nullable=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="siglas", type="string", nullable=true)
     */
    private $siglas;

    /**
     * @var bool
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false, options={"default"="1"})
     */
    private $visible = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", nullable=true)
     */
    private $codigo;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="mixta", type="boolean", nullable=true)
     */
    private $mixta = false;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nivel", type="string", length=10, nullable=true)
     */
    private $nivel;

    /**
     * @var \Municipio
     *
     * @ORM\ManyToOne(targetEntity="Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipio", referencedColumnName="id")
     * })
     */
    private $municipio;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="padreid", referencedColumnName="id")
     * })
     */
    private $padreid;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codfincimex", type="string", nullable=true)
     */
    private $codfincimex;



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
     * @param string|null $nombre
     *
     * @return Unidad
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set siglas.
     *
     * @param string|null $siglas
     *
     * @return Unidad
     */
    public function setSiglas($siglas = null)
    {
        $this->siglas = $siglas;

        return $this;
    }

    /**
     * Get siglas.
     *
     * @return string|null
     */
    public function getSiglas()
    {
        return $this->siglas;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return Unidad
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set codigo.
     *
     * @param string|null $codigo
     *
     * @return Unidad
     */
    public function setCodigo($codigo = null)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return string|null
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set codfincimex.
     *
     * @param string|null $codfincimex
     *
     * @return Unidad
     */
    public function setCodfincimex($codfincimex = null)
    {
        $this->codfincimex = $codfincimex;

        return $this;
    }

    /**
     * Get codfincimex.
     *
     * @return string|null
     */
    public function getCodfincimex()
    {
        return $this->codfincimex;
    }

    /**
     * Set mixta.
     *
     * @param bool|null $mixta
     *
     * @return Unidad
     */
    public function setMixta($mixta = null)
    {
        $this->mixta = $mixta;

        return $this;
    }

    /**
     * Get mixta.
     *
     * @return bool|null
     */
    public function getMixta()
    {
        return $this->mixta;
    }

    /**
     * Set nivel.
     *
     * @param string|null $nivel
     *
     * @return Unidad
     */
    public function setNivel($nivel = null)
    {
        $this->nivel = $nivel;

        return $this;
    }

    /**
     * Get nivel.
     *
     * @return string|null
     */
    public function getNivel()
    {
        return $this->nivel;
    }

    /**
     * Set municipio.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Municipio|null $municipio
     *
     * @return Unidad
     */
    public function setMunicipio(\Geocuba\PortadoresBundle\Entity\Municipio $municipio = null)
    {
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * Get municipio.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Municipio|null
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Set padreid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $padreid
     *
     * @return Unidad
     */
    public function setPadreid(\Geocuba\PortadoresBundle\Entity\Unidad $padreid = null)
    {
        $this->padreid = $padreid;

        return $this;
    }

    /**
     * Get padreid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getPadreid()
    {
        return $this->padreid;
    }
}
