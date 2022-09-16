<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DesgloseServicios
 *
 * @ORM\Table(name="datos.desglose_servicios", indexes={@ORM\Index(name="IDX_13958E6D6153BA27", columns={"idservicio"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\DesgloseServicioRepository")
 */
class DesgloseServicios
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="plan_total", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $planTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="plan_pico", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $planPico;

    /**
     * @var integer
     *
     * @ORM\Column(name="mes", type="integer", nullable=true)
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

    /**
     * @var string
     *
     * @ORM\Column(name="perdidasT", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $perdidast;

    /**
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idservicio", referencedColumnName="id")
     * })
     */
    private $idservicio;



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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return DesgloseServicios
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
     * Set planTotal
     *
     * @param string $planTotal
     *
     * @return DesgloseServicios
     */
    public function setPlanTotal($planTotal)
    {
        $this->planTotal = $planTotal;

        return $this;
    }

    /**
     * Get planTotal
     *
     * @return string
     */
    public function getPlanTotal()
    {
        return $this->planTotal;
    }

    /**
     * Set planPico
     *
     * @param string $planPico
     *
     * @return DesgloseServicios
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
     * Set mes
     *
     * @param integer $mes
     *
     * @return DesgloseServicios
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
     * Set anno
     *
     * @param integer $anno
     *
     * @return DesgloseServicios
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
     * Set perdidast
     *
     * @param string $perdidast
     *
     * @return DesgloseServicios
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
     * Set idservicio
     *
     * @param \Geocuba\PortadoresBundle\Entity\Servicio $idservicio
     *
     * @return DesgloseServicios
     */
    public function setIdservicio(\Geocuba\PortadoresBundle\Entity\Servicio $idservicio = null)
    {
        $this->idservicio = $idservicio;

        return $this;
    }

    /**
     * Get idservicio
     *
     * @return \Geocuba\PortadoresBundle\Entity\Servicio
     */
    public function getIdservicio()
    {
        return $this->idservicio;
    }
}
