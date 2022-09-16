<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Persona
 *
 * @ORM\Table(name="nomencladores.persona", indexes={@ORM\Index(name="IDX_72BFFACC3C7CEFFE", columns={"nunidadid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\PersonaRepository")
 */
class Persona
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
     * @ORM\Column(name="nombre", type="string", length=350, nullable=false)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="ci", type="string", length=11, nullable=false)
     */
    private $ci;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=700, nullable=true)
     */
    private $direccion;

    /**
     * @var integer
     *
     * @ORM\Column(name="telefono", type="string", length=20, nullable=false)
     */
    private $telefono;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;

    /**
     * @var Cargo
     *
     * @ORM\ManyToOne(targetEntity="Cargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cargoid", referencedColumnName="id")
     * })
     */
    private $cargoid;


    /**
     * @var integer
     *
     * @ORM\Column(name="operario_taller", type="integer", nullable=true)
     */
    private $operarioTaller;

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
     * @return Persona
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
     * Set ci
     *
     * @param string $ci
     *
     * @return Persona
     */
    public function setCi($ci)
    {
        $this->ci = $ci;

        return $this;
    }

    /**
     * Get ci
     *
     * @return string
     */
    public function getCi()
    {
        return $this->ci;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Persona
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
     * Set telefono
     *
     * @param string $telefono
     *
     * @return Persona
     */
    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    /**
     * Get telefono
     *
     * @return string
     */
    public function getTelefono()
    {
        return $this->telefono;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Persona
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
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return Persona
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
     * Set cargoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Cargo $cargoid
     *
     * @return Persona
     */
    public function setCargoid(\Geocuba\PortadoresBundle\Entity\Cargo $cargoid = null)
    {
        $this->cargoid = $cargoid;

        return $this;
    }

    /**
     * Get cargoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Cargo
     */
    public function getCargoid()
    {
        return $this->cargoid;
    }

    /**
     * @return int
     */
    public function getOperarioTaller()
    {
        return $this->operarioTaller;
    }

    /**
     * @param int $operarioTaller
     */
    public function setOperarioTaller($operarioTaller)
    {
        $this->operarioTaller = $operarioTaller;
    }
}
