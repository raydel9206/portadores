<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistorialTarjeta
 *
 * @ORM\Table(name="datos.historial_tarjeta", uniqueConstraints={@ORM\UniqueConstraint(name="historial_tarjeta_tarjetaid_fecha_nro_vale_key", columns={"tarjetaid", "fecha", "nro_vale"})}, indexes={@ORM\Index(name="IDX_B2B80D4C4D0472A6", columns={"liquidacionid"}), @ORM\Index(name="IDX_B2B80D4C87E5D022", columns={"tarjetaid"}), @ORM\Index(name="IDX_B2B80D4CCFFD7DF2", columns={"racargaid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\HistorialTarjetaRepository")
 */
class HistorialTarjeta
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id = 'clone.get_id()';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="entrada_importe", type="decimal", precision=8, scale=2, nullable=true)
     */
    private $entradaImporte = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="entrada_cantidad", type="decimal", precision=8, scale=2, nullable=true)
     */
    private $entradaCantidad = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="salida_importe", type="decimal", precision=8, scale=2, nullable=true)
     */
    private $salidaImporte = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="salida_cantidad", type="decimal", precision=8, scale=2, nullable=true)
     */
    private $salidaCantidad = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="existencia_importe", type="decimal", precision=8, scale=2, nullable=false)
     */
    private $existenciaImporte;

    /**
     * @var string
     *
     * @ORM\Column(name="existencia_cantidad", type="decimal", precision=8, scale=2, nullable=false)
     */
    private $existenciaCantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_vale", type="string", nullable=true)
     */
    private $nroVale = 's/n';

    /**
     * @var string
     *
     * @ORM\Column(name="nro_factura", type="string", nullable=true)
     */
    private $nroFactura = 's/n';

    /**
     * @var boolean
     *
     * @ORM\Column(name="cancelado", type="boolean", nullable=true)
     */
    private $cancelado = false;

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
     * @ORM\Column(name="saldo_inicial", type="decimal", precision=8, scale=2, nullable=true)
     */
    private $saldoInicial = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="saldo_inicial_cantidad", type="decimal", precision=8, scale=2, nullable=true)
     */
    private $saldoInicialCantidad = '0.00';

    /**
     * @var \Liquidacion
     *
     * @ORM\ManyToOne(targetEntity="Liquidacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="liquidacionid", referencedColumnName="id")
     * })
     */
    private $liquidacionid;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tarjetaid", referencedColumnName="id")
     * })
     */
    private $tarjetaid;

    /**
     * @var \HistorialContableRecarga
     *
     * @ORM\ManyToOne(targetEntity="HistorialContableRecarga")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="racargaid", referencedColumnName="id")
     * })
     */
    private $racargaid;

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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return HistorialTarjeta
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
     * Set entradaImporte
     *
     * @param string $entradaImporte
     *
     * @return HistorialTarjeta
     */
    public function setEntradaImporte($entradaImporte)
    {
        $this->entradaImporte = $entradaImporte;

        return $this;
    }

    /**
     * Get entradaImporte
     *
     * @return string
     */
    public function getEntradaImporte()
    {
        return $this->entradaImporte;
    }

    /**
     * Set entradaCantidad
     *
     * @param string $entradaCantidad
     *
     * @return HistorialTarjeta
     */
    public function setEntradaCantidad($entradaCantidad)
    {
        $this->entradaCantidad = $entradaCantidad;

        return $this;
    }

    /**
     * Get entradaCantidad
     *
     * @return string
     */
    public function getEntradaCantidad()
    {
        return $this->entradaCantidad;
    }

    /**
     * Set salidaImporte
     *
     * @param string $salidaImporte
     *
     * @return HistorialTarjeta
     */
    public function setSalidaImporte($salidaImporte)
    {
        $this->salidaImporte = $salidaImporte;

        return $this;
    }

    /**
     * Get salidaImporte
     *
     * @return string
     */
    public function getSalidaImporte()
    {
        return $this->salidaImporte;
    }

    /**
     * Set salidaCantidad
     *
     * @param string $salidaCantidad
     *
     * @return HistorialTarjeta
     */
    public function setSalidaCantidad($salidaCantidad)
    {
        $this->salidaCantidad = $salidaCantidad;

        return $this;
    }

    /**
     * Get salidaCantidad
     *
     * @return string
     */
    public function getSalidaCantidad()
    {
        return $this->salidaCantidad;
    }

    /**
     * Set existenciaImporte
     *
     * @param string $existenciaImporte
     *
     * @return HistorialTarjeta
     */
    public function setExistenciaImporte($existenciaImporte)
    {
        $this->existenciaImporte = $existenciaImporte;

        return $this;
    }

    /**
     * Get existenciaImporte
     *
     * @return string
     */
    public function getExistenciaImporte()
    {
        return $this->existenciaImporte;
    }

    /**
     * Set existenciaCantidad
     *
     * @param string $existenciaCantidad
     *
     * @return HistorialTarjeta
     */
    public function setExistenciaCantidad($existenciaCantidad)
    {
        $this->existenciaCantidad = $existenciaCantidad;

        return $this;
    }

    /**
     * Get existenciaCantidad
     *
     * @return string
     */
    public function getExistenciaCantidad()
    {
        return $this->existenciaCantidad;
    }

    /**
     * Set nroVale
     *
     * @param string $nroVale
     *
     * @return HistorialTarjeta
     */
    public function setNroVale($nroVale)
    {
        $this->nroVale = $nroVale;

        return $this;
    }

    /**
     * Get nroVale
     *
     * @return string
     */
    public function getNroVale()
    {
        return $this->nroVale;
    }

    /**
     * Set nroFactura
     *
     * @param string $nroFactura
     *
     * @return HistorialTarjeta
     */
    public function setNroFactura($nroFactura)
    {
        $this->nroFactura = $nroFactura;

        return $this;
    }

    /**
     * Get nroFactura
     *
     * @return string
     */
    public function getNroFactura()
    {
        return $this->nroFactura;
    }

    /**
     * Set cancelado
     *
     * @param boolean $cancelado
     *
     * @return HistorialTarjeta
     */
    public function setCancelado($cancelado)
    {
        $this->cancelado = $cancelado;

        return $this;
    }

    /**
     * Get cancelado
     *
     * @return boolean
     */
    public function getCancelado()
    {
        return $this->cancelado;
    }

    /**
     * Set mes
     *
     * @param integer $mes
     *
     * @return HistorialTarjeta
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
     * @return HistorialTarjeta
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
     * @return HistorialTarjeta
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
     * Set saldoInicialCantidad
     *
     * @param string $saldoInicialCantidad
     *
     * @return HistorialTarjeta
     */
    public function setSaldoInicialCantidad($saldoInicialCantidad)
    {
        $this->saldoInicialCantidad = $saldoInicialCantidad;

        return $this;
    }

    /**
     * Get saldoInicialCantidad
     *
     * @return string
     */
    public function getSaldoInicialCantidad()
    {
        return $this->saldoInicialCantidad;
    }

    /**
     * Set liquidacionid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Liquidacion $liquidacionid
     *
     * @return HistorialTarjeta
     */
    public function setLiquidacionid(\Geocuba\PortadoresBundle\Entity\Liquidacion $liquidacionid = null)
    {
        $this->liquidacionid = $liquidacionid;

        return $this;
    }

    /**
     * Get liquidacionid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Liquidacion
     */
    public function getLiquidacionid()
    {
        return $this->liquidacionid;
    }

    /**
     * Set tarjetaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $tarjetaid
     *
     * @return HistorialTarjeta
     */
    public function setTarjetaid(\Geocuba\PortadoresBundle\Entity\Tarjeta $tarjetaid = null)
    {
        $this->tarjetaid = $tarjetaid;

        return $this;
    }

    /**
     * Get tarjetaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getTarjetaid()
    {
        return $this->tarjetaid;
    }

    /**
     * Set racargaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\HistorialContableRecarga $racargaid
     *
     * @return HistorialTarjeta
     */
    public function setRacargaid(\Geocuba\PortadoresBundle\Entity\HistorialContableRecarga $racargaid = null)
    {
        $this->racargaid = $racargaid;

        return $this;
    }

    /**
     * Get racargaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\HistorialContableRecarga
     */
    public function getRacargaid()
    {
        return $this->racargaid;
    }
}
