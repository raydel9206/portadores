<?php

namespace Geocuba\PortadoresBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * AsignacionTecnologicos
 *
 * @ORM\Table(name="datos.asignacion_tecnologicos", indexes={@ORM\Index(name="IDX_B6FA572E9D01464C", columns={"unidad_id"}), @ORM\Index(name="IDX_B6FA572E596E597F", columns={"tipo_combustible_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\AsignacionTecnologicosRepository")
 */
class AsignacionTecnologicos
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
     * @ORM\Column(name="cantidad", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $cantidad;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    private $unidad;

    /**
     * @var TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible_id", referencedColumnName="id")
     * })
     */
    private $tipoCombustible;



    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set cantidad.
     *
     * @param string $cantidad
     *
     * @return AsignacionTecnologicos
     */
    public function setCantidad($cantidad): AsignacionTecnologicos
    {
        $this->cantidad = $cantidad;
    
        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return string
     */
    public function getCantidad(): string
    {
        return $this->cantidad;
    }

    /**
     * Set fecha.
     *
     * @param DateTime $fecha
     *
     * @return AsignacionTecnologicos
     */
    public function setFecha($fecha): AsignacionTecnologicos
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha.
     *
     * @return DateTime
     */
    public function getFecha(): DateTime
    {
        return $this->fecha;
    }

    /**
     * Set unidad.
     *
     * @param Unidad $unidad
     *
     * @return AsignacionTecnologicos
     */
    public function setUnidad(Unidad $unidad): AsignacionTecnologicos
    {
        $this->unidad = $unidad;
    
        return $this;
    }

    /**
     * Get unidad.
     *
     * @return Unidad
     */
    public function getUnidad(): Unidad
    {
        return $this->unidad;
    }

    /**
     * Set tipoCombustible.
     *
     * @param TipoCombustible $tipoCombustible
     *
     * @return AsignacionTecnologicos
     */
    public function setTipoCombustible(TipoCombustible $tipoCombustible): AsignacionTecnologicos
    {
        $this->tipoCombustible = $tipoCombustible;
    
        return $this;
    }

    /**
     * Get tipoCombustible.
     *
     * @return TipoCombustible
     */
    public function getTipoCombustible(): TipoCombustible
    {
        return $this->tipoCombustible;
    }
}
