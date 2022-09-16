<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Servicio
 *
 * @ORM\Table(name="nomencladores.servicio", indexes={@ORM\Index(name="IDX_3EEB3B433C7CEFFE", columns={"nunidadid"}), @ORM\Index(name="IDX_3EEB3B43C035332", columns={"ntarifaid"}), @ORM\Index(name="IDX_3EEB3B438D656668", columns={"provinciaid"}), @ORM\Index(name="IDX_3EEB3B438D824A80", columns={"nactividadid"}), @ORM\Index(name="IDX_3EEB3B434A0E0EA8", columns={"turnos_trabajo"}), @ORM\Index(name="IDX_3EEB3B43FE98F5E0", columns={"municipio"}), @ORM\Index(name="IDX_3EEB3B43F5999009", columns={"cap_transf1"}), @ORM\Index(name="IDX_3EEB3B436C90C1B3", columns={"cap_transf2"}), @ORM\Index(name="IDX_3EEB3B431B97F125", columns={"cap_transf3"}), @ORM\Index(name="IDX_3EEB3B4385F36486", columns={"cap_transf4"}), @ORM\Index(name="IDX_3EEB3B43F2F45410", columns={"cap_transf5"}), @ORM\Index(name="IDX_3EEB3B43F0FEE5C7", columns={"capac_banco_transformadores"})})
 * @ORM\Entity
 */
class Servicio
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=150, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_servicio", type="string", length=255, nullable=true)
     */
    private $nombreServicio;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo_cliente", type="string", length=255, nullable=true)
     */
    private $codigoCliente;

    /**
     * @var string
     *
     * @ORM\Column(name="control", type="string", length=255, nullable=true)
     */
    private $control;

    /**
     * @var string
     *
     * @ORM\Column(name="ruta", type="string", length=255, nullable=true)
     */
    private $ruta;

    /**
     * @var string
     *
     * @ORM\Column(name="folio", type="string", length=255, nullable=true)
     */
    private $folio;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="factor_metrocontador", type="decimal", precision=19, scale=0, nullable=true)
     */
    private $factorMetrocontador;

    /**
     * @var float
     *
     * @ORM\Column(name="maxima_demanda_contratada", type="float", precision=10, scale=0, nullable=true)
     */
    private $maximaDemandaContratada;

    /**
     * @var float
     *
     * @ORM\Column(name="factor_combustible", type="float", precision=10, scale=0, nullable=true)
     */
    private $factorCombustible;

    /**
     * @var float
     *
     * @ORM\Column(name="indice_consumo", type="float", precision=10, scale=0, nullable=true)
     */
    private $indiceConsumo;

    /**
     * @var integer
     *
     * @ORM\Column(name="consumo_promedio_anno", type="bigint", nullable=true)
     */
    private $consumoPromedioAnno;

    /**
     * @var float
     *
     * @ORM\Column(name="consumo_promedio_plan", type="float", precision=10, scale=0, nullable=true)
     */
    private $consumoPromedioPlan;

    /**
     * @var float
     *
     * @ORM\Column(name="consumo_promedio_real", type="float", precision=10, scale=0, nullable=true)
     */
    private $consumoPromedioReal;

    /**
     * @var string
     *
     * @ORM\Column(name="servicio_electrico", type="string", length=255, nullable=true)
     */
    private $servicioElectrico;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var boolean
     *
     * @ORM\Column(name="metro_regresivo", type="boolean", nullable=true)
     */
    private $metroRegresivo;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", nullable=true)
     */
    private $numero;

    /**
     * @var boolean
     *
     * @ORM\Column(name="servicio_mayor", type="boolean", nullable=true)
     */
    private $servicioMayor;

    /**
     * @var boolean
     *
     * @ORM\Column(name="servicio_prepago", type="boolean", nullable=true)
     */
    private $servicioPrepago;

    /**
     * @var float
     *
     * @ORM\Column(name="cant_transf_banco", type="float", precision=10, scale=0, nullable=true)
     */
    private $cantTransfBanco;

    /**
     * @var float
     *
     * @ORM\Column(name="cap_banco_mayor", type="float", precision=10, scale=0, nullable=true)
     */
    private $capBancoMayor;

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
     * @var \Tarifa
     *
     * @ORM\ManyToOne(targetEntity="Tarifa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ntarifaid", referencedColumnName="id")
     * })
     */
    private $ntarifaid;

    /**
     * @var \Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="provinciaid", referencedColumnName="id")
     * })
     */
    private $provinciaid;

    /**
     * @var \Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nactividadid", referencedColumnName="id")
     * })
     */
    private $nactividadid;

    /**
     * @var \TurnoTrabajo
     *
     * @ORM\ManyToOne(targetEntity="TurnoTrabajo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="turnos_trabajo", referencedColumnName="id")
     * })
     */
    private $turnosTrabajo;

    /**
     * @var \Municipio
     *
     * @ORM\ManyToOne(targetEntity="Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipio", referencedColumnName="id")
     * })
     */
    private $municipio;

    /**
     * @var \BancoTransformadores
     *
     * @ORM\ManyToOne(targetEntity="BancoTransformadores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cap_transf1", referencedColumnName="id")
     * })
     */
    private $capTransf1;

    /**
     * @var \BancoTransformadores
     *
     * @ORM\ManyToOne(targetEntity="BancoTransformadores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cap_transf2", referencedColumnName="id")
     * })
     */
    private $capTransf2;

    /**
     * @var \BancoTransformadores
     *
     * @ORM\ManyToOne(targetEntity="BancoTransformadores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cap_transf3", referencedColumnName="id")
     * })
     */
    private $capTransf3;

    /**
     * @var \BancoTransformadores
     *
     * @ORM\ManyToOne(targetEntity="BancoTransformadores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cap_transf4", referencedColumnName="id")
     * })
     */
    private $capTransf4;

    /**
     * @var \BancoTransformadores
     *
     * @ORM\ManyToOne(targetEntity="BancoTransformadores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cap_transf5", referencedColumnName="id")
     * })
     */
    private $capTransf5;

    /**
     * @var \BancoTransformadores
     *
     * @ORM\ManyToOne(targetEntity="BancoTransformadores")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="capac_banco_transformadores", referencedColumnName="id")
     * })
     */
    private $capacBancoTransformadores;



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
     * Set nombreServicio
     *
     * @param string $nombreServicio
     *
     * @return Servicio
     */
    public function setNombreServicio($nombreServicio)
    {
        $this->nombreServicio = $nombreServicio;

        return $this;
    }

    /**
     * Get nombreServicio
     *
     * @return string
     */
    public function getNombreServicio()
    {
        return $this->nombreServicio;
    }

    /**
     * Set codigoCliente
     *
     * @param string $codigoCliente
     *
     * @return Servicio
     */
    public function setCodigoCliente($codigoCliente)
    {
        $this->codigoCliente = $codigoCliente;

        return $this;
    }

    /**
     * Get codigoCliente
     *
     * @return string
     */
    public function getCodigoCliente()
    {
        return $this->codigoCliente;
    }

    /**
     * Set control
     *
     * @param string $control
     *
     * @return Servicio
     */
    public function setControl($control)
    {
        $this->control = $control;

        return $this;
    }

    /**
     * Get control
     *
     * @return string
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * Set ruta
     *
     * @param string $ruta
     *
     * @return Servicio
     */
    public function setRuta($ruta)
    {
        $this->ruta = $ruta;

        return $this;
    }

    /**
     * Get ruta
     *
     * @return string
     */
    public function getRuta()
    {
        return $this->ruta;
    }

    /**
     * Set folio
     *
     * @param string $folio
     *
     * @return Servicio
     */
    public function setFolio($folio)
    {
        $this->folio = $folio;

        return $this;
    }

    /**
     * Get folio
     *
     * @return string
     */
    public function getFolio()
    {
        return $this->folio;
    }

    /**
     * Set direccion
     *
     * @param string $direccion
     *
     * @return Servicio
     */
    public function setDireccion($direccion)
    {
        $this->direccion = $direccion;

        return $this;
    }

    /**
     * Get direccion
     *
     * @return string
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * Set factorMetrocontador
     *
     * @param string $factorMetrocontador
     *
     * @return Servicio
     */
    public function setFactorMetrocontador($factorMetrocontador)
    {
        $this->factorMetrocontador = $factorMetrocontador;

        return $this;
    }

    /**
     * Get factorMetrocontador
     *
     * @return string
     */
    public function getFactorMetrocontador()
    {
        return $this->factorMetrocontador;
    }

    /**
     * Set maximaDemandaContratada
     *
     * @param float $maximaDemandaContratada
     *
     * @return Servicio
     */
    public function setMaximaDemandaContratada($maximaDemandaContratada)
    {
        $this->maximaDemandaContratada = $maximaDemandaContratada;

        return $this;
    }

    /**
     * Get maximaDemandaContratada
     *
     * @return float
     */
    public function getMaximaDemandaContratada()
    {
        return $this->maximaDemandaContratada;
    }

    /**
     * Set factorCombustible
     *
     * @param float $factorCombustible
     *
     * @return Servicio
     */
    public function setFactorCombustible($factorCombustible)
    {
        $this->factorCombustible = $factorCombustible;

        return $this;
    }

    /**
     * Get factorCombustible
     *
     * @return float
     */
    public function getFactorCombustible()
    {
        return $this->factorCombustible;
    }

    /**
     * Set indiceConsumo
     *
     * @param float $indiceConsumo
     *
     * @return Servicio
     */
    public function setIndiceConsumo($indiceConsumo)
    {
        $this->indiceConsumo = $indiceConsumo;

        return $this;
    }

    /**
     * Get indiceConsumo
     *
     * @return float
     */
    public function getIndiceConsumo()
    {
        return $this->indiceConsumo;
    }

    /**
     * Set consumoPromedioAnno
     *
     * @param integer $consumoPromedioAnno
     *
     * @return Servicio
     */
    public function setConsumoPromedioAnno($consumoPromedioAnno)
    {
        $this->consumoPromedioAnno = $consumoPromedioAnno;

        return $this;
    }

    /**
     * Get consumoPromedioAnno
     *
     * @return integer
     */
    public function getConsumoPromedioAnno()
    {
        return $this->consumoPromedioAnno;
    }

    /**
     * Set consumoPromedioPlan
     *
     * @param float $consumoPromedioPlan
     *
     * @return Servicio
     */
    public function setConsumoPromedioPlan($consumoPromedioPlan)
    {
        $this->consumoPromedioPlan = $consumoPromedioPlan;

        return $this;
    }

    /**
     * Get consumoPromedioPlan
     *
     * @return float
     */
    public function getConsumoPromedioPlan()
    {
        return $this->consumoPromedioPlan;
    }

    /**
     * Set consumoPromedioReal
     *
     * @param float $consumoPromedioReal
     *
     * @return Servicio
     */
    public function setConsumoPromedioReal($consumoPromedioReal)
    {
        $this->consumoPromedioReal = $consumoPromedioReal;

        return $this;
    }

    /**
     * Get consumoPromedioReal
     *
     * @return float
     */
    public function getConsumoPromedioReal()
    {
        return $this->consumoPromedioReal;
    }

    /**
     * Set servicioElectrico
     *
     * @param string $servicioElectrico
     *
     * @return Servicio
     */
    public function setServicioElectrico($servicioElectrico)
    {
        $this->servicioElectrico = $servicioElectrico;

        return $this;
    }

    /**
     * Get servicioElectrico
     *
     * @return string
     */
    public function getServicioElectrico()
    {
        return $this->servicioElectrico;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Servicio
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
     * Set metroRegresivo
     *
     * @param boolean $metroRegresivo
     *
     * @return Servicio
     */
    public function setMetroRegresivo($metroRegresivo)
    {
        $this->metroRegresivo = $metroRegresivo;

        return $this;
    }

    /**
     * Get metroRegresivo
     *
     * @return boolean
     */
    public function getMetroRegresivo()
    {
        return $this->metroRegresivo;
    }

    /**
     * Set numero
     *
     * @param string $numero
     *
     * @return Servicio
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set servicioMayor
     *
     * @param boolean $servicioMayor
     *
     * @return Servicio
     */
    public function setServicioMayor($servicioMayor)
    {
        $this->servicioMayor = $servicioMayor;

        return $this;
    }

    /**
     * Get servicioMayor
     *
     * @return boolean
     */
    public function getServicioMayor()
    {
        return $this->servicioMayor;
    }

    /**
     * Set servicioPrepago
     *
     * @param boolean $servicioPrepago
     *
     * @return Servicio
     */
    public function setServicioPrepago($servicioPrepago)
    {
        $this->servicioPrepago = $servicioPrepago;

        return $this;
    }

    /**
     * Get servicioPrepago
     *
     * @return boolean
     */
    public function getServicioPrepago()
    {
        return $this->servicioPrepago;
    }

    /**
     * Set cantTransfBanco
     *
     * @param float $cantTransfBanco
     *
     * @return Servicio
     */
    public function setCantTransfBanco($cantTransfBanco)
    {
        $this->cantTransfBanco = $cantTransfBanco;

        return $this;
    }

    /**
     * Get cantTransfBanco
     *
     * @return float
     */
    public function getCantTransfBanco()
    {
        return $this->cantTransfBanco;
    }

    /**
     * Set capBancoMayor
     *
     * @param float $capBancoMayor
     *
     * @return Servicio
     */
    public function setCapBancoMayor($capBancoMayor)
    {
        $this->capBancoMayor = $capBancoMayor;

        return $this;
    }

    /**
     * Get capBancoMayor
     *
     * @return float
     */
    public function getCapBancoMayor()
    {
        return $this->capBancoMayor;
    }

    /**
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return Servicio
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
     * Set ntarifaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarifa $ntarifaid
     *
     * @return Servicio
     */
    public function setNtarifaid(\Geocuba\PortadoresBundle\Entity\Tarifa $ntarifaid = null)
    {
        $this->ntarifaid = $ntarifaid;

        return $this;
    }

    /**
     * Get ntarifaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarifa
     */
    public function getNtarifaid()
    {
        return $this->ntarifaid;
    }

    /**
     * Set provinciaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Provincia $provinciaid
     *
     * @return Servicio
     */
    public function setProvinciaid(\Geocuba\PortadoresBundle\Entity\Provincia $provinciaid = null)
    {
        $this->provinciaid = $provinciaid;

        return $this;
    }

    /**
     * Get provinciaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Provincia
     */
    public function getProvinciaid()
    {
        return $this->provinciaid;
    }

    /**
     * Set nactividadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Actividad $nactividadid
     *
     * @return Servicio
     */
    public function setNactividadid(\Geocuba\PortadoresBundle\Entity\Actividad $nactividadid = null)
    {
        $this->nactividadid = $nactividadid;

        return $this;
    }

    /**
     * Get nactividadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Actividad
     */
    public function getNactividadid()
    {
        return $this->nactividadid;
    }

    /**
     * Set turnosTrabajo
     *
     * @param \Geocuba\PortadoresBundle\Entity\TurnoTrabajo $turnosTrabajo
     *
     * @return Servicio
     */
    public function setTurnosTrabajo(\Geocuba\PortadoresBundle\Entity\TurnoTrabajo $turnosTrabajo = null)
    {
        $this->turnosTrabajo = $turnosTrabajo;

        return $this;
    }

    /**
     * Get turnosTrabajo
     *
     * @return \Geocuba\PortadoresBundle\Entity\TurnoTrabajo
     */
    public function getTurnosTrabajo()
    {
        return $this->turnosTrabajo;
    }

    /**
     * Set municipio
     *
     * @param \Geocuba\PortadoresBundle\Entity\Municipio $municipio
     *
     * @return Servicio
     */
    public function setMunicipio(\Geocuba\PortadoresBundle\Entity\Municipio $municipio = null)
    {
        $this->municipio = $municipio;

        return $this;
    }

    /**
     * Get municipio
     *
     * @return \Geocuba\PortadoresBundle\Entity\Municipio
     */
    public function getMunicipio()
    {
        return $this->municipio;
    }

    /**
     * Set capTransf1
     *
     * @param \Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf1
     *
     * @return Servicio
     */
    public function setCapTransf1(\Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf1 = null)
    {
        $this->capTransf1 = $capTransf1;

        return $this;
    }

    /**
     * Get capTransf1
     *
     * @return \Geocuba\PortadoresBundle\Entity\BancoTransformadores
     */
    public function getCapTransf1()
    {
        return $this->capTransf1;
    }

    /**
     * Set capTransf2
     *
     * @param \Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf2
     *
     * @return Servicio
     */
    public function setCapTransf2(\Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf2 = null)
    {
        $this->capTransf2 = $capTransf2;

        return $this;
    }

    /**
     * Get capTransf2
     *
     * @return \Geocuba\PortadoresBundle\Entity\BancoTransformadores
     */
    public function getCapTransf2()
    {
        return $this->capTransf2;
    }

    /**
     * Set capTransf3
     *
     * @param \Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf3
     *
     * @return Servicio
     */
    public function setCapTransf3(\Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf3 = null)
    {
        $this->capTransf3 = $capTransf3;

        return $this;
    }

    /**
     * Get capTransf3
     *
     * @return \Geocuba\PortadoresBundle\Entity\BancoTransformadores
     */
    public function getCapTransf3()
    {
        return $this->capTransf3;
    }

    /**
     * Set capTransf4
     *
     * @param \Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf4
     *
     * @return Servicio
     */
    public function setCapTransf4(\Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf4 = null)
    {
        $this->capTransf4 = $capTransf4;

        return $this;
    }

    /**
     * Get capTransf4
     *
     * @return \Geocuba\PortadoresBundle\Entity\BancoTransformadores
     */
    public function getCapTransf4()
    {
        return $this->capTransf4;
    }

    /**
     * Set capTransf5
     *
     * @param \Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf5
     *
     * @return Servicio
     */
    public function setCapTransf5(\Geocuba\PortadoresBundle\Entity\BancoTransformadores $capTransf5 = null)
    {
        $this->capTransf5 = $capTransf5;

        return $this;
    }

    /**
     * Get capTransf5
     *
     * @return \Geocuba\PortadoresBundle\Entity\BancoTransformadores
     */
    public function getCapTransf5()
    {
        return $this->capTransf5;
    }

    /**
     * Set capacBancoTransformadores
     *
     * @param \Geocuba\PortadoresBundle\Entity\BancoTransformadores $capacBancoTransformadores
     *
     * @return Servicio
     */
    public function setCapacBancoTransformadores(\Geocuba\PortadoresBundle\Entity\BancoTransformadores $capacBancoTransformadores = null)
    {
        $this->capacBancoTransformadores = $capacBancoTransformadores;

        return $this;
    }

    /**
     * Get capacBancoTransformadores
     *
     * @return \Geocuba\PortadoresBundle\Entity\BancoTransformadores
     */
    public function getCapacBancoTransformadores()
    {
        return $this->capacBancoTransformadores;
    }
}
