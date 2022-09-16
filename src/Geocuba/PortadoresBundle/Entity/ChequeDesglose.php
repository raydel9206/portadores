<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ChequeDesglose
 *
 * @ORM\Table(name="datos.cheque_desglose")
 * @ORM\Entity
 */
class ChequeDesglose
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
     * @var \ChequeFincimex
     *
     * @ORM\ManyToOne(targetEntity="ChequeFincimex")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cheque", referencedColumnName="id")
     * })
     */
    private $cheque;

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
     * @ORM\Column(name="monto", type="decimal", precision=8, scale=3, nullable=false)
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="litros", type="decimal", precision=8, scale=3, nullable=false)
     */
    private $litros;

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
     * @return ChequeDesglose
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
     * Set litros.
     *
     * @param string $litros
     *
     * @return ChequeDesglose
     */
    public function setLitros($litros)
    {
        $this->litros = $litros;
    
        return $this;
    }

    /**
     * Get litros.
     *
     * @return string
     */
    public function getLitros()
    {
        return $this->litros;
    }

    /**
     * Set cheque.
     *
     * @param \Geocuba\PortadoresBundle\Entity\ChequeFincimex|null $cheque
     *
     * @return ChequeDesglose
     */
    public function setCheque(\Geocuba\PortadoresBundle\Entity\ChequeFincimex $cheque = null)
    {
        $this->cheque = $cheque;
    
        return $this;
    }

    /**
     * Get cheque.
     *
     * @return \Geocuba\PortadoresBundle\Entity\ChequeFincimex|null
     */
    public function getCheque()
    {
        return $this->cheque;
    }

    /**
     * Set tipoCombustible.
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible|null $tipoCombustible
     *
     * @return ChequeDesglose
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
}
