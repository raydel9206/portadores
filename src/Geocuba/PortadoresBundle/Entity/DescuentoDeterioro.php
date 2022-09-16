<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DescuentoDeterioro
 *
 * @ORM\Table(name="datos.descuento_deterioro")
 * @ORM\Entity
 */
class DescuentoDeterioro
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
     * @var float
     *
     * @ORM\Column(name="cantidad", type="float", precision=10, scale=0, nullable=true)
     */
    private $cantidad;

    /**
     * @var float
     *
     * @ORM\Column(name="mes", type="float", precision=10, scale=0, nullable=true)
     */
    private $mes;

    /**
     * @var float
     *
     * @ORM\Column(name="acumulado", type="float", precision=10, scale=0, nullable=true)
     */
    private $acumulado;



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
     * Set cantidad
     *
     * @param float $cantidad
     *
     * @return DescuentoDeterioro
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
     * Set mes
     *
     * @param float $mes
     *
     * @return DescuentoDeterioro
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return float
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set acumulado
     *
     * @param float $acumulado
     *
     * @return DescuentoDeterioro
     */
    public function setAcumulado($acumulado)
    {
        $this->acumulado = $acumulado;

        return $this;
    }

    /**
     * Get acumulado
     *
     * @return float
     */
    public function getAcumulado()
    {
        return $this->acumulado;
    }
}
