<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Datos.modelo5073
 *
 * @ORM\Table(name="datos.modelo5073", indexes={@ORM\Index(name="IDX_2581DF94C0044659", columns={"producto_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\Modelo5073Repository")
 */
class Modelo5073
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
     * @var int|null
     *
     * @ORM\Column(name="mes", type="integer", nullable=true, options={"comment"="

"})
     */
    private $mes;

    /**
     * @var int|null
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inventario_inicial_fisico", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $inventarioInicialFisico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="compras_cupet", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $comprasCupet;

    /**
     * @var string|null
     *
     * @ORM\Column(name="otras_entradas", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $otrasEntradas;

    /**
     * @var string|null
     *
     * @ORM\Column(name="consumo_directo", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $consumoDirecto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="consumo_indirecto", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $consumoIndirecto;

    /**
     * @var string|null
     *
     * @ORM\Column(name="otras_salidas", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $otrasSalidas;

    /**
     * @var string|null
     *
     * @ORM\Column(name="inventario_final_fisico", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $inventarioFinalFisico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="asignado_mes", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $asignadoMes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="recibido_efectua_cargar", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $recibidoEfectuaCargar;

    /**
     * @var string|null
     *
     * @ORM\Column(name="consumo", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $consumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="entrega_consumo", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $entregaConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="saldo_final_total", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $saldoFinalTotal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="saldo_final_utilizar_proximo_mes", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $saldoFinalUtilizarProximoMes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="saldo_final_disponible_fincimex", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $saldoFinalDisponibleFincimex;

    /**
     * @var string|null
     *
     * @ORM\Column(name="acumulado_real", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $acumuladoReal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="acumulado_anno_anterior", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $acumuladoAnnoAnterior;

    /**
     * @var \Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto_id", referencedColumnName="id")
     * })
     * @ORM\OrderBy({"fila" = "ASC"})
     */
    private $producto;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidadid", referencedColumnName="id")
     * })
     */
    private $unidadid;



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
     * Set mes.
     *
     * @param int|null $mes
     *
     * @return Modelo5073
     */
    public function setMes($mes = null)
    {
        $this->mes = $mes;
    
        return $this;
    }

    /**
     * Get mes.
     *
     * @return int|null
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set anno.
     *
     * @param int|null $anno
     *
     * @return Modelo5073
     */
    public function setAnno($anno = null)
    {
        $this->anno = $anno;
    
        return $this;
    }

    /**
     * Get anno.
     *
     * @return int|null
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set inventarioInicialFisico.
     *
     * @param string|null $inventarioInicialFisico
     *
     * @return Modelo5073
     */
    public function setInventarioInicialFisico($inventarioInicialFisico = null)
    {
        $this->inventarioInicialFisico = $inventarioInicialFisico;
    
        return $this;
    }

    /**
     * Get inventarioInicialFisico.
     *
     * @return string|null
     */
    public function getInventarioInicialFisico()
    {
        return $this->inventarioInicialFisico;
    }

    /**
     * Set comprasCupet.
     *
     * @param string|null $comprasCupet
     *
     * @return Modelo5073
     */
    public function setComprasCupet($comprasCupet = null)
    {
        $this->comprasCupet = $comprasCupet;
    
        return $this;
    }

    /**
     * Get comprasCupet.
     *
     * @return string|null
     */
    public function getComprasCupet()
    {
        return $this->comprasCupet;
    }

    /**
     * Set otrasEntradas.
     *
     * @param string|null $otrasEntradas
     *
     * @return Modelo5073
     */
    public function setOtrasEntradas($otrasEntradas = null)
    {
        $this->otrasEntradas = $otrasEntradas;
    
        return $this;
    }

    /**
     * Get otrasEntradas.
     *
     * @return string|null
     */
    public function getOtrasEntradas()
    {
        return $this->otrasEntradas;
    }

    /**
     * Set consumoDirecto.
     *
     * @param string|null $consumoDirecto
     *
     * @return Modelo5073
     */
    public function setConsumoDirecto($consumoDirecto = null)
    {
        $this->consumoDirecto = $consumoDirecto;
    
        return $this;
    }

    /**
     * Get consumoDirecto.
     *
     * @return string|null
     */
    public function getConsumoDirecto()
    {
        return $this->consumoDirecto;
    }

    /**
     * Set consumoIndirecto.
     *
     * @param string|null $consumoIndirecto
     *
     * @return Modelo5073
     */
    public function setConsumoIndirecto($consumoIndirecto = null)
    {
        $this->consumoIndirecto = $consumoIndirecto;
    
        return $this;
    }

    /**
     * Get consumoIndirecto.
     *
     * @return string|null
     */
    public function getConsumoIndirecto()
    {
        return $this->consumoIndirecto;
    }

    /**
     * Set otrasSalidas.
     *
     * @param string|null $otrasSalidas
     *
     * @return Modelo5073
     */
    public function setOtrasSalidas($otrasSalidas = null)
    {
        $this->otrasSalidas = $otrasSalidas;
    
        return $this;
    }

    /**
     * Get otrasSalidas.
     *
     * @return string|null
     */
    public function getOtrasSalidas()
    {
        return $this->otrasSalidas;
    }

    /**
     * Set inventarioFinalFisico.
     *
     * @param string|null $inventarioFinalFisico
     *
     * @return Modelo5073
     */
    public function setInventarioFinalFisico($inventarioFinalFisico = null)
    {
        $this->inventarioFinalFisico = $inventarioFinalFisico;
    
        return $this;
    }

    /**
     * Get inventarioFinalFisico.
     *
     * @return string|null
     */
    public function getInventarioFinalFisico()
    {
        return $this->inventarioFinalFisico;
    }

    /**
     * Set asignadoMes.
     *
     * @param string|null $asignadoMes
     *
     * @return Modelo5073
     */
    public function setAsignadoMes($asignadoMes = null)
    {
        $this->asignadoMes = $asignadoMes;
    
        return $this;
    }

    /**
     * Get asignadoMes.
     *
     * @return string|null
     */
    public function getAsignadoMes()
    {
        return $this->asignadoMes;
    }

    /**
     * Set recibidoEfectuaCargar.
     *
     * @param string|null $recibidoEfectuaCargar
     *
     * @return Modelo5073
     */
    public function setRecibidoEfectuaCargar($recibidoEfectuaCargar = null)
    {
        $this->recibidoEfectuaCargar = $recibidoEfectuaCargar;
    
        return $this;
    }

    /**
     * Get recibidoEfectuaCargar.
     *
     * @return string|null
     */
    public function getRecibidoEfectuaCargar()
    {
        return $this->recibidoEfectuaCargar;
    }

    /**
     * Set consumo.
     *
     * @param string|null $consumo
     *
     * @return Modelo5073
     */
    public function setConsumo($consumo = null)
    {
        $this->consumo = $consumo;
    
        return $this;
    }

    /**
     * Get consumo.
     *
     * @return string|null
     */
    public function getConsumo()
    {
        return $this->consumo;
    }

    /**
     * Set entregaConsumo.
     *
     * @param string|null $entregaConsumo
     *
     * @return Modelo5073
     */
    public function setEntregaConsumo($entregaConsumo = null)
    {
        $this->entregaConsumo = $entregaConsumo;
    
        return $this;
    }

    /**
     * Get entregaConsumo.
     *
     * @return string|null
     */
    public function getEntregaConsumo()
    {
        return $this->entregaConsumo;
    }

    /**
     * Set saldoFinalTotal.
     *
     * @param string|null $saldoFinalTotal
     *
     * @return Modelo5073
     */
    public function setSaldoFinalTotal($saldoFinalTotal = null)
    {
        $this->saldoFinalTotal = $saldoFinalTotal;
    
        return $this;
    }

    /**
     * Get saldoFinalTotal.
     *
     * @return string|null
     */
    public function getSaldoFinalTotal()
    {
        return $this->saldoFinalTotal;
    }

    /**
     * Set saldoFinalUtilizarProximoMes.
     *
     * @param string|null $saldoFinalUtilizarProximoMes
     *
     * @return Modelo5073
     */
    public function setSaldoFinalUtilizarProximoMes($saldoFinalUtilizarProximoMes = null)
    {
        $this->saldoFinalUtilizarProximoMes = $saldoFinalUtilizarProximoMes;
    
        return $this;
    }

    /**
     * Get saldoFinalUtilizarProximoMes.
     *
     * @return string|null
     */
    public function getSaldoFinalUtilizarProximoMes()
    {
        return $this->saldoFinalUtilizarProximoMes;
    }

    /**
     * Set saldoFinalDisponibleFincimex.
     *
     * @param string|null $saldoFinalDisponibleFincimex
     *
     * @return Modelo5073
     */
    public function setSaldoFinalDisponibleFincimex($saldoFinalDisponibleFincimex = null)
    {
        $this->saldoFinalDisponibleFincimex = $saldoFinalDisponibleFincimex;
    
        return $this;
    }

    /**
     * Get saldoFinalDisponibleFincimex.
     *
     * @return string|null
     */
    public function getSaldoFinalDisponibleFincimex()
    {
        return $this->saldoFinalDisponibleFincimex;
    }

    /**
     * Set acumuladoReal.
     *
     * @param string|null $acumuladoReal
     *
     * @return Modelo5073
     */
    public function setAcumuladoReal($acumuladoReal = null)
    {
        $this->acumuladoReal = $acumuladoReal;
    
        return $this;
    }

    /**
     * Get acumuladoReal.
     *
     * @return string|null
     */
    public function getAcumuladoReal()
    {
        return $this->acumuladoReal;
    }

    /**
     * Set acumuladoAnnoAnterior.
     *
     * @param string|null $acumuladoAnnoAnterior
     *
     * @return Modelo5073
     */
    public function setAcumuladoAnnoAnterior($acumuladoAnnoAnterior = null)
    {
        $this->acumuladoAnnoAnterior = $acumuladoAnnoAnterior;
    
        return $this;
    }

    /**
     * Get acumuladoAnnoAnterior.
     *
     * @return string|null
     */
    public function getAcumuladoAnnoAnterior()
    {
        return $this->acumuladoAnnoAnterior;
    }

    /**
     * Set producto.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Producto|null $producto
     *
     * @return Modelo5073
     */
    public function setProducto(\Geocuba\PortadoresBundle\Entity\Producto $producto = null)
    {
        $this->producto = $producto;
    
        return $this;
    }

    /**
     * Get producto.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Producto|null
     */
    public function getProducto()
    {
        return $this->producto;
    }

    /**
     * Set unidadid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $unidadid
     *
     * @return Modelo5073
     */
    public function setUnidadid(\Geocuba\PortadoresBundle\Entity\Unidad $unidadid = null)
    {
        $this->unidadid = $unidadid;
    
        return $this;
    }

    /**
     * Get unidadid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getUnidadid()
    {
        return $this->unidadid;
    }
}
