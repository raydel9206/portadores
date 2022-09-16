<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InfoPrimaria5073
 *
 * @ORM\Table(name="datos.info_primaria_5073", indexes={@ORM\Index(name="IDX_97ACD2C7D8720997", columns={"tarjeta_id"}), @ORM\Index(name="IDX_97ACD2C7BEF6566F", columns={"portador_id"}), @ORM\Index(name="IDX_97ACD2C79D01464C", columns={"unidad_id"})})
 * @ORM\Entity
 */
class InfoPrimaria5073
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
     * @ORM\Column(name="vehiculos", type="string", length=500, nullable=true)
     */
    private $vehiculos;

    /**
     * @var float
     *
     * @ORM\Column(name="comb_inicio_mes", type="float", precision=10, scale=0, nullable=true)
     */
    private $combInicioMes;

    /**
     * @var float
     *
     * @ORM\Column(name="compra_mes", type="float", precision=10, scale=0, nullable=true)
     */
    private $compraMes;

    /**
     * @var float
     *
     * @ORM\Column(name="consumo", type="float", precision=10, scale=0, nullable=true)
     */
    private $consumo;

    /**
     * @var float
     *
     * @ORM\Column(name="comb_fin_mes", type="float", precision=10, scale=0, nullable=true)
     */
    private $combFinMes;

    /**
     * @var float
     *
     * @ORM\Column(name="comprobacion", type="float", precision=10, scale=0, nullable=true)
     */
    private $comprobacion;

    /**
     * @var float
     *
     * @ORM\Column(name="carga_prox_mes", type="float", precision=10, scale=0, nullable=true)
     */
    private $cargaProxMes;

    /**
     * @var float
     *
     * @ORM\Column(name="com_asignado", type="float", precision=10, scale=0, nullable=true)
     */
    private $comAsignado;

    /**
     * @var float
     *
     * @ORM\Column(name="com_disp_fincimex", type="float", precision=10, scale=0, nullable=true)
     */
    private $comDispFincimex;

    /**
     * @var float
     *
     * @ORM\Column(name="comb_disp_cuenta1", type="float", precision=10, scale=0, nullable=true)
     */
    private $combDispCuenta1;

    /**
     * @var string
     *
     * @ORM\Column(name="no_cliente", type="string", nullable=true)
     */
    private $noCliente;

    /**
     * @var float
     *
     * @ORM\Column(name="comb_entr_constructor", type="float", precision=10, scale=0, nullable=true)
     */
    private $combEntrConstructor;

    /**
     * @var float
     *
     * @ORM\Column(name="mes", type="float", precision=10, scale=0, nullable=true)
     */
    private $mes;

    /**
     * @var float
     *
     * @ORM\Column(name="anno", type="float", precision=10, scale=0, nullable=true)
     */
    private $anno;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tarjeta_id", referencedColumnName="id")
     * })
     */
    private $tarjeta;

    /**
     * @var \Portador
     *
     * @ORM\ManyToOne(targetEntity="Portador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portador_id", referencedColumnName="id")
     * })
     */
    private $portador;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    private $unidad;



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
     * Set vehiculos
     *
     * @param string $vehiculos
     *
     * @return InfoPrimaria5073
     */
    public function setVehiculos($vehiculos)
    {
        $this->vehiculos = $vehiculos;

        return $this;
    }

    /**
     * Get vehiculos
     *
     * @return string
     */
    public function getVehiculos()
    {
        return $this->vehiculos;
    }

    /**
     * Set combInicioMes
     *
     * @param float $combInicioMes
     *
     * @return InfoPrimaria5073
     */
    public function setCombInicioMes($combInicioMes)
    {
        $this->combInicioMes = $combInicioMes;

        return $this;
    }

    /**
     * Get combInicioMes
     *
     * @return float
     */
    public function getCombInicioMes()
    {
        return $this->combInicioMes;
    }

    /**
     * Set compraMes
     *
     * @param float $compraMes
     *
     * @return InfoPrimaria5073
     */
    public function setCompraMes($compraMes)
    {
        $this->compraMes = $compraMes;

        return $this;
    }

    /**
     * Get compraMes
     *
     * @return float
     */
    public function getCompraMes()
    {
        return $this->compraMes;
    }

    /**
     * Set consumo
     *
     * @param float $consumo
     *
     * @return InfoPrimaria5073
     */
    public function setConsumo($consumo)
    {
        $this->consumo = $consumo;

        return $this;
    }

    /**
     * Get consumo
     *
     * @return float
     */
    public function getConsumo()
    {
        return $this->consumo;
    }

    /**
     * Set combFinMes
     *
     * @param float $combFinMes
     *
     * @return InfoPrimaria5073
     */
    public function setCombFinMes($combFinMes)
    {
        $this->combFinMes = $combFinMes;

        return $this;
    }

    /**
     * Get combFinMes
     *
     * @return float
     */
    public function getCombFinMes()
    {
        return $this->combFinMes;
    }

    /**
     * Set comprobacion
     *
     * @param float $comprobacion
     *
     * @return InfoPrimaria5073
     */
    public function setComprobacion($comprobacion)
    {
        $this->comprobacion = $comprobacion;

        return $this;
    }

    /**
     * Get comprobacion
     *
     * @return float
     */
    public function getComprobacion()
    {
        return $this->comprobacion;
    }

    /**
     * Set cargaProxMes
     *
     * @param float $cargaProxMes
     *
     * @return InfoPrimaria5073
     */
    public function setCargaProxMes($cargaProxMes)
    {
        $this->cargaProxMes = $cargaProxMes;

        return $this;
    }

    /**
     * Get cargaProxMes
     *
     * @return float
     */
    public function getCargaProxMes()
    {
        return $this->cargaProxMes;
    }

    /**
     * Set comAsignado
     *
     * @param float $comAsignado
     *
     * @return InfoPrimaria5073
     */
    public function setComAsignado($comAsignado)
    {
        $this->comAsignado = $comAsignado;

        return $this;
    }

    /**
     * Get comAsignado
     *
     * @return float
     */
    public function getComAsignado()
    {
        return $this->comAsignado;
    }

    /**
     * Set comDispFincimex
     *
     * @param float $comDispFincimex
     *
     * @return InfoPrimaria5073
     */
    public function setComDispFincimex($comDispFincimex)
    {
        $this->comDispFincimex = $comDispFincimex;

        return $this;
    }

    /**
     * Get comDispFincimex
     *
     * @return float
     */
    public function getComDispFincimex()
    {
        return $this->comDispFincimex;
    }

    /**
     * Set combDispCuenta1
     *
     * @param float $combDispCuenta1
     *
     * @return InfoPrimaria5073
     */
    public function setCombDispCuenta1($combDispCuenta1)
    {
        $this->combDispCuenta1 = $combDispCuenta1;

        return $this;
    }

    /**
     * Get combDispCuenta1
     *
     * @return float
     */
    public function getCombDispCuenta1()
    {
        return $this->combDispCuenta1;
    }

    /**
     * Set noCliente
     *
     * @param string $noCliente
     *
     * @return InfoPrimaria5073
     */
    public function setNoCliente($noCliente)
    {
        $this->noCliente = $noCliente;

        return $this;
    }

    /**
     * Get noCliente
     *
     * @return string
     */
    public function getNoCliente()
    {
        return $this->noCliente;
    }

    /**
     * Set combEntrConstructor
     *
     * @param float $combEntrConstructor
     *
     * @return InfoPrimaria5073
     */
    public function setCombEntrConstructor($combEntrConstructor)
    {
        $this->combEntrConstructor = $combEntrConstructor;

        return $this;
    }

    /**
     * Get combEntrConstructor
     *
     * @return float
     */
    public function getCombEntrConstructor()
    {
        return $this->combEntrConstructor;
    }

    /**
     * Set mes
     *
     * @param float $mes
     *
     * @return InfoPrimaria5073
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
     * Set anno
     *
     * @param float $anno
     *
     * @return InfoPrimaria5073
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return float
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set tarjeta
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $tarjeta
     *
     * @return InfoPrimaria5073
     */
    public function setTarjeta(\Geocuba\PortadoresBundle\Entity\Tarjeta $tarjeta = null)
    {
        $this->tarjeta = $tarjeta;

        return $this;
    }

    /**
     * Get tarjeta
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getTarjeta()
    {
        return $this->tarjeta;
    }

    /**
     * Set portador
     *
     * @param \Geocuba\PortadoresBundle\Entity\Portador $portador
     *
     * @return InfoPrimaria5073
     */
    public function setPortador(\Geocuba\PortadoresBundle\Entity\Portador $portador = null)
    {
        $this->portador = $portador;

        return $this;
    }

    /**
     * Get portador
     *
     * @return \Geocuba\PortadoresBundle\Entity\Portador
     */
    public function getPortador()
    {
        return $this->portador;
    }

    /**
     * Set unidad
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $unidad
     *
     * @return InfoPrimaria5073
     */
    public function setUnidad(\Geocuba\PortadoresBundle\Entity\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
}
