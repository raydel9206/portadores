<?php

namespace Geocuba\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Geocuba\Utils\{
    Functions, Constants
};

/**
 * Notificacion
 *
 * @ORM\Table(name="admin.notificacion", indexes={@ORM\Index(name="notificacion_id", columns={"id"}), @ORM\Index(name="idx_e2a32d52a76ed395", columns={"usuario_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\AdminBundle\Repository\NotificacionRepository")
 */
class Notificacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="admin.notificacion_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="string", length=255, nullable=false)
     */
    private $mensaje;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="smallint", nullable=false)
     */
    private $tipo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creacion", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="fecha_aceptacion", type="datetime", nullable=true)
     */
    private $fechaAceptacion;

    /**
     * @var \Geocuba\AdminBundle\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;


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
     * Set mensaje.
     *
     * @param string $mensaje
     *
     * @return Notificacion
     */
    public function setMensaje($mensaje)
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    /**
     * Get mensaje.
     *
     * @return string
     */
    public function getMensaje()
    {
        return $this->mensaje;
    }

    /**
     * Set tipo.
     *
     * @param int $tipo
     *
     * @return Notificacion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo.
     *
     * @return int
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Notificacion
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaAceptacion.
     *
     * @param \DateTime|null $fechaAceptacion
     *
     * @return Notificacion
     */
    public function setFechaAceptacion($fechaAceptacion = null)
    {
        $this->fechaAceptacion = $fechaAceptacion;

        return $this;
    }

    /**
     * Get fechaAceptacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAceptacion()
    {
        return $this->fechaAceptacion;
    }

    /**
     * Set usuario.
     *
     * @param \Geocuba\AdminBundle\Entity\Usuario|null $usuario
     *
     * @return Notificacion
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

    /**
     * Mapea el objeto a un arreglo donde cada elemento es una asociación atributo - valor del objeto.
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'mensaje' => $this->mensaje,
            'tipo' => Constants::NOTIFICACIONES[$this->tipo],

            'fecha_aceptacion' => Functions::formatDateTime($this->fechaAceptacion),
            'fecha_creacion' => Functions::formatDateTime($this->fechaCreacion),

            'usuario' => $this->usuario ? $this->usuario->toArray() : null
        ];
    }
}
