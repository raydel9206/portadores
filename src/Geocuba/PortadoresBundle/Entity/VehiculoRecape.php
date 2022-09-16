<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VehiculoRecape
 *
 * @ORM\Table(name="datos.vehiculo_recape", indexes={@ORM\Index(name="IDX_EDEBAC0F78D1350A", columns={"id_plan_recape"}), @ORM\Index(name="IDX_EDEBAC0F9921FA96", columns={"id_vehiculo"})})
 * @ORM\Entity
 */
class VehiculoRecape
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
     * @ORM\Column(name="marca_neumatico", type="string", length=255, nullable=true)
     */
    private $marcaNeumatico;

    /**
     * @var string
     *
     * @ORM\Column(name="medidas_neumatico", type="string", length=255, nullable=true)
     */
    private $medidasNeumatico;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_rotacion_neumaticos", type="date", nullable=true)
     */
    private $fechaRotacionNeumaticos;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", length=255, nullable=true)
     */
    private $mes;

    /**
     * @var float
     *
     * @ORM\Column(name="cant_neumaticos", type="float", precision=10, scale=0, nullable=true)
     */
    private $cantNeumaticos;

    /**
     * @var \PlanRecape
     *
     * @ORM\ManyToOne(targetEntity="PlanRecape")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_plan_recape", referencedColumnName="id")
     * })
     */
    private $idPlanRecape;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_vehiculo", referencedColumnName="id")
     * })
     */
    private $idVehiculo;



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
     * Set marcaNeumatico
     *
     * @param string $marcaNeumatico
     *
     * @return VehiculoRecape
     */
    public function setMarcaNeumatico($marcaNeumatico)
    {
        $this->marcaNeumatico = $marcaNeumatico;

        return $this;
    }

    /**
     * Get marcaNeumatico
     *
     * @return string
     */
    public function getMarcaNeumatico()
    {
        return $this->marcaNeumatico;
    }

    /**
     * Set medidasNeumatico
     *
     * @param string $medidasNeumatico
     *
     * @return VehiculoRecape
     */
    public function setMedidasNeumatico($medidasNeumatico)
    {
        $this->medidasNeumatico = $medidasNeumatico;

        return $this;
    }

    /**
     * Get medidasNeumatico
     *
     * @return string
     */
    public function getMedidasNeumatico()
    {
        return $this->medidasNeumatico;
    }

    /**
     * Set fechaRotacionNeumaticos
     *
     * @param \DateTime $fechaRotacionNeumaticos
     *
     * @return VehiculoRecape
     */
    public function setFechaRotacionNeumaticos($fechaRotacionNeumaticos)
    {
        $this->fechaRotacionNeumaticos = $fechaRotacionNeumaticos;

        return $this;
    }

    /**
     * Get fechaRotacionNeumaticos
     *
     * @return \DateTime
     */
    public function getFechaRotacionNeumaticos()
    {
        return $this->fechaRotacionNeumaticos;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return VehiculoRecape
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
     * Set mes
     *
     * @param string $mes
     *
     * @return VehiculoRecape
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set cantNeumaticos
     *
     * @param float $cantNeumaticos
     *
     * @return VehiculoRecape
     */
    public function setCantNeumaticos($cantNeumaticos)
    {
        $this->cantNeumaticos = $cantNeumaticos;

        return $this;
    }

    /**
     * Get cantNeumaticos
     *
     * @return float
     */
    public function getCantNeumaticos()
    {
        return $this->cantNeumaticos;
    }

    /**
     * Set idPlanRecape
     *
     * @param \Geocuba\PortadoresBundle\Entity\PlanRecape $idPlanRecape
     *
     * @return VehiculoRecape
     */
    public function setIdPlanRecape(\Geocuba\PortadoresBundle\Entity\PlanRecape $idPlanRecape = null)
    {
        $this->idPlanRecape = $idPlanRecape;

        return $this;
    }

    /**
     * Get idPlanRecape
     *
     * @return \Geocuba\PortadoresBundle\Entity\PlanRecape
     */
    public function getIdPlanRecape()
    {
        return $this->idPlanRecape;
    }

    /**
     * Set idVehiculo
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $idVehiculo
     *
     * @return VehiculoRecape
     */
    public function setIdVehiculo(\Geocuba\PortadoresBundle\Entity\Vehiculo $idVehiculo = null)
    {
        $this->idVehiculo = $idVehiculo;

        return $this;
    }

    /**
     * Get idVehiculo
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getIdVehiculo()
    {
        return $this->idVehiculo;
    }
}
