<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnexoUnico
 *
 * @ORM\Table(name="datos.anexo_unico")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\AnexoUnicoRepository")
 */
class AnexoUnico
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
     * @ORM\Column(name="matricula", type="string", length=50, nullable=false)
     */
    private $matricula;

    /**
     * @var string
     *
     * @ORM\Column(name="norma_plan", type="decimal", precision=19, scale=2, nullable=false)
     */
    private $normaPlan;

    /**
     * @var float
     *
     * @ORM\Column(name="kilometraje_mes_anterior", type="float", precision=10, scale=0, nullable=false)
     */
    private $kilometrajeMesAnterior;

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_estimado_tanque", type="float", precision=10, scale=0, nullable=false)
     */
    private $combustibleEstimadoTanque;

    /**
     * @var float
     *
     * @ORM\Column(name="kilometraje_proximo_mantenimiento", type="float", precision=10, scale=0, nullable=false)
     */
    private $kilometrajeProximoMantenimiento;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var float
     *
     * @ORM\Column(name="kilometraje_cierre_mes", type="float", precision=10, scale=0, nullable=false)
     */
    private $kilometrajeCierreMes;

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_estimado_tanque_cierre", type="float", precision=10, scale=0, nullable=false)
     */
    private $combustibleEstimadoTanqueCierre;

    /**
     * @var float
     *
     * @ORM\Column(name="kilometros_total_recorrido", type="float", precision=10, scale=0, nullable=false)
     */
    private $kilometrosTotalRecorrido;

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_total_consumido", type="float", precision=10, scale=0, nullable=false)
     */
    private $combustibleTotalConsumido;

    /**
     * @var float
     *
     * @ORM\Column(name="norma_real", type="float", precision=10, scale=0, nullable=false)
     */
    private $normaReal;

    /**
     * @var float
     *
     * @ORM\Column(name="porciento_norma_real_plan", type="float", precision=10, scale=0, nullable=false)
     */
    private $porcientoNormaRealPlan;

    /**
     * @var integer
     *
     * @ORM\Column(name="kilometraje_mantenimiento", type="integer", nullable=true)
     */
    private $kilometrajeMantenimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="variacion_mantenimiento", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $variacionMantenimiento;

    /**
     * @var string
     *
     * @ORM\Column(name="observaciones", type="text", nullable=true)
     */
    private $observaciones;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=false)
     */
    private $anno;

    /**
     * @var integer
     *
     * @ORM\Column(name="mes", type="integer", nullable=false)
     */
    private $mes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_total_abastecido", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleTotalAbastecido;

    /**
     * @var float
     *
     * @ORM\Column(name="real_plan", type="float", precision=10, scale=0, nullable=true)
     */
    private $realPlan;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nvehiculoid", referencedColumnName="id")
     * })
     */
    private $nvehiculoid;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="npersonaid", referencedColumnName="id")
     * })
     */
    private $npersonaid;

    /**
     * @var \TipoMantenimiento
     *
     * @ORM\ManyToOne(targetEntity="TipoMantenimiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_mantenimientoid", referencedColumnName="id")
     * })
     */
    private $tipoMantenimientoid;


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
     * @return AnexoUnico
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
     * Set normaPlan
     *
     * @param string $normaPlan
     *
     * @return AnexoUnico
     */
    public function setNormaPlan($normaPlan)
    {
        $this->normaPlan = $normaPlan;

        return $this;
    }

    /**
     * Get normaPlan
     *
     * @return string
     */
    public function getNormaPlan()
    {
        return $this->normaPlan;
    }

    /**
     * Set kilometrajeMesAnterior
     *
     * @param float $kilometrajeMesAnterior
     *
     * @return AnexoUnico
     */
    public function setKilometrajeMesAnterior($kilometrajeMesAnterior)
    {
        $this->kilometrajeMesAnterior = $kilometrajeMesAnterior;

        return $this;
    }

    /**
     * Get kilometrajeMesAnterior
     *
     * @return float
     */
    public function getKilometrajeMesAnterior()
    {
        return $this->kilometrajeMesAnterior;
    }

    /**
     * Set combustibleEstimadoTanque
     *
     * @param float $combustibleEstimadoTanque
     *
     * @return AnexoUnico
     */
    public function setCombustibleEstimadoTanque($combustibleEstimadoTanque)
    {
        $this->combustibleEstimadoTanque = $combustibleEstimadoTanque;

        return $this;
    }

    /**
     * Get combustibleEstimadoTanque
     *
     * @return float
     */
    public function getCombustibleEstimadoTanque()
    {
        return $this->combustibleEstimadoTanque;
    }

    /**
     * Set kilometrajeProximoMantenimiento
     *
     * @param float $kilometrajeProximoMantenimiento
     *
     * @return AnexoUnico
     */
    public function setKilometrajeProximoMantenimiento($kilometrajeProximoMantenimiento)
    {
        $this->kilometrajeProximoMantenimiento = $kilometrajeProximoMantenimiento;

        return $this;
    }

    /**
     * Get kilometrajeProximoMantenimiento
     *
     * @return float
     */
    public function getKilometrajeProximoMantenimiento()
    {
        return $this->kilometrajeProximoMantenimiento;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return AnexoUnico
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
     * Set kilometrajeCierreMes
     *
     * @param float $kilometrajeCierreMes
     *
     * @return AnexoUnico
     */
    public function setKilometrajeCierreMes($kilometrajeCierreMes)
    {
        $this->kilometrajeCierreMes = $kilometrajeCierreMes;

        return $this;
    }

    /**
     * Get kilometrajeCierreMes
     *
     * @return float
     */
    public function getKilometrajeCierreMes()
    {
        return $this->kilometrajeCierreMes;
    }

    /**
     * Set combustibleEstimadoTanqueCierre
     *
     * @param float $combustibleEstimadoTanqueCierre
     *
     * @return AnexoUnico
     */
    public function setCombustibleEstimadoTanqueCierre($combustibleEstimadoTanqueCierre)
    {
        $this->combustibleEstimadoTanqueCierre = $combustibleEstimadoTanqueCierre;

        return $this;
    }

    /**
     * Get combustibleEstimadoTanqueCierre
     *
     * @return float
     */
    public function getCombustibleEstimadoTanqueCierre()
    {
        return $this->combustibleEstimadoTanqueCierre;
    }

    /**
     * Set kilometrosTotalRecorrido
     *
     * @param float $kilometrosTotalRecorrido
     *
     * @return AnexoUnico
     */
    public function setKilometrosTotalRecorrido($kilometrosTotalRecorrido)
    {
        $this->kilometrosTotalRecorrido = $kilometrosTotalRecorrido;

        return $this;
    }

    /**
     * Get kilometrosTotalRecorrido
     *
     * @return float
     */
    public function getKilometrosTotalRecorrido()
    {
        return $this->kilometrosTotalRecorrido;
    }

    /**
     * Set combustibleTotalConsumido
     *
     * @param float $combustibleTotalConsumido
     *
     * @return AnexoUnico
     */
    public function setCombustibleTotalConsumido($combustibleTotalConsumido)
    {
        $this->combustibleTotalConsumido = $combustibleTotalConsumido;

        return $this;
    }

    /**
     * Get combustibleTotalConsumido
     *
     * @return float
     */
    public function getCombustibleTotalConsumido()
    {
        return $this->combustibleTotalConsumido;
    }

    /**
     * Set normaReal
     *
     * @param float $normaReal
     *
     * @return AnexoUnico
     */
    public function setNormaReal($normaReal)
    {
        $this->normaReal = $normaReal;

        return $this;
    }

    /**
     * Get normaReal
     *
     * @return float
     */
    public function getNormaReal()
    {
        return $this->normaReal;
    }

    /**
     * Set porcientoNormaRealPlan
     *
     * @param float $porcientoNormaRealPlan
     *
     * @return AnexoUnico
     */
    public function setPorcientoNormaRealPlan($porcientoNormaRealPlan)
    {
        $this->porcientoNormaRealPlan = $porcientoNormaRealPlan;

        return $this;
    }

    /**
     * Get porcientoNormaRealPlan
     *
     * @return float
     */
    public function getPorcientoNormaRealPlan()
    {
        return $this->porcientoNormaRealPlan;
    }

    /**
     * Set kilometrajeMantenimiento
     *
     * @param integer $kilometrajeMantenimiento
     *
     * @return AnexoUnico
     */
    public function setKilometrajeMantenimiento($kilometrajeMantenimiento)
    {
        $this->kilometrajeMantenimiento = $kilometrajeMantenimiento;

        return $this;
    }

    /**
     * Get kilometrajeMantenimiento
     *
     * @return integer
     */
    public function getKilometrajeMantenimiento()
    {
        return $this->kilometrajeMantenimiento;
    }

    /**
     * Set variacionMantenimiento
     *
     * @param string $variacionMantenimiento
     *
     * @return AnexoUnico
     */
    public function setVariacionMantenimiento($variacionMantenimiento)
    {
        $this->variacionMantenimiento = $variacionMantenimiento;

        return $this;
    }

    /**
     * Get variacionMantenimiento
     *
     * @return string
     */
    public function getVariacionMantenimiento()
    {
        return $this->variacionMantenimiento;
    }

    /**
     * Set observaciones
     *
     * @param string $observaciones
     *
     * @return AnexoUnico
     */
    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;

        return $this;
    }

    /**
     * Get observaciones
     *
     * @return string
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Set anno
     *
     * @param integer $anno
     *
     * @return AnexoUnico
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
     * Set mes
     *
     * @param integer $mes
     *
     * @return AnexoUnico
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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return AnexoUnico
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
     * Set combustibleTotalAbastecido
     *
     * @param float $combustibleTotalAbastecido
     *
     * @return AnexoUnico
     */
    public function setCombustibleTotalAbastecido($combustibleTotalAbastecido)
    {
        $this->combustibleTotalAbastecido = $combustibleTotalAbastecido;

        return $this;
    }

    /**
     * Get combustibleTotalAbastecido
     *
     * @return float
     */
    public function getCombustibleTotalAbastecido()
    {
        return $this->combustibleTotalAbastecido;
    }

    /**
     * Set realPlan
     *
     * @param float $realPlan
     *
     * @return AnexoUnico
     */
    public function setRealPlan($realPlan)
    {
        $this->realPlan = $realPlan;

        return $this;
    }

    /**
     * Get realPlan
     *
     * @return float
     */
    public function getRealPlan()
    {
        return $this->realPlan;
    }

    /**
     * Set nvehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid
     *
     * @return AnexoUnico
     */
    public function setNvehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid = null)
    {
        $this->nvehiculoid = $nvehiculoid;

        return $this;
    }

    /**
     * Get nvehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getNvehiculoid()
    {
        return $this->nvehiculoid;
    }

    /**
     * Set npersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $npersonaid
     *
     * @return AnexoUnico
     */
    public function setNpersonaid(\Geocuba\PortadoresBundle\Entity\Persona $npersonaid = null)
    {
        $this->npersonaid = $npersonaid;

        return $this;
    }

    /**
     * Get npersonaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getNpersonaid()
    {
        return $this->npersonaid;
    }

    /**
     * Set tipoMantenimientoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoMantenimiento $tipoMantenimientoid
     *
     * @return AnexoUnico
     */
    public function setTipoMantenimientoid(\Geocuba\PortadoresBundle\Entity\TipoMantenimiento $tipoMantenimientoid = null)
    {
        $this->tipoMantenimientoid = $tipoMantenimientoid;

        return $this;
    }

    /**
     * Get tipoMantenimientoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoMantenimiento
     */
    public function getTipoMantenimientoid()
    {
        return $this->tipoMantenimientoid;
    }


}
