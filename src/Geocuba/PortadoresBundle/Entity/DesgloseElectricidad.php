<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DesgloseElectricidad
 *
 * @ORM\Table(name="datos.desglose_electricidad", indexes={@ORM\Index(name="IDX_84C2CD6B8DA4C929", columns={"iddesglose_servicios"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\DesgloseElectricidadRepository")
 */
class DesgloseElectricidad
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
     * @ORM\Column(name="plan_pico", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $planPico;

    /**
     * @var string
     *
     * @ORM\Column(name="plan_diario", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $planDiario;

    /**
     * @var string
     *
     * @ORM\Column(name="anno", type="string", nullable=true)
     */
    private $anno;

    /**
     * @var integer
     *
     * @ORM\Column(name="mes", type="integer", nullable=true)
     */
    private $mes;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_desglose", type="date", nullable=true)
     */
    private $fechaDesglose;

    /**
     * @var string
     *
     * @ORM\Column(name="perdidast", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $perdidast;

    /**
     * @var \DesgloseServicios
     *
     * @ORM\ManyToOne(targetEntity="DesgloseServicios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="iddesglose_servicios", referencedColumnName="id")
     * })
     */
    private $iddesgloseServicios;



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
     * Set planPico
     *
     * @param string $planPico
     *
     * @return DesgloseElectricidad
     */
    public function setPlanPico($planPico)
    {
        $this->planPico = $planPico;

        return $this;
    }

    /**
     * Get planPico
     *
     * @return string
     */
    public function getPlanPico()
    {
        return $this->planPico;
    }

    /**
     * Set planDiario
     *
     * @param string $planDiario
     *
     * @return DesgloseElectricidad
     */
    public function setPlanDiario($planDiario)
    {
        $this->planDiario = $planDiario;

        return $this;
    }

    /**
     * Get planDiario
     *
     * @return string
     */
    public function getPlanDiario()
    {
        return $this->planDiario;
    }

    /**
     * Set anno
     *
     * @param string $anno
     *
     * @return DesgloseElectricidad
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return string
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
     * @return DesgloseElectricidad
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
     * Set fechaDesglose
     *
     * @param \DateTime $fechaDesglose
     *
     * @return DesgloseElectricidad
     */
    public function setFechaDesglose($fechaDesglose)
    {
        $this->fechaDesglose = $fechaDesglose;

        return $this;
    }

    /**
     * Get fechaDesglose
     *
     * @return \DateTime
     */
    public function getFechaDesglose()
    {
        return $this->fechaDesglose;
    }

    /**
     * Set perdidast
     *
     * @param string $perdidast
     *
     * @return DesgloseElectricidad
     */
    public function setPerdidast($perdidast)
    {
        $this->perdidast = $perdidast;

        return $this;
    }

    /**
     * Get perdidast
     *
     * @return string
     */
        public function getPerdidast()
    {
        return $this->perdidast;
    }

    /**
     * Set iddesgloseServicios
     *
     * @param \Geocuba\PortadoresBundle\Entity\DesgloseServicios $iddesgloseServicios
     *
     * @return DesgloseElectricidad
     */
    public function setIddesgloseServicios(\Geocuba\PortadoresBundle\Entity\DesgloseServicios $iddesgloseServicios = null)
    {
        $this->iddesgloseServicios = $iddesgloseServicios;

        return $this;
    }

    /**
     * Get iddesgloseServicios
     *
     * @return \Geocuba\PortadoresBundle\Entity\DesgloseServicios
     */
    public function getIddesgloseServicios()
    {
        return $this->iddesgloseServicios;
    }
}
