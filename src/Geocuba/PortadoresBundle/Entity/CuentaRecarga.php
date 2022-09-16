<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaRecarga
 *
 * @ORM\Table(name="datos.cuenta_recarga", indexes={@ORM\Index(name="IDX_D2DFDC944C1994A", columns={"unidad"})})
 * @ORM\Entity
 */
class CuentaRecarga
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
     * @var \Moneda
     *
     * @ORM\ManyToOne(targetEntity="Moneda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="moneda", referencedColumnName="id")
     * })
     */
    private $moneda;

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
     * @var string
     *
     * @ORM\Column(name="monto", type="decimal", precision=20, scale=3, nullable=false)
     */
    private $monto = '0';



//    /**
//     * @var string
//     *
//     * @ORM\Column(name="monto_diesel", type="decimal", precision=8, scale=3, nullable=false)
//     */
//    private $montoDiesel = '0';
//
//    /**
//     * @var string
//     *
//     * @ORM\Column(name="monto_gasolina", type="decimal", precision=8, scale=3, nullable=false)
//     */
//    private $montoGasolina = '0';

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
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set monto.
     *
     * @param string $monto
     *
     * @return CuentaRecarga
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;
    
        return $this;
    }

    /**
     * Get monto.
     *
     * @return string
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return CuentaRecarga
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

    /**
     * Set tipoCombustible.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible|null $tipoCombustible
     *
     * @return CuentaRecarga
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
     * @return CuentaRecarga
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
}
