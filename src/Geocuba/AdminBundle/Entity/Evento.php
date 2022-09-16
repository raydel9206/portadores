<?php

namespace Geocuba\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Geocuba\Utils\Functions;

/**
 * Evento
 *
 * @ORM\Table(name="admin.evento", indexes={@ORM\Index(name="evento_tipo", columns={"tipo"}), @ORM\Index(name="evento_entidad", columns={"entidad"}), @ORM\Index(name="evento_usuario", columns={"usuario"}), @ORM\Index(name="evento_id", columns={"id"}), @ORM\Index(name="evento_tabla", columns={"tabla"}), @ORM\Index(name="idx_a2de39ed1294ead8", columns={"entidad", "usuario", "tipo"})})
 * @ORM\Entity(repositoryClass="Geocuba\AdminBundle\Repository\EventoRepository")
 */
class Evento
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="admin.evento_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tabla", type="text", nullable=false)
     */
    private $tabla;

    /**
     * @var string
     *
     * @ORM\Column(name="entidad", type="text", nullable=false)
     */
    private $entidad;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="text", nullable=false)
     */
    private $usuario;

    /**
     * @var string|array
     *
     * @ORM\Column(name="datos", type="hstore", nullable=false)
     */
    private $datos;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo", type="smallint", nullable=false)
     */
    private $tipo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="datos_modificados", type="hstore", nullable=true)
     */
    private $datosModificados;


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
     * Set tabla.
     *
     * @param string $tabla
     *
     * @return Evento
     */
    public function setTabla($tabla)
    {
        $this->tabla = $tabla;

        return $this;
    }

    /**
     * Get tabla.
     *
     * @return string
     */
    public function getTabla()
    {
        return $this->tabla;
    }

    /**
     * Set entidad.
     *
     * @param string $entidad
     *
     * @return Evento
     */
    public function setEntidad($entidad)
    {
        $this->entidad = $entidad;

        return $this;
    }

    /**
     * Get entidad.
     *
     * @return string
     */
    public function getEntidad()
    {
        return $this->entidad;
    }

    /**
     * Set usuario.
     *
     * @param string $usuario
     *
     * @return Evento
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario.
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set datos.
     *
     * @param string|array $datos
     *
     * @return Evento
     */
    public function setDatos($datos)
    {
        $this->datos = $datos;

        return $this;
    }

    /**
     * Get datos.
     *
     * @return string|array
     */
    public function getDatos()
    {
        return $this->datos;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return Evento
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set tipo.
     *
     * @param int $tipo
     *
     * @return Evento
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
     * Set datosModificados.
     *
     * @param string|array|null $datosModificados
     *
     * @return Evento
     */
    public function setDatosModificados($datosModificados = null)
    {
        $this->datosModificados = $datosModificados;

        return $this;
    }

    /**
     * Get datosModificados.
     *
     * @return string|array|null
     */
    public function getDatosModificados()
    {
        return $this->datosModificados;
    }

    /**
     * Mapea el objeto a un arreglo donde cada elemento es una asociación atributo - valor del objeto.
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'tabla' => $this->tabla,
            'entidad' => $this->entidad,
            'usuario' => $this->usuario,
            'datos' => $this->datos, true,
            'datos_modificados' => $this->datosModificados,
            'fecha' => Functions::formatDateTime($this->fecha),
            'tipo' => $this->tipo
        ];
    }
}
