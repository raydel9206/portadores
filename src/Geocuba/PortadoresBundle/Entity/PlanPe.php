<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlanPe
 *
 * @ORM\Table(name="datos.plan_pe", indexes={@ORM\Index(name="IDX_A0FE63BE8DF2BD06", columns={"actividad"}), @ORM\Index(name="IDX_A0FE63BE955098DD", columns={"inventario_fisico"}), @ORM\Index(name="IDX_A0FE63BE2BF67E0F", columns={"unidadid"}), @ORM\Index(name="IDX_A0FE63BEE95AADD", columns={"idplan"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\PlanPeRepository")
 */
class PlanPe
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
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

    /**
     * @var string
     *
     * @ORM\Column(name="cant_diesel", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $cantDiesel;

    /**
     * @var string
     *
     * @ORM\Column(name="cant_gasolina", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $cantGasolina;

    /**
     * @var string
     *
     * @ORM\Column(name="plan_lubric", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $planLubric;

    /**
     * @var string
     *
     * @ORM\Column(name="ind_consumo", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $indConsumo;

    /**
     * @var string
     *
     * @ORM\Column(name="km_diesel", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $kmDiesel;

    /**
     * @var string
     *
     * @ORM\Column(name="km_gasolina", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $kmGasolina;

    /**
     * @var string
     *
     * @ORM\Column(name="diesel_anual", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $dieselAnual;

    /**
     * @var string
     *
     * @ORM\Column(name="gasolina_anual", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $gasolinaAnual;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var \Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="actividad", referencedColumnName="id")
     * })
     */
    private $actividad;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="inventario_fisico", referencedColumnName="id")
     * })
     */
    private $inventarioFisico;

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
     * @var \Plan
     *
     * @ORM\ManyToOne(targetEntity="Plan")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idplan", referencedColumnName="id")
     * })
     */
    private $idplan;



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
     * Set anno
     *
     * @param integer $anno
     *
     * @return PlanPe
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
     * Set cantDiesel
     *
     * @param string $cantDiesel
     *
     * @return PlanPe
     */
    public function setCantDiesel($cantDiesel)
    {
        $this->cantDiesel = $cantDiesel;

        return $this;
    }

    /**
     * Get cantDiesel
     *
     * @return string
     */
    public function getCantDiesel()
    {
        return $this->cantDiesel;
    }

    /**
     * Set cantGasolina
     *
     * @param string $cantGasolina
     *
     * @return PlanPe
     */
    public function setCantGasolina($cantGasolina)
    {
        $this->cantGasolina = $cantGasolina;

        return $this;
    }

    /**
     * Get cantGasolina
     *
     * @return string
     */
    public function getCantGasolina()
    {
        return $this->cantGasolina;
    }

    /**
     * Set planLubric
     *
     * @param string $planLubric
     *
     * @return PlanPe
     */
    public function setPlanLubric($planLubric)
    {
        $this->planLubric = $planLubric;

        return $this;
    }

    /**
     * Get planLubric
     *
     * @return string
     */
    public function getPlanLubric()
    {
        return $this->planLubric;
    }

    /**
     * Set indConsumo
     *
     * @param string $indConsumo
     *
     * @return PlanPe
     */
    public function setIndConsumo($indConsumo)
    {
        $this->indConsumo = $indConsumo;

        return $this;
    }

    /**
     * Get indConsumo
     *
     * @return string
     */
    public function getIndConsumo()
    {
        return $this->indConsumo;
    }

    /**
     * Set kmDiesel
     *
     * @param string $kmDiesel
     *
     * @return PlanPe
     */
    public function setKmDiesel($kmDiesel)
    {
        $this->kmDiesel = $kmDiesel;

        return $this;
    }

    /**
     * Get kmDiesel
     *
     * @return string
     */
    public function getKmDiesel()
    {
        return $this->kmDiesel;
    }

    /**
     * Set kmGasolina
     *
     * @param string $kmGasolina
     *
     * @return PlanPe
     */
    public function setKmGasolina($kmGasolina)
    {
        $this->kmGasolina = $kmGasolina;

        return $this;
    }

    /**
     * Get kmGasolina
     *
     * @return string
     */
    public function getKmGasolina()
    {
        return $this->kmGasolina;
    }

    /**
     * Set dieselAnual
     *
     * @param string $dieselAnual
     *
     * @return PlanPe
     */
    public function setDieselAnual($dieselAnual)
    {
        $this->dieselAnual = $dieselAnual;

        return $this;
    }

    /**
     * Get dieselAnual
     *
     * @return string
     */
    public function getDieselAnual()
    {
        return $this->dieselAnual;
    }

    /**
     * Set gasolinaAnual
     *
     * @param string $gasolinaAnual
     *
     * @return PlanPe
     */
    public function setGasolinaAnual($gasolinaAnual)
    {
        $this->gasolinaAnual = $gasolinaAnual;

        return $this;
    }

    /**
     * Get gasolinaAnual
     *
     * @return string
     */
    public function getGasolinaAnual()
    {
        return $this->gasolinaAnual;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return PlanPe
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
     * Set actividad
     *
     * @param \Geocuba\PortadoresBundle\Entity\Actividad $actividad
     *
     * @return PlanPe
     */
    public function setActividad(\Geocuba\PortadoresBundle\Entity\Actividad $actividad = null)
    {
        $this->actividad = $actividad;

        return $this;
    }

    /**
     * Get actividad
     *
     * @return \Geocuba\PortadoresBundle\Entity\Actividad
     */
    public function getActividad()
    {
        return $this->actividad;
    }

    /**
     * Set inventarioFisico
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $inventarioFisico
     *
     * @return PlanPe
     */
    public function setInventarioFisico(\Geocuba\PortadoresBundle\Entity\Vehiculo $inventarioFisico = null)
    {
        $this->inventarioFisico = $inventarioFisico;

        return $this;
    }

    /**
     * Get inventarioFisico
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getInventarioFisico()
    {
        return $this->inventarioFisico;
    }

    /**
     * Set unidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $unidadid
     *
     * @return PlanPe
     */
    public function setUnidadid(\Geocuba\PortadoresBundle\Entity\Unidad $unidadid = null)
    {
        $this->unidadid = $unidadid;

        return $this;
    }

    /**
     * Get unidadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getUnidadid()
    {
        return $this->unidadid;
    }

    /**
     * Set idplan
     *
     * @param \Geocuba\PortadoresBundle\Entity\Plan $idplan
     *
     * @return PlanPe
     */
    public function setIdplan(\Geocuba\PortadoresBundle\Entity\Plan $idplan = null)
    {
        $this->idplan = $idplan;

        return $this;
    }

    /**
     * Get idplan
     *
     * @return \Geocuba\PortadoresBundle\Entity\Plan
     */
    public function getIdplan()
    {
        return $this->idplan;
    }
}
