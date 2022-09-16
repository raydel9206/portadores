<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DistribucionCombustible
 *
 * @ORM\Table(name="datos.distribucion_combustible", indexes={@ORM\Index(name="IDX_343BE3934EC221B2", columns={"tipo_combustible"}), @ORM\Index(name="IDX_343BE3933C7CEFFE", columns={"nunidadid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\DistribucionCombustibleRepository")
 */
class DistribucionCombustible
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
     * @ORM\Column(name="denominacion", type="string", length=255, nullable=false)
     */
    private $denominacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

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
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=0, nullable=false)
     */
    private $cantidad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var tipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible", referencedColumnName="id")
     * })
     */
    private $tipoCombustible;

    /**
     * @var unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="aprobada", type="boolean", nullable=true)
     */
    private $aprobada;



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
     * Set denominacion
     *
     * @param string $denominacion
     *
     * @return DistribucionCombustible
     */
    public function setDenominacion($denominacion)
    {
        $this->denominacion = $denominacion;

        return $this;
    }

    /**
     * Get denominacion
     *
     * @return string
     */
    public function getDenominacion()
    {
        return $this->denominacion;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return DistribucionCombustible
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return DistribucionCombustible
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return float
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Nvehiculo
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
     * Set tipoCombustible
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible $tipoCombustible
     *
     * @return DistribucionCombustible
     */
    public function setTipoCombustible(\Geocuba\PortadoresBundle\Entity\TipoCombustible $tipoCombustible = null)
    {
        $this->tipoCombustible = $tipoCombustible;

        return $this;
    }

    /**
     * Get tipoCombustible
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoCombustible
     */
    public function getTipoCombustible()
    {
        return $this->tipoCombustible;
    }

    /**
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return DistribucionCombustible
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
     * Set anno.
     *
     * @param int $anno
     *
     * @return DistribucionCombustible
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
     * @return DistribucionCombustible
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
     * Set aprobada.
     *
     * @param bool|null $aprobada
     *
     * @return DistribucionCombustible
     */
    public function setAprobada($aprobada = null)
    {
        $this->aprobada = $aprobada;

        return $this;
    }

    /**
     * Get aprobada.
     *
     * @return bool|null
     */
    public function getAprobada()
    {
        return $this->aprobada;
    }
}
