<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Asignacion
 *
 * @ORM\Table(name="datos.asignacion")
 * @ORM\Entity
 */
class Asignacion
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=150, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     */
    private $denominacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=true)
     */
    private $fecha;

    /**
     * @var \TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible", referencedColumnName="id")
     * })
     */
    private $tipoCombustible;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad", referencedColumnName="id")
     * })
     */
    private $unidad;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=0, nullable=true)
     */
    private $cantidad = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="disponible", type="float", precision=10, scale=0, nullable=true)
     */
    private $disponible = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=false)
     */
    private $anno;

    /**
     * @var integer
     *
     * @ORM\Column(name="mes", type="integer", nullable=false)
     */
    private $mes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="para_mes", type="date", nullable=true)
     */
    private $paraMes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="modificable", type="boolean", nullable=true)
     */
    private $modificable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var \Moneda
     *
     * @ORM\ManyToOne(targetEntity="Moneda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="moneda", referencedColumnName="id")
     * })
     */
    private $moneda;

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
     * Set fecha.
     *
     * @param \DateTime|null $fecha
     *
     * @return Asignacion
     */
    public function setFecha($fecha = null)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime|null
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set cantidad.
     *
     * @param float|null $cantidad
     *
     * @return Asignacion
     */
    public function setCantidad($cantidad = null)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return float|null
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set tipoCombustible.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible|null $tipoCombustible
     *
     * @return Asignacion
     */
    public function setTipoCombustible(\Geocuba\PortadoresBundle\Entity\TipoCombustible $tipoCombustible = null)
    {
        $this->tipoCombustible = $tipoCombustible;

        return $this;
    }

    /**
     * Get tipoCombustible.
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoCombustible|null
     */
    public function getTipoCombustible()
    {
        return $this->tipoCombustible;
    }

    /**
     * Set unidad.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $unidad
     *
     * @return Asignacion
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
     * Set disponible.
     *
     * @param float|null $disponible
     *
     * @return Asignacion
     */
    public function setDisponible($disponible = null)
    {
        $this->disponible = $disponible;

        return $this;
    }

    /**
     * Get disponible.
     *
     * @return float|null
     */
    public function getDisponible()
    {
        return $this->disponible;
    }

    /**
     * Set denominacion.
     *
     * @param string $denominacion
     *
     * @return Asignacion
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion.
     *
     * @return string
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * Set visible.
     *
     * @param bool|null $visible
     *
     * @return Asignacion
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
     * Set anno.
     *
     * @param int $anno
     *
     * @return Asignacion
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno.
     *
     * @return int
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set mes.
     *
     * @param int $mes
     *
     * @return Asignacion
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes.
     *
     * @return int
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set modificable.
     *
     * @param bool|null $modificable
     *
     * @return Asignacion
     */
    public function setModificable($modificable = null)
    {
        $this->modificable = $modificable;

        return $this;
    }

    /**
     * Get modificable.
     *
     * @return bool|null
     */
    public function getModificable()
    {
        return $this->modificable;
    }

    /**
     * Set paraMes.
     *
     * @param \DateTime|null $paraMes
     *
     * @return Asignacion
     */
    public function setParaMes($paraMes = null)
    {
        $this->paraMes = $paraMes;
    
        return $this;
    }

    /**
     * Get paraMes.
     *
     * @return \DateTime|null
     */
    public function getParaMes()
    {
        return $this->paraMes;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return Asignacion
     */
    public function setMoneda(\Geocuba\PortadoresBundle\Entity\Moneda $moneda = null)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Moneda|null
     */
    public function getMoneda()
    {
        return $this->moneda;
    }
}
