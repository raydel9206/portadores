<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CDA001
 *
 * @ORM\Table(name="datos.cda001", indexes={@ORM\Index(name="IDX_CB458883581E2F8", columns={"portadorid"}), @ORM\Index(name="IDX_CB4588833C7CEFFE", columns={"nunidadid"}), @ORM\Index(name="IDX_CB4588836014FACA", columns={"actividad_id"}), @ORM\Index(name="IDX_CB458883B00B2B2D", columns={"moneda"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\CDA001Repository")
 */
class CDA001
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
     * @var int|null
     *
     * @ORM\Column(name="anno", type="integer", nullable=true, options={"comment"=""})
     */
    private $anno;

    /**
     * @var string|null
     *
     * @ORM\Column(name="real_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $realNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="real_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $realConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="real_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $realIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="acumulado_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $acumuladoNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="acumulado_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $acumuladoConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="acumulado_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $acumuladoIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estimado_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $estimadoNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estimado_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $estimadoConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="estimado_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $estimadoIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="propuesta_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $propuestaNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="propuesta_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $propuestaConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="propuesta_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $propuestaIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="plan_final_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $planFinalNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="plan_final_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $planFinalConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="plan_final_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $planFinalIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_desglose_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $totalDesgloseNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_desglose_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $totalDesgloseConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="total_desglose_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $totalDesgloseIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="enero_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $eneroNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="enero_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $eneroConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="enero_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $eneroIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="febrero_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $febreroNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="febrero_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $febreroConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="febrero_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $febreroIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="marzo_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $marzoNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="marzo_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $marzoConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="marzo_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $marzoIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="abril_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $abrilNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="abril_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $abrilConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="abril_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $abrilIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mayo_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $mayoNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mayo_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $mayoConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mayo_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $mayoIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="junio_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $junioNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="junio_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $junioConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="junio_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $junioIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="julio_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $julioNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="julio_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $julioConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="julio_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $julioIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="agosto_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $agostoNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="agosto_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $agostoConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="agosto_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $agostoIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="septiembre_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $septiembreNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="septiembre_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $septiembreConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="septiembre_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $septiembreIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="octubre_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $octubreNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="octubre_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $octubreConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="octubre_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $octubreIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="noviembre_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $noviembreNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="noviembre_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $noviembreConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="noviembre_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $noviembreIndice;

    /**
     * @var string|null
     *
     * @ORM\Column(name="diciembre_nivel_act", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $diciembreNivelAct;

    /**
     * @var string|null
     *
     * @ORM\Column(name="diciembre_consumo", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $diciembreConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="diciembre_indice", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $diciembreIndice;

    /**
     * @var \Portador
     *
     * @ORM\ManyToOne(targetEntity="Portador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portadorid", referencedColumnName="id")
     * })
     */
    private $portadorid;

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
     * @var \Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="actividad_id", referencedColumnName="id")
     * })
     */
    private $actividad;

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
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set anno.
     *
     * @param int|null $anno
     *
     * @return CDA001
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
     * Set realNivelAct.
     *
     * @param string|null $realNivelAct
     *
     * @return CDA001
     */
    public function setRealNivelAct($realNivelAct = null)
    {
        $this->realNivelAct = $realNivelAct;
    
        return $this;
    }

    /**
     * Get realNivelAct.
     *
     * @return string|null
     */
    public function getRealNivelAct()
    {
        return $this->realNivelAct;
    }

    /**
     * Set realConsumo.
     *
     * @param string|null $realConsumo
     *
     * @return CDA001
     */
    public function setRealConsumo($realConsumo = null)
    {
        $this->realConsumo = $realConsumo;
    
        return $this;
    }

    /**
     * Get realConsumo.
     *
     * @return string|null
     */
    public function getRealConsumo()
    {
        return $this->realConsumo;
    }

    /**
     * Set realIndice.
     *
     * @param string|null $realIndice
     *
     * @return CDA001
     */
    public function setRealIndice($realIndice = null)
    {
        $this->realIndice = $realIndice;
    
        return $this;
    }

    /**
     * Get realIndice.
     *
     * @return string|null
     */
    public function getRealIndice()
    {
        return $this->realIndice;
    }

    /**
     * Set acumuladoNivelAct.
     *
     * @param string|null $acumuladoNivelAct
     *
     * @return CDA001
     */
    public function setAcumuladoNivelAct($acumuladoNivelAct = null)
    {
        $this->acumuladoNivelAct = $acumuladoNivelAct;
    
        return $this;
    }

    /**
     * Get acumuladoNivelAct.
     *
     * @return string|null
     */
    public function getAcumuladoNivelAct()
    {
        return $this->acumuladoNivelAct;
    }

    /**
     * Set acumuladoConsumo.
     *
     * @param string|null $acumuladoConsumo
     *
     * @return CDA001
     */
    public function setAcumuladoConsumo($acumuladoConsumo = null)
    {
        $this->acumuladoConsumo = $acumuladoConsumo;
    
        return $this;
    }

    /**
     * Get acumuladoConsumo.
     *
     * @return string|null
     */
    public function getAcumuladoConsumo()
    {
        return $this->acumuladoConsumo;
    }

    /**
     * Set acumuladoIndice.
     *
     * @param string|null $acumuladoIndice
     *
     * @return CDA001
     */
    public function setAcumuladoIndice($acumuladoIndice = null)
    {
        $this->acumuladoIndice = $acumuladoIndice;
    
        return $this;
    }

    /**
     * Get acumuladoIndice.
     *
     * @return string|null
     */
    public function getAcumuladoIndice()
    {
        return $this->acumuladoIndice;
    }

    /**
     * Set estimadoNivelAct.
     *
     * @param string|null $estimadoNivelAct
     *
     * @return CDA001
     */
    public function setEstimadoNivelAct($estimadoNivelAct = null)
    {
        $this->estimadoNivelAct = $estimadoNivelAct;
    
        return $this;
    }

    /**
     * Get estimadoNivelAct.
     *
     * @return string|null
     */
    public function getEstimadoNivelAct()
    {
        return $this->estimadoNivelAct;
    }

    /**
     * Set estimadoConsumo.
     *
     * @param string|null $estimadoConsumo
     *
     * @return CDA001
     */
    public function setEstimadoConsumo($estimadoConsumo = null)
    {
        $this->estimadoConsumo = $estimadoConsumo;
    
        return $this;
    }

    /**
     * Get estimadoConsumo.
     *
     * @return string|null
     */
    public function getEstimadoConsumo()
    {
        return $this->estimadoConsumo;
    }

    /**
     * Set estimadoIndice.
     *
     * @param string|null $estimadoIndice
     *
     * @return CDA001
     */
    public function setEstimadoIndice($estimadoIndice = null)
    {
        $this->estimadoIndice = $estimadoIndice;
    
        return $this;
    }

    /**
     * Get estimadoIndice.
     *
     * @return string|null
     */
    public function getEstimadoIndice()
    {
        return $this->estimadoIndice;
    }

    /**
     * Set propuestaNivelAct.
     *
     * @param string|null $propuestaNivelAct
     *
     * @return CDA001
     */
    public function setPropuestaNivelAct($propuestaNivelAct = null)
    {
        $this->propuestaNivelAct = $propuestaNivelAct;
    
        return $this;
    }

    /**
     * Get propuestaNivelAct.
     *
     * @return string|null
     */
    public function getPropuestaNivelAct()
    {
        return $this->propuestaNivelAct;
    }

    /**
     * Set propuestaConsumo.
     *
     * @param string|null $propuestaConsumo
     *
     * @return CDA001
     */
    public function setPropuestaConsumo($propuestaConsumo = null)
    {
        $this->propuestaConsumo = $propuestaConsumo;
    
        return $this;
    }

    /**
     * Get propuestaConsumo.
     *
     * @return string|null
     */
    public function getPropuestaConsumo()
    {
        return $this->propuestaConsumo;
    }

    /**
     * Set propuestaIndice.
     *
     * @param string|null $propuestaIndice
     *
     * @return CDA001
     */
    public function setPropuestaIndice($propuestaIndice = null)
    {
        $this->propuestaIndice = $propuestaIndice;
    
        return $this;
    }

    /**
     * Get propuestaIndice.
     *
     * @return string|null
     */
    public function getPropuestaIndice()
    {
        return $this->propuestaIndice;
    }

    /**
     * Set planFinalNivelAct.
     *
     * @param string|null $planFinalNivelAct
     *
     * @return CDA001
     */
    public function setPlanFinalNivelAct($planFinalNivelAct = null)
    {
        $this->planFinalNivelAct = $planFinalNivelAct;
    
        return $this;
    }

    /**
     * Get planFinalNivelAct.
     *
     * @return string|null
     */
    public function getPlanFinalNivelAct()
    {
        return $this->planFinalNivelAct;
    }

    /**
     * Set planFinalConsumo.
     *
     * @param string|null $planFinalConsumo
     *
     * @return CDA001
     */
    public function setPlanFinalConsumo($planFinalConsumo = null)
    {
        $this->planFinalConsumo = $planFinalConsumo;
    
        return $this;
    }

    /**
     * Get planFinalConsumo.
     *
     * @return string|null
     */
    public function getPlanFinalConsumo()
    {
        return $this->planFinalConsumo;
    }

    /**
     * Set planFinalIndice.
     *
     * @param string|null $planFinalIndice
     *
     * @return CDA001
     */
    public function setPlanFinalIndice($planFinalIndice = null)
    {
        $this->planFinalIndice = $planFinalIndice;
    
        return $this;
    }

    /**
     * Get planFinalIndice.
     *
     * @return string|null
     */
    public function getPlanFinalIndice()
    {
        return $this->planFinalIndice;
    }

    /**
     * Set totalDesgloseNivelAct.
     *
     * @param string|null $totalDesgloseNivelAct
     *
     * @return CDA001
     */
    public function setTotalDesgloseNivelAct($totalDesgloseNivelAct = null)
    {
        $this->totalDesgloseNivelAct = $totalDesgloseNivelAct;
    
        return $this;
    }

    /**
     * Get totalDesgloseNivelAct.
     *
     * @return string|null
     */
    public function getTotalDesgloseNivelAct()
    {
        return $this->totalDesgloseNivelAct;
    }

    /**
     * Set totalDesgloseConsumo.
     *
     * @param string|null $totalDesgloseConsumo
     *
     * @return CDA001
     */
    public function setTotalDesgloseConsumo($totalDesgloseConsumo = null)
    {
        $this->totalDesgloseConsumo = $totalDesgloseConsumo;
    
        return $this;
    }

    /**
     * Get totalDesgloseConsumo.
     *
     * @return string|null
     */
    public function getTotalDesgloseConsumo()
    {
        return $this->totalDesgloseConsumo;
    }

    /**
     * Set totalDesgloseIndice.
     *
     * @param string|null $totalDesgloseIndice
     *
     * @return CDA001
     */
    public function setTotalDesgloseIndice($totalDesgloseIndice = null)
    {
        $this->totalDesgloseIndice = $totalDesgloseIndice;
    
        return $this;
    }

    /**
     * Get totalDesgloseIndice.
     *
     * @return string|null
     */
    public function getTotalDesgloseIndice()
    {
        return $this->totalDesgloseIndice;
    }

    /**
     * Set eneroNivelAct.
     *
     * @param string|null $eneroNivelAct
     *
     * @return CDA001
     */
    public function setEneroNivelAct($eneroNivelAct = null)
    {
        $this->eneroNivelAct = $eneroNivelAct;
    
        return $this;
    }

    /**
     * Get eneroNivelAct.
     *
     * @return string|null
     */
    public function getEneroNivelAct()
    {
        return $this->eneroNivelAct;
    }

    /**
     * Set eneroConsumo.
     *
     * @param string|null $eneroConsumo
     *
     * @return CDA001
     */
    public function setEneroConsumo($eneroConsumo = null)
    {
        $this->eneroConsumo = $eneroConsumo;
    
        return $this;
    }

    /**
     * Get eneroConsumo.
     *
     * @return string|null
     */
    public function getEneroConsumo()
    {
        return $this->eneroConsumo;
    }

    /**
     * Set eneroIndice.
     *
     * @param string|null $eneroIndice
     *
     * @return CDA001
     */
    public function setEneroIndice($eneroIndice = null)
    {
        $this->eneroIndice = $eneroIndice;
    
        return $this;
    }

    /**
     * Get eneroIndice.
     *
     * @return string|null
     */
    public function getEneroIndice()
    {
        return $this->eneroIndice;
    }

    /**
     * Set febreroNivelAct.
     *
     * @param string|null $febreroNivelAct
     *
     * @return CDA001
     */
    public function setFebreroNivelAct($febreroNivelAct = null)
    {
        $this->febreroNivelAct = $febreroNivelAct;
    
        return $this;
    }

    /**
     * Get febreroNivelAct.
     *
     * @return string|null
     */
    public function getFebreroNivelAct()
    {
        return $this->febreroNivelAct;
    }

    /**
     * Set febreroConsumo.
     *
     * @param string|null $febreroConsumo
     *
     * @return CDA001
     */
    public function setFebreroConsumo($febreroConsumo = null)
    {
        $this->febreroConsumo = $febreroConsumo;
    
        return $this;
    }

    /**
     * Get febreroConsumo.
     *
     * @return string|null
     */
    public function getFebreroConsumo()
    {
        return $this->febreroConsumo;
    }

    /**
     * Set febreroIndice.
     *
     * @param string|null $febreroIndice
     *
     * @return CDA001
     */
    public function setFebreroIndice($febreroIndice = null)
    {
        $this->febreroIndice = $febreroIndice;
    
        return $this;
    }

    /**
     * Get febreroIndice.
     *
     * @return string|null
     */
    public function getFebreroIndice()
    {
        return $this->febreroIndice;
    }

    /**
     * Set marzoNivelAct.
     *
     * @param string|null $marzoNivelAct
     *
     * @return CDA001
     */
    public function setMarzoNivelAct($marzoNivelAct = null)
    {
        $this->marzoNivelAct = $marzoNivelAct;
    
        return $this;
    }

    /**
     * Get marzoNivelAct.
     *
     * @return string|null
     */
    public function getMarzoNivelAct()
    {
        return $this->marzoNivelAct;
    }

    /**
     * Set marzoConsumo.
     *
     * @param string|null $marzoConsumo
     *
     * @return CDA001
     */
    public function setMarzoConsumo($marzoConsumo = null)
    {
        $this->marzoConsumo = $marzoConsumo;
    
        return $this;
    }

    /**
     * Get marzoConsumo.
     *
     * @return string|null
     */
    public function getMarzoConsumo()
    {
        return $this->marzoConsumo;
    }

    /**
     * Set marzoIndice.
     *
     * @param string|null $marzoIndice
     *
     * @return CDA001
     */
    public function setMarzoIndice($marzoIndice = null)
    {
        $this->marzoIndice = $marzoIndice;
    
        return $this;
    }

    /**
     * Get marzoIndice.
     *
     * @return string|null
     */
    public function getMarzoIndice()
    {
        return $this->marzoIndice;
    }

    /**
     * Set abrilNivelAct.
     *
     * @param string|null $abrilNivelAct
     *
     * @return CDA001
     */
    public function setAbrilNivelAct($abrilNivelAct = null)
    {
        $this->abrilNivelAct = $abrilNivelAct;
    
        return $this;
    }

    /**
     * Get abrilNivelAct.
     *
     * @return string|null
     */
    public function getAbrilNivelAct()
    {
        return $this->abrilNivelAct;
    }

    /**
     * Set abrilConsumo.
     *
     * @param string|null $abrilConsumo
     *
     * @return CDA001
     */
    public function setAbrilConsumo($abrilConsumo = null)
    {
        $this->abrilConsumo = $abrilConsumo;
    
        return $this;
    }

    /**
     * Get abrilConsumo.
     *
     * @return string|null
     */
    public function getAbrilConsumo()
    {
        return $this->abrilConsumo;
    }

    /**
     * Set abrilIndice.
     *
     * @param string|null $abrilIndice
     *
     * @return CDA001
     */
    public function setAbrilIndice($abrilIndice = null)
    {
        $this->abrilIndice = $abrilIndice;
    
        return $this;
    }

    /**
     * Get abrilIndice.
     *
     * @return string|null
     */
    public function getAbrilIndice()
    {
        return $this->abrilIndice;
    }

    /**
     * Set mayoNivelAct.
     *
     * @param string|null $mayoNivelAct
     *
     * @return CDA001
     */
    public function setMayoNivelAct($mayoNivelAct = null)
    {
        $this->mayoNivelAct = $mayoNivelAct;
    
        return $this;
    }

    /**
     * Get mayoNivelAct.
     *
     * @return string|null
     */
    public function getMayoNivelAct()
    {
        return $this->mayoNivelAct;
    }

    /**
     * Set mayoConsumo.
     *
     * @param string|null $mayoConsumo
     *
     * @return CDA001
     */
    public function setMayoConsumo($mayoConsumo = null)
    {
        $this->mayoConsumo = $mayoConsumo;
    
        return $this;
    }

    /**
     * Get mayoConsumo.
     *
     * @return string|null
     */
    public function getMayoConsumo()
    {
        return $this->mayoConsumo;
    }

    /**
     * Set mayoIndice.
     *
     * @param string|null $mayoIndice
     *
     * @return CDA001
     */
    public function setMayoIndice($mayoIndice = null)
    {
        $this->mayoIndice = $mayoIndice;
    
        return $this;
    }

    /**
     * Get mayoIndice.
     *
     * @return string|null
     */
    public function getMayoIndice()
    {
        return $this->mayoIndice;
    }

    /**
     * Set junioNivelAct.
     *
     * @param string|null $junioNivelAct
     *
     * @return CDA001
     */
    public function setJunioNivelAct($junioNivelAct = null)
    {
        $this->junioNivelAct = $junioNivelAct;
    
        return $this;
    }

    /**
     * Get junioNivelAct.
     *
     * @return string|null
     */
    public function getJunioNivelAct()
    {
        return $this->junioNivelAct;
    }

    /**
     * Set junioConsumo.
     *
     * @param string|null $junioConsumo
     *
     * @return CDA001
     */
    public function setJunioConsumo($junioConsumo = null)
    {
        $this->junioConsumo = $junioConsumo;
    
        return $this;
    }

    /**
     * Get junioConsumo.
     *
     * @return string|null
     */
    public function getJunioConsumo()
    {
        return $this->junioConsumo;
    }

    /**
     * Set junioIndice.
     *
     * @param string|null $junioIndice
     *
     * @return CDA001
     */
    public function setJunioIndice($junioIndice = null)
    {
        $this->junioIndice = $junioIndice;
    
        return $this;
    }

    /**
     * Get junioIndice.
     *
     * @return string|null
     */
    public function getJunioIndice()
    {
        return $this->junioIndice;
    }

    /**
     * Set julioNivelAct.
     *
     * @param string|null $julioNivelAct
     *
     * @return CDA001
     */
    public function setJulioNivelAct($julioNivelAct = null)
    {
        $this->julioNivelAct = $julioNivelAct;
    
        return $this;
    }

    /**
     * Get julioNivelAct.
     *
     * @return string|null
     */
    public function getJulioNivelAct()
    {
        return $this->julioNivelAct;
    }

    /**
     * Set julioConsumo.
     *
     * @param string|null $julioConsumo
     *
     * @return CDA001
     */
    public function setJulioConsumo($julioConsumo = null)
    {
        $this->julioConsumo = $julioConsumo;
    
        return $this;
    }

    /**
     * Get julioConsumo.
     *
     * @return string|null
     */
    public function getJulioConsumo()
    {
        return $this->julioConsumo;
    }

    /**
     * Set julioIndice.
     *
     * @param string|null $julioIndice
     *
     * @return CDA001
     */
    public function setJulioIndice($julioIndice = null)
    {
        $this->julioIndice = $julioIndice;
    
        return $this;
    }

    /**
     * Get julioIndice.
     *
     * @return string|null
     */
    public function getJulioIndice()
    {
        return $this->julioIndice;
    }

    /**
     * Set agostoNivelAct.
     *
     * @param string|null $agostoNivelAct
     *
     * @return CDA001
     */
    public function setAgostoNivelAct($agostoNivelAct = null)
    {
        $this->agostoNivelAct = $agostoNivelAct;
    
        return $this;
    }

    /**
     * Get agostoNivelAct.
     *
     * @return string|null
     */
    public function getAgostoNivelAct()
    {
        return $this->agostoNivelAct;
    }

    /**
     * Set agostoConsumo.
     *
     * @param string|null $agostoConsumo
     *
     * @return CDA001
     */
    public function setAgostoConsumo($agostoConsumo = null)
    {
        $this->agostoConsumo = $agostoConsumo;
    
        return $this;
    }

    /**
     * Get agostoConsumo.
     *
     * @return string|null
     */
    public function getAgostoConsumo()
    {
        return $this->agostoConsumo;
    }

    /**
     * Set agostoIndice.
     *
     * @param string|null $agostoIndice
     *
     * @return CDA001
     */
    public function setAgostoIndice($agostoIndice = null)
    {
        $this->agostoIndice = $agostoIndice;
    
        return $this;
    }

    /**
     * Get agostoIndice.
     *
     * @return string|null
     */
    public function getAgostoIndice()
    {
        return $this->agostoIndice;
    }

    /**
     * Set septiembreNivelAct.
     *
     * @param string|null $septiembreNivelAct
     *
     * @return CDA001
     */
    public function setSeptiembreNivelAct($septiembreNivelAct = null)
    {
        $this->septiembreNivelAct = $septiembreNivelAct;
    
        return $this;
    }

    /**
     * Get septiembreNivelAct.
     *
     * @return string|null
     */
    public function getSeptiembreNivelAct()
    {
        return $this->septiembreNivelAct;
    }

    /**
     * Set septiembreConsumo.
     *
     * @param string|null $septiembreConsumo
     *
     * @return CDA001
     */
    public function setSeptiembreConsumo($septiembreConsumo = null)
    {
        $this->septiembreConsumo = $septiembreConsumo;
    
        return $this;
    }

    /**
     * Get septiembreConsumo.
     *
     * @return string|null
     */
    public function getSeptiembreConsumo()
    {
        return $this->septiembreConsumo;
    }

    /**
     * Set septiembreIndice.
     *
     * @param string|null $septiembreIndice
     *
     * @return CDA001
     */
    public function setSeptiembreIndice($septiembreIndice = null)
    {
        $this->septiembreIndice = $septiembreIndice;
    
        return $this;
    }

    /**
     * Get septiembreIndice.
     *
     * @return string|null
     */
    public function getSeptiembreIndice()
    {
        return $this->septiembreIndice;
    }

    /**
     * Set octubreNivelAct.
     *
     * @param string|null $octubreNivelAct
     *
     * @return CDA001
     */
    public function setOctubreNivelAct($octubreNivelAct = null)
    {
        $this->octubreNivelAct = $octubreNivelAct;
    
        return $this;
    }

    /**
     * Get octubreNivelAct.
     *
     * @return string|null
     */
    public function getOctubreNivelAct()
    {
        return $this->octubreNivelAct;
    }

    /**
     * Set octubreConsumo.
     *
     * @param string|null $octubreConsumo
     *
     * @return CDA001
     */
    public function setOctubreConsumo($octubreConsumo = null)
    {
        $this->octubreConsumo = $octubreConsumo;
    
        return $this;
    }

    /**
     * Get octubreConsumo.
     *
     * @return string|null
     */
    public function getOctubreConsumo()
    {
        return $this->octubreConsumo;
    }

    /**
     * Set octubreIndice.
     *
     * @param string|null $octubreIndice
     *
     * @return CDA001
     */
    public function setOctubreIndice($octubreIndice = null)
    {
        $this->octubreIndice = $octubreIndice;
    
        return $this;
    }

    /**
     * Get octubreIndice.
     *
     * @return string|null
     */
    public function getOctubreIndice()
    {
        return $this->octubreIndice;
    }

    /**
     * Set noviembreNivelAct.
     *
     * @param string|null $noviembreNivelAct
     *
     * @return CDA001
     */
    public function setNoviembreNivelAct($noviembreNivelAct = null)
    {
        $this->noviembreNivelAct = $noviembreNivelAct;
    
        return $this;
    }

    /**
     * Get noviembreNivelAct.
     *
     * @return string|null
     */
    public function getNoviembreNivelAct()
    {
        return $this->noviembreNivelAct;
    }

    /**
     * Set noviembreConsumo.
     *
     * @param string|null $noviembreConsumo
     *
     * @return CDA001
     */
    public function setNoviembreConsumo($noviembreConsumo = null)
    {
        $this->noviembreConsumo = $noviembreConsumo;
    
        return $this;
    }

    /**
     * Get noviembreConsumo.
     *
     * @return string|null
     */
    public function getNoviembreConsumo()
    {
        return $this->noviembreConsumo;
    }

    /**
     * Set noviembreIndice.
     *
     * @param string|null $noviembreIndice
     *
     * @return CDA001
     */
    public function setNoviembreIndice($noviembreIndice = null)
    {
        $this->noviembreIndice = $noviembreIndice;
    
        return $this;
    }

    /**
     * Get noviembreIndice.
     *
     * @return string|null
     */
    public function getNoviembreIndice()
    {
        return $this->noviembreIndice;
    }

    /**
     * Set diciembreNivelAct.
     *
     * @param string|null $diciembreNivelAct
     *
     * @return CDA001
     */
    public function setDiciembreNivelAct($diciembreNivelAct = null)
    {
        $this->diciembreNivelAct = $diciembreNivelAct;
    
        return $this;
    }

    /**
     * Get diciembreNivelAct.
     *
     * @return string|null
     */
    public function getDiciembreNivelAct()
    {
        return $this->diciembreNivelAct;
    }

    /**
     * Set diciembreConsumo.
     *
     * @param string|null $diciembreConsumo
     *
     * @return CDA001
     */
    public function setDiciembreConsumo($diciembreConsumo = null)
    {
        $this->diciembreConsumo = $diciembreConsumo;
    
        return $this;
    }

    /**
     * Get diciembreConsumo.
     *
     * @return string|null
     */
    public function getDiciembreConsumo()
    {
        return $this->diciembreConsumo;
    }

    /**
     * Set diciembreIndice.
     *
     * @param string|null $diciembreIndice
     *
     * @return CDA001
     */
    public function setDiciembreIndice($diciembreIndice = null)
    {
        $this->diciembreIndice = $diciembreIndice;
    
        return $this;
    }

    /**
     * Get diciembreIndice.
     *
     * @return string|null
     */
    public function getDiciembreIndice()
    {
        return $this->diciembreIndice;
    }

    /**
     * Set portadorid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Portador|null $portadorid
     *
     * @return CDA001
     */
    public function setPortadorid(\Geocuba\PortadoresBundle\Entity\Portador $portadorid = null)
    {
        $this->portadorid = $portadorid;
    
        return $this;
    }

    /**
     * Get portadorid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Portador|null
     */
    public function getPortadorid()
    {
        return $this->portadorid;
    }

    /**
     * Set nunidadid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $nunidadid
     *
     * @return CDA001
     */
    public function setNunidadid(\Geocuba\PortadoresBundle\Entity\Unidad $nunidadid = null)
    {
        $this->nunidadid = $nunidadid;
    
        return $this;
    }

    /**
     * Get nunidadid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getNunidadid()
    {
        return $this->nunidadid;
    }

    /**
     * Set actividad.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Actividad|null $actividad
     *
     * @return CDA001
     */
    public function setActividad(\Geocuba\PortadoresBundle\Entity\Actividad $actividad = null)
    {
        $this->actividad = $actividad;
    
        return $this;
    }

    /**
     * Get actividad.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Actividad|null
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set moneda.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
     *
     * @return CDA001
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
