<?php

namespace Geocuba\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Role\Role;

/**
 * Grupo
 *
 * @ORM\Table(name="admin.grupo", indexes={@ORM\Index(name="grupo_id", columns={"id"}), @ORM\Index(name="grupo_activo", columns={"activo"})})
 * @ORM\Entity
 */
class Grupo extends Role
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="admin.grupo_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false)
     */
    private $activo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Usuario", mappedBy="grupos")
     */
    private $usuarios;

    /**
     * Constructor
     * @param string $role
     */
    public function __construct($role)
    {
        $this->usuarios = new \Doctrine\Common\Collections\ArrayCollection();
        parent::__construct($role);
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
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return Grupo
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
     * Set activo.
     *
     * @param bool $activo
     *
     * @return Grupo
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo.
     *
     * @return bool
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Add usuario.
     *
     * @param \Geocuba\AdminBundle\Entity\Usuario $usuario
     *
     * @return Grupo
     */
    public function addUsuario(\Geocuba\AdminBundle\Entity\Usuario $usuario)
    {
        $this->usuarios[] = $usuario;

        return $this;
    }

    /**
     * Remove usuario.
     *
     * @param \Geocuba\AdminBundle\Entity\Usuario $usuario
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUsuario(\Geocuba\AdminBundle\Entity\Usuario $usuario)
    {
        return $this->usuarios->removeElement($usuario);
    }

    /**
     * Get usuarios.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }

    /**
     * Mapea el objeto a un arreglo donde cada elemento es una asociación atributo - valor del objeto.
     * @param bool $simple
     * @return array
     */
    public function toArray($simple = true)
    {
        $usuarios = $simple ? [] : array_values($this->usuarios->filter(function (Usuario $usuario) {
            return $usuario->getActivo();
        })->map(function (Usuario $usuario) {
            return $usuario->toArray(true);
        })->toArray());

        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'activo' => $this->activo,

            'usuarios' => $usuarios
        ];
    }
}
