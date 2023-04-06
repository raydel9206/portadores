<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cuenta
 *
 * @ORM\Table(name="nomencladores.cuenta", indexes={@ORM\Index(name="IDX_8CB4B74CF3E6D02F", columns={"unidad"}), @ORM\Index(name="IDX_8CB4B74CCF5DE994", columns={"clasificador"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\CuentaRepository")
 */
class Cuenta
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
     * @var int|null
     *
     * @ORM\Column(name="nro_cuenta", type="integer", nullable=true)
     */
    private $nroCuenta;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true, options={"default"="1"})
     */
    private $visible = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=true)
     */
    private $descripcion;

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
     * @var \Clasificador
     *
     * @ORM\ManyToOne(targetEntity="Clasificador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="clasificador", referencedColumnName="id")
     * })
     */
    private $clasificador;



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
     * Set nroCuenta.
     *
     * @param int|null $nroCuenta
     *
     * @return Cuenta
     */
    public function setNroCuenta($nroCuenta = null)
    {
        $this->nroCuenta = $nroCuenta;
    
        return $this;
    }

    /**
     * Get nroCuenta.
     *
     * @return int|null
     */
    public function getNroCuenta()
    {
        return $this->nroCuenta;
    }

    /**
     * Set visible.
     *
     * @param bool|null $visible
     *
     * @return Cuenta
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
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return Cuenta
     */
    public function setDescripcion($descripcion = null)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string|null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set unidad.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $unidad
     *
     * @return Cuenta
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
     * Set clasificador.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Clasificador|null $clasificador
     *
     * @return Cuenta
     */
    public function setClasificador(\Geocuba\PortadoresBundle\Entity\Clasificador $clasificador = null)
    {
        $this->clasificador = $clasificador;
    
        return $this;
    }

    /**
     * Get clasificador.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Clasificador|null
     */
    public function getClasificador()
    {
        return $this->clasificador;
    }
}
