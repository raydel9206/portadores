<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SubCuenta
 *
 * @ORM\Table(name="nomencladores.sub_cuenta", indexes={@ORM\Index(name="IDX_C3AB5DEA31C7BFCF", columns={"cuenta"}), @ORM\Index(name="IDX_C3AB5DEAB00B2B2D", columns={"moneda"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\SubcuentaRepository")
 */
class SubCuenta
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
     * @ORM\Column(name="nro_subcuenta", type="integer", nullable=true)
     */
    private $nroSubcuenta;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true, options={"default"="1"})
     */
    private $visible = true;

    /**
     * @var \Cuenta
     *
     * @ORM\ManyToOne(targetEntity="Cuenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cuenta", referencedColumnName="id")
     * })
     */
    private $cuenta;

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
     * Set nroSubcuenta.
     *
     * @param int|null $nroSubcuenta
     *
     * @return SubCuenta
     */
    public function setNroSubcuenta($nroSubcuenta = null)
    {
        $this->nroSubcuenta = $nroSubcuenta;
    
        return $this;
    }

    /**
     * Get nroSubcuenta.
     *
     * @return int|null
     */
    public function getNroSubcuenta()
    {
        return $this->nroSubcuenta;
    }

    /**
     * Set visible.
     *
     * @param bool|null $visible
     *
     * @return SubCuenta
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
     * Set cuenta.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Cuenta|null $cuenta
     *
     * @return SubCuenta
     */
    public function setCuenta(\Geocuba\PortadoresBundle\Entity\Cuenta $cuenta = null)
    {
        $this->cuenta = $cuenta;
    
        return $this;
    }

    /**
     * Get cuenta.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Cuenta|null
     */
    public function getCuenta()
    {
        return $this->cuenta;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return SubCuenta
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
