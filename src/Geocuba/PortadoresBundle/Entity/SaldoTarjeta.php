<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SaldoTarjeta
 *
 * @ORM\Table(name="datos.saldo_tarjeta", indexes={@ORM\Index(name="IDX_EB97E9B3700D1EF6", columns={"id_tarjeta"})})
 * @ORM\Entity
 */
class SaldoTarjeta
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
     * @var integer
     *
     * @ORM\Column(name="mes", type="integer", nullable=true)
     */
    private $mes;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

    /**
     * @var string
     *
     * @ORM\Column(name="saldo_inicial", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $saldoInicial;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tarjeta", referencedColumnName="id")
     * })
     */
    private $idTarjeta;


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
     * Set mes
     *
     * @param integer $mes
     *
     * @return SaldoTarjeta
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return integer
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set anno
     *
     * @param integer $anno
     *
     * @return SaldoTarjeta
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return integer
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set saldoInicial
     *
     * @param string $saldoInicial
     *
     * @return SaldoTarjeta
     */
    public function setSaldoInicial($saldoInicial)
    {
        $this->saldoInicial = $saldoInicial;

        return $this;
    }

    /**
     * Get saldoInicial
     *
     * @return string
     */
    public function getSaldoInicial()
    {
        return $this->saldoInicial;
    }

    /**
     * Set idTarjeta
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $idTarjeta
     *
     * @return SaldoTarjeta
     */
    public function setIdTarjeta(\Geocuba\PortadoresBundle\Entity\Tarjeta $idTarjeta = null)
    {
        $this->idTarjeta = $idTarjeta;

        return $this;
    }

    /**
     * Get idTarjeta
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getIdTarjeta()
    {
        return $this->idTarjeta;
    }
}
