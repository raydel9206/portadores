<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaGasto
 *
 * @ORM\Table(name="nomencladores.cuenta_gasto", indexes={@ORM\Index(name="IDX_1C12746D3C7CEFFE", columns={"centro_costo"}), @ORM\Index(name="IDX_1C12746D4EC221B2", columns={"elemento_gasto"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\CuentaGastoRepository")
 */
class CuentaGasto
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
     * @ORM\Column(name="no_cuenta", type="string", length=20, nullable=false)
     */
    private $noCuenta;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="text", nullable=false)
     */
    private $descripcion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var \CentroCosto
     *
     * @ORM\ManyToOne(targetEntity="CentroCosto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="centro_costo", referencedColumnName="id")
     * })
     */
    private $centroCosto;

    /**
     * @var \ElementoGasto
     *
     * @ORM\ManyToOne(targetEntity="ElementoGasto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="elemento_gasto", referencedColumnName="id")
     * })
     */
    private $elementoGasto;

    /**
     * @var \DetalleGasto
     *
     * @ORM\ManyToOne(targetEntity="DetalleGasto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="detalle_gasto", referencedColumnName="id")
     * })
     */
    private $detalleGasto;

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
     * Set noCuenta.
     *
     * @param string $noCuenta
     *
     * @return CuentaGasto
     */
    public function setNoCuenta($noCuenta)
    {
        $this->noCuenta = $noCuenta;

        return $this;
    }

    /**
     * Get noCuenta.
     *
     * @return string
     */
    public function getNoCuenta()
    {
        return $this->noCuenta;
    }

    /**
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return CuentaGasto
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set visible.
     *
     * @param bool $visible
     *
     * @return CuentaGasto
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set centroCosto.
     *
     * @param \Geocuba\PortadoresBundle\Entity\CentroCosto|null $centroCosto
     *
     * @return CuentaGasto
     */
    public function setCentroCosto(\Geocuba\PortadoresBundle\Entity\CentroCosto $centroCosto = null)
    {
        $this->centroCosto = $centroCosto;

        return $this;
    }

    /**
     * Get centroCosto.
     *
     * @return \Geocuba\PortadoresBundle\Entity\CentroCosto|null
     */
    public function getCentroCosto()
    {
        return $this->centroCosto;
    }

    /**
     * Set elementoGasto.
     *
     * @param \Geocuba\PortadoresBundle\Entity\ElementoGasto|null $elementoGasto
     *
     * @return CuentaGasto
     */
    public function setElementoGasto(\Geocuba\PortadoresBundle\Entity\ElementoGasto $elementoGasto = null)
    {
        $this->elementoGasto = $elementoGasto;

        return $this;
    }

    /**
     * Get elementoGasto.
     *
     * @return \Geocuba\PortadoresBundle\Entity\ElementoGasto|null
     */
    public function getElementoGasto()
    {
        return $this->elementoGasto;
    }

    /**
     * Set detalleGasto.
     *
     * @param \Geocuba\PortadoresBundle\Entity\DetalleGasto|null $detalleGasto
     *
     * @return CuentaGasto
     */
    public function setDetalleGasto(\Geocuba\PortadoresBundle\Entity\DetalleGasto $detalleGasto = null)
    {
        $this->detalleGasto = $detalleGasto;

        return $this;
    }

    /**
     * Get detalleGasto.
     *
     * @return \Geocuba\PortadoresBundle\Entity\DetalleGasto|null
     */
    public function getDetalleGasto()
    {
        return $this->detalleGasto;
    }
}
