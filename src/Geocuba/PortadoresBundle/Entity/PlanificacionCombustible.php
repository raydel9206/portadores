<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlanificacionCombustible
 *
 * @ORM\Table(name="datos.planificacion_combustible", indexes={@ORM\Index(name="IDX_E5B8D58D60C04838", columns={"vehiculoid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\PlanificacionCombustibleRepository")
 */
class PlanificacionCombustible
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
     * @var float
     *
     * @ORM\Column(name="combustible_litros_ene", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosEne = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_feb", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosFeb = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_mar", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosMar = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_abr", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosAbr = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_may", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosMay = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_jun", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosJun = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_jul", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosJul = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_ago", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosAgo = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_sep", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosSep = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_oct", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosOct = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_nov", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosNov = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_dic", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosDic = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_ene", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsEne = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_feb", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsFeb = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_mar", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsMar = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_abr", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsAbr = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_may", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsMay = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_jun", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsJun = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_jul", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsJul = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_ago", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsAgo = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_sep", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsSep = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_oct", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsOct = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_nov", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsNov = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_dic", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsDic = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_ene", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteEne = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_feb", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteFeb = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_mar", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteMar = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_abr", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteAbr = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_may", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteMay = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_jun", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteJun = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_jul", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteJul = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_ago", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteAgo = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_sep", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteSep = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_oct", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteOct = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_nov", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteNov = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_dic", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteDic = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_ene", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoEne = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_feb", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoFeb = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_mar", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoMar = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_abr", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoAbr = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_may", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoMay = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_jun", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoJun = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_jul", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoJul = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_ago", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoAgo = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_sep", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoSep = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_oct", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoOct = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_nov", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoNov = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_dic", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoDic = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=false)
     */
    private $anno;

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
     * @var boolean
     *
     * @ORM\Column(name="aprobada", type="boolean", nullable=true)
     */
    private $aprobada;

    /**
     * @var float
     *
     * @ORM\Column(name="combustible_litros_total", type="float", precision=10, scale=0, nullable=true)
     */
    private $combustibleLitrosTotal = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="nivel_act_kms_total", type="float", precision=10, scale=0, nullable=true)
     */
    private $nivelActKmsTotal = '0';


    /**
     * @var float
     *
     * @ORM\Column(name="lubricante_total", type="float", precision=10, scale=0, nullable=true)
     */
    private $lubricanteTotal = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="liquido_freno_total", type="float", precision=10, scale=0, nullable=true)
     */
    private $liquidoFrenoTotal = '0';

    /**
     * @var float
     *
     * @ORM\Column(name="precio_combustible", type="float", precision=10, scale=3, nullable=true)
     */
    private $precioCombustible = '0';

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
     * Set combustibleLitrosEne
     *
     * @param float $combustibleLitrosEne
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosEne($combustibleLitrosEne)
    {
        $this->combustibleLitrosEne = $combustibleLitrosEne;

        return $this;
    }

    /**
     * Get combustibleLitrosEne
     *
     * @return float
     */
    public function getCombustibleLitrosEne()
    {
        return $this->combustibleLitrosEne;
    }

    /**
     * Set combustibleLitrosFeb
     *
     * @param float $combustibleLitrosFeb
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosFeb($combustibleLitrosFeb)
    {
        $this->combustibleLitrosFeb = $combustibleLitrosFeb;

        return $this;
    }

    /**
     * Get combustibleLitrosFeb
     *
     * @return float
     */
    public function getCombustibleLitrosFeb()
    {
        return $this->combustibleLitrosFeb;
    }

    /**
     * Set combustibleLitrosMar
     *
     * @param float $combustibleLitrosMar
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosMar($combustibleLitrosMar)
    {
        $this->combustibleLitrosMar = $combustibleLitrosMar;

        return $this;
    }

    /**
     * Get combustibleLitrosMar
     *
     * @return float
     */
    public function getCombustibleLitrosMar()
    {
        return $this->combustibleLitrosMar;
    }

    /**
     * Set combustibleLitrosAbr
     *
     * @param float $combustibleLitrosAbr
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosAbr($combustibleLitrosAbr)
    {
        $this->combustibleLitrosAbr = $combustibleLitrosAbr;

        return $this;
    }

    /**
     * Get combustibleLitrosAbr
     *
     * @return float
     */
    public function getCombustibleLitrosAbr()
    {
        return $this->combustibleLitrosAbr;
    }

    /**
     * Set combustibleLitrosMay
     *
     * @param float $combustibleLitrosMay
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosMay($combustibleLitrosMay)
    {
        $this->combustibleLitrosMay = $combustibleLitrosMay;

        return $this;
    }

    /**
     * Get combustibleLitrosMay
     *
     * @return float
     */
    public function getCombustibleLitrosMay()
    {
        return $this->combustibleLitrosMay;
    }

    /**
     * Set combustibleLitrosJun
     *
     * @param float $combustibleLitrosJun
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosJun($combustibleLitrosJun)
    {
        $this->combustibleLitrosJun = $combustibleLitrosJun;

        return $this;
    }

    /**
     * Get combustibleLitrosJun
     *
     * @return float
     */
    public function getCombustibleLitrosJun()
    {
        return $this->combustibleLitrosJun;
    }

    /**
     * Set combustibleLitrosJul
     *
     * @param float $combustibleLitrosJul
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosJul($combustibleLitrosJul)
    {
        $this->combustibleLitrosJul = $combustibleLitrosJul;

        return $this;
    }

    /**
     * Get combustibleLitrosJul
     *
     * @return float
     */
    public function getCombustibleLitrosJul()
    {
        return $this->combustibleLitrosJul;
    }

    /**
     * Set combustibleLitrosAgo
     *
     * @param float $combustibleLitrosAgo
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosAgo($combustibleLitrosAgo)
    {
        $this->combustibleLitrosAgo = $combustibleLitrosAgo;

        return $this;
    }

    /**
     * Get combustibleLitrosAgo
     *
     * @return float
     */
    public function getCombustibleLitrosAgo()
    {
        return $this->combustibleLitrosAgo;
    }

    /**
     * Set combustibleLitrosSep
     *
     * @param float $combustibleLitrosSep
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosSep($combustibleLitrosSep)
    {
        $this->combustibleLitrosSep = $combustibleLitrosSep;

        return $this;
    }

    /**
     * Get combustibleLitrosSep
     *
     * @return float
     */
    public function getCombustibleLitrosSep()
    {
        return $this->combustibleLitrosSep;
    }

    /**
     * Set combustibleLitrosOct
     *
     * @param float $combustibleLitrosOct
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosOct($combustibleLitrosOct)
    {
        $this->combustibleLitrosOct = $combustibleLitrosOct;

        return $this;
    }

    /**
     * Get combustibleLitrosOct
     *
     * @return float
     */
    public function getCombustibleLitrosOct()
    {
        return $this->combustibleLitrosOct;
    }

    /**
     * Set combustibleLitrosNov
     *
     * @param float $combustibleLitrosNov
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosNov($combustibleLitrosNov)
    {
        $this->combustibleLitrosNov = $combustibleLitrosNov;

        return $this;
    }

    /**
     * Get combustibleLitrosNov
     *
     * @return float
     */
    public function getCombustibleLitrosNov()
    {
        return $this->combustibleLitrosNov;
    }

    /**
     * Set combustibleLitrosDic
     *
     * @param float $combustibleLitrosDic
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosDic($combustibleLitrosDic)
    {
        $this->combustibleLitrosDic = $combustibleLitrosDic;

        return $this;
    }

    /**
     * Get combustibleLitrosDic
     *
     * @return float
     */
    public function getCombustibleLitrosDic()
    {
        return $this->combustibleLitrosDic;
    }

    /**
     * Set nivelActKmsEne
     *
     * @param float $nivelActKmsEne
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsEne($nivelActKmsEne)
    {
        $this->nivelActKmsEne = $nivelActKmsEne;

        return $this;
    }

    /**
     * Get nivelActKmsEne
     *
     * @return float
     */
    public function getNivelActKmsEne()
    {
        return $this->nivelActKmsEne;
    }

    /**
     * Set nivelActKmsFeb
     *
     * @param float $nivelActKmsFeb
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsFeb($nivelActKmsFeb)
    {
        $this->nivelActKmsFeb = $nivelActKmsFeb;

        return $this;
    }

    /**
     * Get nivelActKmsFeb
     *
     * @return float
     */
    public function getNivelActKmsFeb()
    {
        return $this->nivelActKmsFeb;
    }

    /**
     * Set nivelActKmsMar
     *
     * @param float $nivelActKmsMar
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsMar($nivelActKmsMar)
    {
        $this->nivelActKmsMar = $nivelActKmsMar;

        return $this;
    }

    /**
     * Get nivelActKmsMar
     *
     * @return float
     */
    public function getNivelActKmsMar()
    {
        return $this->nivelActKmsMar;
    }

    /**
     * Set nivelActKmsAbr
     *
     * @param float $nivelActKmsAbr
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsAbr($nivelActKmsAbr)
    {
        $this->nivelActKmsAbr = $nivelActKmsAbr;

        return $this;
    }

    /**
     * Get nivelActKmsAbr
     *
     * @return float
     */
    public function getNivelActKmsAbr()
    {
        return $this->nivelActKmsAbr;
    }

    /**
     * Set nivelActKmsMay
     *
     * @param float $nivelActKmsMay
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsMay($nivelActKmsMay)
    {
        $this->nivelActKmsMay = $nivelActKmsMay;

        return $this;
    }

    /**
     * Get nivelActKmsMay
     *
     * @return float
     */
    public function getNivelActKmsMay()
    {
        return $this->nivelActKmsMay;
    }

    /**
     * Set nivelActKmsJun
     *
     * @param float $nivelActKmsJun
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsJun($nivelActKmsJun)
    {
        $this->nivelActKmsJun = $nivelActKmsJun;

        return $this;
    }

    /**
     * Get nivelActKmsJun
     *
     * @return float
     */
    public function getNivelActKmsJun()
    {
        return $this->nivelActKmsJun;
    }

    /**
     * Set nivelActKmsJul
     *
     * @param float $nivelActKmsJul
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsJul($nivelActKmsJul)
    {
        $this->nivelActKmsJul = $nivelActKmsJul;

        return $this;
    }

    /**
     * Get nivelActKmsJul
     *
     * @return float
     */
    public function getNivelActKmsJul()
    {
        return $this->nivelActKmsJul;
    }

    /**
     * Set nivelActKmsAgo
     *
     * @param float $nivelActKmsAgo
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsAgo($nivelActKmsAgo)
    {
        $this->nivelActKmsAgo = $nivelActKmsAgo;

        return $this;
    }

    /**
     * Get nivelActKmsAgo
     *
     * @return float
     */
    public function getNivelActKmsAgo()
    {
        return $this->nivelActKmsAgo;
    }

    /**
     * Set nivelActKmsSep
     *
     * @param float $nivelActKmsSep
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsSep($nivelActKmsSep)
    {
        $this->nivelActKmsSep = $nivelActKmsSep;

        return $this;
    }

    /**
     * Get nivelActKmsSep
     *
     * @return float
     */
    public function getNivelActKmsSep()
    {
        return $this->nivelActKmsSep;
    }

    /**
     * Set nivelActKmsOct
     *
     * @param float $nivelActKmsOct
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsOct($nivelActKmsOct)
    {
        $this->nivelActKmsOct = $nivelActKmsOct;

        return $this;
    }

    /**
     * Get nivelActKmsOct
     *
     * @return float
     */
    public function getNivelActKmsOct()
    {
        return $this->nivelActKmsOct;
    }

    /**
     * Set nivelActKmsNov
     *
     * @param float $nivelActKmsNov
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsNov($nivelActKmsNov)
    {
        $this->nivelActKmsNov = $nivelActKmsNov;

        return $this;
    }

    /**
     * Get nivelActKmsNov
     *
     * @return float
     */
    public function getNivelActKmsNov()
    {
        return $this->nivelActKmsNov;
    }

    /**
     * Set nivelActKmsDic
     *
     * @param float $nivelActKmsDic
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsDic($nivelActKmsDic)
    {
        $this->nivelActKmsDic = $nivelActKmsDic;

        return $this;
    }

    /**
     * Get nivelActKmsDic
     *
     * @return float
     */
    public function getNivelActKmsDic()
    {
        return $this->nivelActKmsDic;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return PlanificacionCombustible
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
     * Set anno
     *
     * @param integer $anno
     *
     * @return PlanificacionCombustible
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
     * Set vehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculoid
     *
     * @return PlanificacionCombustible
     */
    public function setVehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculoid = null): PlanificacionCombustible
    {
        $this->vehiculoid = $vehiculoid;

        return $this;
    }

    /**
     * Get vehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getVehiculoid(): Vehiculo
    {
        return $this->vehiculoid;
    }

    /**
     * Set aprobada
     *
     * @param boolean $aprobada
     *
     * @return PlanificacionCombustible
     */
    public function setAprobada($aprobada)
    {
        $this->aprobada = $aprobada;

        return $this;
    }

    /**
     * Get aprobada
     *
     * @return boolean
     */
    public function getAprobada()
    {
        return $this->aprobada;
    }

    /**
     * Set lubricanteEne
     *
     * @param float $lubricanteEne
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteEne($lubricanteEne)
    {
        $this->lubricanteEne = $lubricanteEne;

        return $this;
    }

    /**
     * Get lubricanteEne
     *
     * @return float
     */
    public function getLubricanteEne()
    {
        return $this->lubricanteEne;
    }

    /**
     * Set lubricanteFeb
     *
     * @param float $lubricanteFeb
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteFeb($lubricanteFeb)
    {
        $this->lubricanteFeb = $lubricanteFeb;

        return $this;
    }

    /**
     * Get lubricanteFeb
     *
     * @return float
     */
    public function getLubricanteFeb()
    {
        return $this->lubricanteFeb;
    }

    /**
     * Set lubricanteMar
     *
     * @param float $lubricanteMar
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteMar($lubricanteMar)
    {
        $this->lubricanteMar = $lubricanteMar;

        return $this;
    }

    /**
     * Get lubricanteMar
     *
     * @return float
     */
    public function getLubricanteMar()
    {
        return $this->lubricanteMar;
    }

    /**
     * Set lubricanteAbr
     *
     * @param float $lubricanteAbr
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteAbr($lubricanteAbr)
    {
        $this->lubricanteAbr = $lubricanteAbr;

        return $this;
    }

    /**
     * Get lubricanteAbr
     *
     * @return float
     */
    public function getLubricanteAbr()
    {
        return $this->lubricanteAbr;
    }

    /**
     * Set lubricanteMay
     *
     * @param float $lubricanteMay
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteMay($lubricanteMay)
    {
        $this->lubricanteMay = $lubricanteMay;

        return $this;
    }

    /**
     * Get lubricanteMay
     *
     * @return float
     */
    public function getLubricanteMay()
    {
        return $this->lubricanteMay;
    }

    /**
     * Set lubricanteJun
     *
     * @param float $lubricanteJun
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteJun($lubricanteJun)
    {
        $this->lubricanteJun = $lubricanteJun;

        return $this;
    }

    /**
     * Get lubricanteJun
     *
     * @return float
     */
    public function getLubricanteJun()
    {
        return $this->lubricanteJun;
    }

    /**
     * Set lubricanteJul
     *
     * @param float $lubricanteJul
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteJul($lubricanteJul)
    {
        $this->lubricanteJul = $lubricanteJul;

        return $this;
    }

    /**
     * Get lubricanteJul
     *
     * @return float
     */
    public function getLubricanteJul()
    {
        return $this->lubricanteJul;
    }

    /**
     * Set lubricanteAgo
     *
     * @param float $lubricanteAgo
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteAgo($lubricanteAgo)
    {
        $this->lubricanteAgo = $lubricanteAgo;

        return $this;
    }

    /**
     * Get lubricanteAgo
     *
     * @return float
     */
    public function getLubricanteAgo()
    {
        return $this->lubricanteAgo;
    }

    /**
     * Set lubricanteSep
     *
     * @param float $lubricanteSep
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteSep($lubricanteSep)
    {
        $this->lubricanteSep = $lubricanteSep;

        return $this;
    }

    /**
     * Get lubricanteSep
     *
     * @return float
     */
    public function getLubricanteSep()
    {
        return $this->lubricanteSep;
    }

    /**
     * Set lubricanteOct
     *
     * @param float $lubricanteOct
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteOct($lubricanteOct)
    {
        $this->lubricanteOct = $lubricanteOct;

        return $this;
    }

    /**
     * Get lubricanteOct
     *
     * @return float
     */
    public function getLubricanteOct()
    {
        return $this->lubricanteOct;
    }

    /**
     * Set lubricanteNov
     *
     * @param float $lubricanteNov
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteNov($lubricanteNov)
    {
        $this->lubricanteNov = $lubricanteNov;

        return $this;
    }

    /**
     * Get lubricanteNov
     *
     * @return float
     */
    public function getLubricanteNov()
    {
        return $this->lubricanteNov;
    }

    /**
     * Set lubricanteDic
     *
     * @param float $lubricanteDic
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteDic($lubricanteDic)
    {
        $this->lubricanteDic = $lubricanteDic;

        return $this;
    }

    /**
     * Get lubricanteDic
     *
     * @return float
     */
    public function getLubricanteDic()
    {
        return $this->lubricanteDic;
    }

    /**
     * Set liquidoFrenoEne
     *
     * @param float $liquidoFrenoEne
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoEne($liquidoFrenoEne)
    {
        $this->liquidoFrenoEne = $liquidoFrenoEne;

        return $this;
    }

    /**
     * Get liquidoFrenoEne
     *
     * @return float
     */
    public function getLiquidoFrenoEne()
    {
        return $this->liquidoFrenoEne;
    }

    /**
     * Set liquidoFrenoFeb
     *
     * @param float $liquidoFrenoFeb
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoFeb($liquidoFrenoFeb)
    {
        $this->liquidoFrenoFeb = $liquidoFrenoFeb;

        return $this;
    }

    /**
     * Get liquidoFrenoFeb
     *
     * @return float
     */
    public function getLiquidoFrenoFeb()
    {
        return $this->liquidoFrenoFeb;
    }

    /**
     * Set liquidoFrenoMar
     *
     * @param float $liquidoFrenoMar
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoMar($liquidoFrenoMar)
    {
        $this->liquidoFrenoMar = $liquidoFrenoMar;

        return $this;
    }

    /**
     * Get liquidoFrenoMar
     *
     * @return float
     */
    public function getLiquidoFrenoMar()
    {
        return $this->liquidoFrenoMar;
    }

    /**
     * Set liquidoFrenoAbr
     *
     * @param float $liquidoFrenoAbr
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoAbr($liquidoFrenoAbr)
    {
        $this->liquidoFrenoAbr = $liquidoFrenoAbr;

        return $this;
    }

    /**
     * Get liquidoFrenoAbr
     *
     * @return float
     */
    public function getLiquidoFrenoAbr()
    {
        return $this->liquidoFrenoAbr;
    }

    /**
     * Set liquidoFrenoMay
     *
     * @param float $liquidoFrenoMay
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoMay($liquidoFrenoMay)
    {
        $this->liquidoFrenoMay = $liquidoFrenoMay;

        return $this;
    }

    /**
     * Get liquidoFrenoMay
     *
     * @return float
     */
    public function getLiquidoFrenoMay()
    {
        return $this->liquidoFrenoMay;
    }

    /**
     * Set liquidoFrenoJun
     *
     * @param float $liquidoFrenoJun
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoJun($liquidoFrenoJun)
    {
        $this->liquidoFrenoJun = $liquidoFrenoJun;

        return $this;
    }

    /**
     * Get liquidoFrenoJun
     *
     * @return float
     */
    public function getLiquidoFrenoJun()
    {
        return $this->liquidoFrenoJun;
    }

    /**
     * Set liquidoFrenoJul
     *
     * @param float $liquidoFrenoJul
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoJul($liquidoFrenoJul)
    {
        $this->liquidoFrenoJul = $liquidoFrenoJul;

        return $this;
    }

    /**
     * Get liquidoFrenoJul
     *
     * @return float
     */
    public function getLiquidoFrenoJul()
    {
        return $this->liquidoFrenoJul;
    }

    /**
     * Set liquidoFrenoAgo
     *
     * @param float $liquidoFrenoAgo
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoAgo($liquidoFrenoAgo)
    {
        $this->liquidoFrenoAgo = $liquidoFrenoAgo;

        return $this;
    }

    /**
     * Get liquidoFrenoAgo
     *
     * @return float
     */
    public function getLiquidoFrenoAgo()
    {
        return $this->liquidoFrenoAgo;
    }

    /**
     * Set liquidoFrenoSep
     *
     * @param float $liquidoFrenoSep
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoSep($liquidoFrenoSep)
    {
        $this->liquidoFrenoSep = $liquidoFrenoSep;

        return $this;
    }

    /**
     * Get liquidoFrenoSep
     *
     * @return float
     */
    public function getLiquidoFrenoSep()
    {
        return $this->liquidoFrenoSep;
    }

    /**
     * Set liquidoFrenoOct
     *
     * @param float $liquidoFrenoOct
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoOct($liquidoFrenoOct)
    {
        $this->liquidoFrenoOct = $liquidoFrenoOct;

        return $this;
    }

    /**
     * Get liquidoFrenoOct
     *
     * @return float
     */
    public function getLiquidoFrenoOct()
    {
        return $this->liquidoFrenoOct;
    }

    /**
     * Set liquidoFrenoNov
     *
     * @param float $liquidoFrenoNov
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoNov($liquidoFrenoNov)
    {
        $this->liquidoFrenoNov = $liquidoFrenoNov;

        return $this;
    }

    /**
     * Get liquidoFrenoNov
     *
     * @return float
     */
    public function getLiquidoFrenoNov()
    {
        return $this->liquidoFrenoNov;
    }

    /**
     * Set liquidoFrenoDic
     *
     * @param float $liquidoFrenoDic
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoDic($liquidoFrenoDic)
    {
        $this->liquidoFrenoDic = $liquidoFrenoDic;

        return $this;
    }

    /**
     * Get liquidoFrenoDic
     *
     * @return float
     */
    public function getLiquidoFrenoDic()
    {
        return $this->liquidoFrenoDic;
    }

    /**
     * Set combustibleLitrosTotal
     *
     * @param float $combustibleLitrosTotal
     *
     * @return PlanificacionCombustible
     */
    public function setCombustibleLitrosTotal($combustibleLitrosTotal)
    {
        $this->combustibleLitrosTotal = $combustibleLitrosTotal;

        return $this;
    }

    /**
     * Get combustibleLitrosTotal
     *
     * @return float
     */
    public function getCombustibleLitrosTotal()
    {
        return $this->combustibleLitrosTotal;
    }

    /**
     * Set nivelActKmsTotal
     *
     * @param float $nivelActKmsTotal
     *
     * @return PlanificacionCombustible
     */
    public function setNivelActKmsTotal($nivelActKmsTotal)
    {
        $this->nivelActKmsTotal = $nivelActKmsTotal;

        return $this;
    }

    /**
     * Get nivelActKmsTotal
     *
     * @return float
     */
    public function getNivelActKmsTotal()
    {
        return $this->nivelActKmsTotal;
    }

    /**
     * Set lubricanteTotal
     *
     * @param float $lubricanteTotal
     *
     * @return PlanificacionCombustible
     */
    public function setLubricanteTotal($lubricanteTotal)
    {
        $this->lubricanteTotal = $lubricanteTotal;

        return $this;
    }

    /**
     * Get lubricanteTotal
     *
     * @return float
     */
    public function getLubricanteTotal()
    {
        return $this->lubricanteTotal;
    }

    /**
     * Set liquidoFrenoTotal
     *
     * @param float $liquidoFrenoTotal
     *
     * @return PlanificacionCombustible
     */
    public function setLiquidoFrenoTotal($liquidoFrenoTotal)
    {
        $this->liquidoFrenoTotal = $liquidoFrenoTotal;

        return $this;
    }

    /**
     * Get liquidoFrenoTotal
     *
     * @return float
     */
    public function getLiquidoFrenoTotal()
    {
        return $this->liquidoFrenoTotal;
    }

    /**
     * Set precioCombustible
     *
     * @param float $precioCombustible
     *
     * @return PlanificacionCombustible
     */
    public function setPrecioCombustible($precioCombustible)
    {
        $this->precioCombustible = $precioCombustible;

        return $this;
    }

    /**
     * Get precioCombustible
     *
     * @return float
     */
    public function getPrecioCombustible()
    {
        return $this->precioCombustible;
    }
}
