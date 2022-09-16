<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DistribucionDesgloseEdit
 *
 * @ORM\Table(name="datos.distribucion_desglose_edit", indexes={@ORM\Index(name="IDX_F0659495A54A2CD2", columns={"dist_combustible_id"}), @ORM\Index(name="IDX_F0659495F8E73DD", columns={"personaid"}), @ORM\Index(name="IDX_F065949587E5D022", columns={"tarjetaid"}), @ORM\Index(name="IDX_F065949560C04838", columns={"vehiculoid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\DistribucionDesgloseEditRepository")
 */
class DistribucionDesgloseEdit
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
     * @ORM\Column(name="matricula", type="string", length=255, nullable=true)
     */
    private $matricula;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_persona", type="string", length=255, nullable=true)
     */
    private $nombrePersona;

    /**
     * @var string
     *
     * @ORM\Column(name="comb_asig", type="string", length=255, nullable=true)
     */
    private $combAsig;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_asig", type="string", length=255, nullable=true)
     */
    private $montoAsig;

    /**
     * @var string
     *
     * @ORM\Column(name="kms", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $kms;

    /**
     * @var string
     *
     * @ORM\Column(name="carga_lts", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $cargaLts;

    /**
     * @var string
     *
     * @ORM\Column(name="carga_monto", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $cargaMonto;

    /**
     * @var integer
     *
     * @ORM\Column(name="nmes", type="integer", nullable=true)
     */
    private $nmes;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_tarjeta", type="string", length=255, nullable=true)
     */
    private $nroTarjeta;

    /**
     * @var string
     *
     * @ORM\Column(name="vehiculo_marca", type="string", length=255, nullable=true)
     */
    private $vehiculoMarca;

    /**
     * @var string
     *
     * @ORM\Column(name="vehiculo_modelo", type="string", length=255, nullable=true)
     */
    private $vehiculoModelo;

    /**
     * @var string
     *
     * @ORM\Column(name="monto", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $monto;

    /**
     * @var string
     *
     * @ORM\Column(name="nota", type="string", length=255, nullable=true)
     */
    private $nota;

    /**
     * @var string
     *
     * @ORM\Column(name="vehiculo_denominacion", type="string", length=255, nullable=true)
     */
    private $vehiculoDenominacion;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_combustible", type="decimal", precision=5, scale=2, nullable=true)
     */
    private $precioCombustible;

    /**
     * @var string
     *
     * @ORM\Column(name="cantidad", type="decimal", precision=19, scale=5, nullable=true)
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="dist_comb_desg", type="string", length=255, nullable=true)
     */
    private $distCombDesg;

    /**
     * @var float
     *
     * @ORM\Column(name="incremento", type="float", precision=10, scale=0, nullable=true)
     */
    private $incremento;

    /**
     * @var float
     *
     * @ORM\Column(name="reduccion", type="float", precision=10, scale=0, nullable=true)
     */
    private $reduccion;

    /**
     * @var \DistribucionCombustible
     *
     * @ORM\ManyToOne(targetEntity="DistribucionCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dist_combustible_id", referencedColumnName="id")
     * })
     */
    private $distCombustible;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="personaid", referencedColumnName="id")
     * })
     */
    private $personaid;

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
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehiculoid", referencedColumnName="id")
     * })
     */
    private $vehiculoid;



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
     * Set matricula
     *
     * @param string $matricula
     *
     * @return DistribucionDesgloseEdit
     */
    public function setMatricula($matricula)
    {
        $this->matricula = $matricula;

        return $this;
    }

    /**
     * Get matricula
     *
     * @return string
     */
    public function getMatricula()
    {
        return $this->matricula;
    }

    /**
     * Set nombrePersona
     *
     * @param string $nombrePersona
     *
     * @return DistribucionDesgloseEdit
     */
    public function setNombrePersona($nombrePersona)
    {
        $this->nombrePersona = $nombrePersona;

        return $this;
    }

    /**
     * Get nombrePersona
     *
     * @return string
     */
    public function getNombrePersona()
    {
        return $this->nombrePersona;
    }

    /**
     * Set combAsig
     *
     * @param string $combAsig
     *
     * @return DistribucionDesgloseEdit
     */
    public function setCombAsig($combAsig)
    {
        $this->combAsig = $combAsig;

        return $this;
    }

    /**
     * Get combAsig
     *
     * @return string
     */
    public function getCombAsig()
    {
        return $this->combAsig;
    }

    /**
     * Set montoAsig
     *
     * @param string $montoAsig
     *
     * @return DistribucionDesgloseEdit
     */
    public function setMontoAsig($montoAsig)
    {
        $this->montoAsig = $montoAsig;

        return $this;
    }

    /**
     * Get montoAsig
     *
     * @return string
     */
    public function getMontoAsig()
    {
        return $this->montoAsig;
    }

    /**
     * Set kms
     *
     * @param string $kms
     *
     * @return DistribucionDesgloseEdit
     */
    public function setKms($kms)
    {
        $this->kms = $kms;

        return $this;
    }

    /**
     * Get kms
     *
     * @return string
     */
    public function getKms()
    {
        return $this->kms;
    }

    /**
     * Set cargaLts
     *
     * @param string $cargaLts
     *
     * @return DistribucionDesgloseEdit
     */
    public function setCargaLts($cargaLts)
    {
        $this->cargaLts = $cargaLts;

        return $this;
    }

    /**
     * Get cargaLts
     *
     * @return string
     */
    public function getCargaLts()
    {
        return $this->cargaLts;
    }

    /**
     * Set cargaMonto
     *
     * @param string $cargaMonto
     *
     * @return DistribucionDesgloseEdit
     */
    public function setCargaMonto($cargaMonto)
    {
        $this->cargaMonto = $cargaMonto;

        return $this;
    }

    /**
     * Get cargaMonto
     *
     * @return string
     */
    public function getCargaMonto()
    {
        return $this->cargaMonto;
    }

    /**
     * Set nmes
     *
     * @param integer $nmes
     *
     * @return DistribucionDesgloseEdit
     */
    public function setNmes($nmes)
    {
        $this->nmes = $nmes;

        return $this;
    }

    /**
     * Get nmes
     *
     * @return integer
     */
    public function getNmes()
    {
        return $this->nmes;
    }

    /**
     * Set nroTarjeta
     *
     * @param string $nroTarjeta
     *
     * @return DistribucionDesgloseEdit
     */
    public function setNroTarjeta($nroTarjeta)
    {
        $this->nroTarjeta = $nroTarjeta;

        return $this;
    }

    /**
     * Get nroTarjeta
     *
     * @return string
     */
    public function getNroTarjeta()
    {
        return $this->nroTarjeta;
    }

    /**
     * Set vehiculoMarca
     *
     * @param string $vehiculoMarca
     *
     * @return DistribucionDesgloseEdit
     */
    public function setVehiculoMarca($vehiculoMarca)
    {
        $this->vehiculoMarca = $vehiculoMarca;

        return $this;
    }

    /**
     * Get vehiculoMarca
     *
     * @return string
     */
    public function getVehiculoMarca()
    {
        return $this->vehiculoMarca;
    }

    /**
     * Set vehiculoModelo
     *
     * @param string $vehiculoModelo
     *
     * @return DistribucionDesgloseEdit
     */
    public function setVehiculoModelo($vehiculoModelo)
    {
        $this->vehiculoModelo = $vehiculoModelo;

        return $this;
    }

    /**
     * Get vehiculoModelo
     *
     * @return string
     */
    public function getVehiculoModelo()
    {
        return $this->vehiculoModelo;
    }

    /**
     * Set monto
     *
     * @param string $monto
     *
     * @return DistribucionDesgloseEdit
     */
    public function setMonto($monto)
    {
        $this->monto = $monto;

        return $this;
    }

    /**
     * Get monto
     *
     * @return string
     */
    public function getMonto()
    {
        return $this->monto;
    }

    /**
     * Set nota
     *
     * @param string $nota
     *
     * @return DistribucionDesgloseEdit
     */
    public function setNota($nota)
    {
        $this->nota = $nota;

        return $this;
    }

    /**
     * Get nota
     *
     * @return string
     */
    public function getNota()
    {
        return $this->nota;
    }

    /**
     * Set vehiculoDenominacion
     *
     * @param string $vehiculoDenominacion
     *
     * @return DistribucionDesgloseEdit
     */
    public function setVehiculoDenominacion($vehiculoDenominacion)
    {
        $this->vehiculoDenominacion = $vehiculoDenominacion;

        return $this;
    }

    /**
     * Get vehiculoDenominacion
     *
     * @return string
     */
    public function getVehiculoDenominacion()
    {
        return $this->vehiculoDenominacion;
    }

    /**
     * Set precioCombustible
     *
     * @param string $precioCombustible
     *
     * @return DistribucionDesgloseEdit
     */
    public function setPrecioCombustible($precioCombustible)
    {
        $this->precioCombustible = $precioCombustible;

        return $this;
    }

    /**
     * Get precioCombustible
     *
     * @return string
     */
    public function getPrecioCombustible()
    {
        return $this->precioCombustible;
    }

    /**
     * Set cantidad
     *
     * @param string $cantidad
     *
     * @return DistribucionDesgloseEdit
     */
    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad
     *
     * @return string
     */
    public function getCantidad()
    {
        return $this->cantidad;
    }

    /**
     * Set distCombDesg
     *
     * @param string $distCombDesg
     *
     * @return DistribucionDesgloseEdit
     */
    public function setDistCombDesg($distCombDesg)
    {
        $this->distCombDesg = $distCombDesg;

        return $this;
    }

    /**
     * Get distCombDesg
     *
     * @return string
     */
    public function getDistCombDesg()
    {
        return $this->distCombDesg;
    }

    /**
     * Set incremento
     *
     * @param float $incremento
     *
     * @return DistribucionDesgloseEdit
     */
    public function setIncremento($incremento)
    {
        $this->incremento = $incremento;

        return $this;
    }

    /**
     * Get incremento
     *
     * @return float
     */
    public function getIncremento()
    {
        return $this->incremento;
    }

    /**
     * Set reduccion
     *
     * @param float $reduccion
     *
     * @return DistribucionDesgloseEdit
     */
    public function setReduccion($reduccion)
    {
        $this->reduccion = $reduccion;

        return $this;
    }

    /**
     * Get reduccion
     *
     * @return float
     */
    public function getReduccion()
    {
        return $this->reduccion;
    }

    /**
     * Set distCombustible
     *
     * @param \Geocuba\PortadoresBundle\Entity\DistribucionCombustible $distCombustible
     *
     * @return DistribucionDesgloseEdit
     */
    public function setDistCombustible(\Geocuba\PortadoresBundle\Entity\DistribucionCombustible $distCombustible = null)
    {
        $this->distCombustible = $distCombustible;

        return $this;
    }

    /**
     * Get distCombustible
     *
     * @return \Geocuba\PortadoresBundle\Entity\DistribucionCombustible
     */
    public function getDistCombustible()
    {
        return $this->distCombustible;
    }

    /**
     * Set personaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $personaid
     *
     * @return DistribucionDesgloseEdit
     */
    public function setPersonaid(\Geocuba\PortadoresBundle\Entity\Persona $personaid = null)
    {
        $this->personaid = $personaid;

        return $this;
    }

    /**
     * Get personaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getPersonaid()
    {
        return $this->personaid;
    }

    /**
     * Set tarjetaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $tarjetaid
     *
     * @return DistribucionDesgloseEdit
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
     * Set vehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculoid
     *
     * @return DistribucionDesgloseEdit
     */
    public function setVehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculoid = null)
    {
        $this->vehiculoid = $vehiculoid;

        return $this;
    }

    /**
     * Get vehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getVehiculoid()
    {
        return $this->vehiculoid;
    }
}
