<?php

namespace Geocuba\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Recurso
 *
 * @ORM\Table(name="admin.recurso", indexes={@ORM\Index(name="idx_35aeb6efe54d947", columns={"grupo_id"}), @ORM\Index(name="recurso_id", columns={"id"}), @ORM\Index(name="recurso_grupo_id", columns={"grupo_id"}), @ORM\Index(name="idx_a5i8dea6769aa4ad", columns={"ruta", "nombre", "grupo_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\AdminBundle\Repository\RecursoRepository")
 */
class Recurso
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="admin.recurso_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="ruta", type="text", nullable=false)
     */
    private $ruta;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="text", nullable=false)
     */
    private $nombre;

    /**
     * @var \Geocuba\AdminBundle\Entity\Grupo
     *
     * @ORM\ManyToOne(targetEntity="Grupo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grupo_id", referencedColumnName="id")
     * })
     */
    private $grupo;

    /**
     * Recurso constructor.
     * @param string $ruta
     * @param string $nombre
     * @param Grupo $grupo
     */
    public function __construct($ruta, $nombre, Grupo $grupo)
    {
        $this->ruta = $ruta;
        $this->nombre = $nombre;
        $this->grupo = $grupo;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ruta.
     *
     * @param string $ruta
     *
     * @return Recurso
     */
    public function setRuta($ruta)
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * Get ruta.
     *
     * @return string
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Recurso
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
     * Set grupo.
     *
     * @param \Geocuba\AdminBundle\Entity\Grupo|null $grupo
     *
     * @return Recurso
     */
    public function setGrupo(\Geocuba\AdminBundle\Entity\Grupo $grupo = null)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo.
     *
     * @return \Geocuba\AdminBundle\Entity\Grupo|null
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * @param \Geocuba\AdminBundle\Entity\Recurso $obj
     * @return boolean
     */
    function equalTo($obj)
    {
        return $this->ruta === $obj->ruta && $this->nombre === $obj->nombre && $this->grupo === $obj->grupo;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return sprintf('%s - %s', $this->ruta, $this->nombre);
    }
}
