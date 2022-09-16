<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChequeFincimex
 *
 * @ORM\Table(name="nomencladores.cheque_fincimex", indexes={@ORM\Index(name="IDX_D1F878AA3C7CEFFE", columns={"nunidadid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ChequeFincimexRepository")
 */
class ChequeFincimex
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
     * @ORM\Column(name="no_cheque", type="string", length=255, nullable=false)
     */
    private $noCheque;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_total", type="decimal", precision=8, scale=3, nullable=false)
     */
    private $montoTotal;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_registro", type="datetime", nullable=false)
     */
    private $fechaRegistro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_deposito", type="datetime", nullable=true)
     */
    private $fechaDeposito;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;

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
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set noCheque
     *
     * @param string $noCheque
     *
     * @return ChequeFincimex
     */
    public function setNoCheque($noCheque)
    {
        $this->noCheque = $noCheque;

        return $this;
    }

    /**
     * Get noCheque
     *
     * @return string
     */
    public function getNoCheque()
    {
        return $this->noCheque;
    }

    /**
     * Set montoTotal
     *
     * @param string $montoTotal
     *
     * @return ChequeFincimex
     */
    public function setMontoTotal($montoTotal)
    {
        $this->montoTotal = $montoTotal;

        return $this;
    }

    /**
     * Get montoTotal
     *
     * @return string
     */
    public function getMontoTotal()
    {
        return $this->montoTotal;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return ChequeFincimex
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
     * Set fechaRegistro
     *
     * @param \DateTime $fechaRegistro
     *
     * @return ChequeFincimex
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    /**
     * Get fechaRegistro
     *
     * @return \DateTime
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set fechaDeposito
     *
     * @param \DateTime $fechaDeposito
     *
     * @return ChequeFincimex
     */
    public function setFechaDeposito($fechaDeposito)
    {
        $this->fechaDeposito = $fechaDeposito;

        return $this;
    }

    /**
     * Get fechaDeposito
     *
     * @return \DateTime
     */
    public function getFechaDeposito()
    {
        return $this->fechaDeposito;
    }

    /**
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return ChequeFincimex
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
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return ChequeFincimex
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
