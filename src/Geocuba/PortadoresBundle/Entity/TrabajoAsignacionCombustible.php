<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TrabajoAsignacionCombustible
 *
 * @ORM\Table(name="nomencladores.trabajo_asignacion_combustible", indexes={@ORM\Index(name="IDX_6B41D4CA4EC221B2", columns={"tipo_combustible"}), @ORM\Index(name="IDX_6B41D4CAB00B2B2D", columns={"moneda"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TrabajoAsignacionCombustibleRepository")
 */
class TrabajoAsignacionCombustible
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
     * @var TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible", referencedColumnName="id")
     * })
     */
    private $tipoCombustible;

    /**
     * @var Moneda
     *
     * @ORM\ManyToOne(targetEntity="Moneda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="moneda", referencedColumnName="id")
     * })
     */
    private $moneda;

    /**
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=0, nullable=true)
     */
    private $cantidad = '0';

    /**
     * @var Trabajo
     *
     * @ORM\ManyToOne(targetEntity="Trabajo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="trabajoid", referencedColumnName="id")
     * })
     */
    private $trabajoid;



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
     * Set tipoCombustible
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible $tipoCombustible
     *
     * @return TrabajoAsignacionCombustible
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
     * Set moneda
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda $moneda
     *
     * @return TrabajoAsignacionCombustible
     */
    public function setMoneda(\Geocuba\PortadoresBundle\Entity\Moneda $moneda = null)
    {
        $this->moneda = $moneda;

        return $this;
    }

    /**
     * Get moneda
     *
     * @return \Geocuba\PortadoresBundle\Entity\Moneda
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return TrabajoAsignacionCombustible
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
     * Set trabajoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Trabajo $trabajoid
     *
     * @return TrabajoAsignacionCombustible
     */
    public function setTrabajoid(\Geocuba\PortadoresBundle\Entity\Trabajo $trabajoid = null)
    {
        $this->trabajoid = $trabajoid;

        return $this;
    }

    /**
     * Get trabajoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Trabajo
     */
    public function getTrabajoid()
    {
        return $this->trabajoid;
    }
}
