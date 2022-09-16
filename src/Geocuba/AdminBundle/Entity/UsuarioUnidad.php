<?php

namespace Geocuba\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UsuarioUnidad
 *
 * @ORM\Table(name="admin.usuario_unidad", indexes={@ORM\Index(name="IDX_655B0446F3E6D02F", columns={"unidad"}), @ORM\Index(name="IDX_655B04462265B05D", columns={"usuario"})})
 * @ORM\Entity
 */
class UsuarioUnidad
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="admin.usuario_unidad_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;


    /**
     * @var \Geocuba\PortadoresBundle\Entity\Unidad
     *
     * @ORM\ManyToOne(targetEntity="\Geocuba\PortadoresBundle\Entity\Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad", referencedColumnName="id")
     * })
     */
    private $unidad;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario", referencedColumnName="id")
     * })
     */
    private $usuario;



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
     * Set unidad.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $unidad
     *
     * @return UsuarioUnidad
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

    /**
     * Set usuario.
     *
     * @param \Geocuba\AdminBundle\Entity\Usuario|null $usuario
     *
     * @return UsuarioUnidad
     */
    public function setUsuario(\Geocuba\AdminBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario.
     *
     * @return \Geocuba\AdminBundle\Entity\Usuario|null
     */
    public function getUsuario()
    {
        return $this->usuario;
    }
}
