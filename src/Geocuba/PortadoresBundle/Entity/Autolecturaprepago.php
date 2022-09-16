<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Autolecturaprepago
 *
 * @ORM\Table(name="datos.autolecturaprepago", indexes={@ORM\Index(name="IDX_19A69AC4409D1C63", columns={"serviciosid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\AutolecturaPrepagoRepository")
 */
class Autolecturaprepago
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
     * @ORM\Column(name="fecha_lectura", type="date", nullable=true)
     */
    private $fechaLectura;

    /**
     * @var string
     *
     * @ORM\Column(name="lectura_dia", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lecturaDia;


    /**
     * @var string
     *
     * @ORM\Column(name="consumo_total_dia", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumoTotalDia;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_total_real", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumoTotalReal;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_total_porciento", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumoTotalPorciento;

    /**
     * @var string
     *
     * @ORM\Column(name="plan_diario", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $planDiario;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cambio_metro", type="boolean", nullable=true)
     */
    private $cambioMetro;

    /**
     * @var \Servicio
     *
     * @ORM\ManyToOne(targetEntity="Servicio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="serviciosid", referencedColumnName="id")
     * })
     */
    private $serviciosid;



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
     * Set fechaLectura
     *
     * @param \DateTime $fechaLectura
     *
     * @return Autolecturaprepago
     */
    public function setFechaLectura($fechaLectura)
    {
        $this->fechaLectura = $fechaLectura;

        return $this;
    }

    /**
     * Get fechaLectura
     *
     * @return \DateTime
     */
    public function getFechaLectura()
    {
        return $this->fechaLectura;
    }

    /**
     * Set lecturaDia
     *
     * @param string $lecturaDia
     *
     * @return Autolecturaprepago
     */
    public function setLecturaDia($lecturaDia)
    {
        $this->lecturaDia = $lecturaDia;

        return $this;
    }

    /**
     * Get lecturaDia
     *
     * @return string
     */
    public function getLecturaDia()
    {
        return $this->lecturaDia;
    }


    /**
     * Set consumoTotalDia
     *
     * @param string $consumoTotalDia
     *
     * @return Autolecturaprepago
     */
    public function setConsumoTotalDia($consumoTotalDia)
    {
        $this->consumoTotalDia = $consumoTotalDia;

        return $this;
    }

    /**
     * Get consumoTotalDia
     *
     * @return string
     */
    public function getConsumoTotalDia()
    {
        return $this->consumoTotalDia;
    }

    /**
     * Set consumoTotalReal
     *
     * @param string $consumoTotalReal
     *
     * @return Autolecturaprepago
     */
    public function setConsumoTotalReal($consumoTotalReal)
    {
        $this->consumoTotalReal = $consumoTotalReal;

        return $this;
    }

    /**
     * Get consumoTotalReal
     *
     * @return string
     */
    public function getConsumoTotalReal()
    {
        return $this->consumoTotalReal;
    }

    /**
     * Set consumoTotalPorciento
     *
     * @param string $consumoTotalPorciento
     *
     * @return Autolecturaprepago
     */
    public function setConsumoTotalPorciento($consumoTotalPorciento)
    {
        $this->consumoTotalPorciento = $consumoTotalPorciento;

        return $this;
    }

    /**
     * Get consumoTotalPorciento
     *
     * @return string
     */
    public function getConsumoTotalPorciento()
    {
        return $this->consumoTotalPorciento;
    }

    /**
     * Set planDiario
     *
     * @param string $planDiario
     *
     * @return Autolecturaprepago
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
     * Set cambioMetro
     *
     * @param boolean $cambioMetro
     *
     * @return Autolecturaprepago
     */
    public function setCambioMetro($cambioMetro)
    {
        $this->cambioMetro = $cambioMetro;

        return $this;
    }

    /**
     * Get cambioMetro
     *
     * @return boolean
     */
    public function getCambioMetro()
    {
        return $this->cambioMetro;
    }

    /**
     * Set serviciosid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Servicio $serviciosid
     *
     * @return Autolecturaprepago
     */
    public function setServiciosid(\Geocuba\PortadoresBundle\Entity\Servicio $serviciosid = null)
    {
        $this->serviciosid = $serviciosid;

        return $this;
    }

    /**
     * Get serviciosid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Servicios
     */
    public function getServiciosid()
    {
        return $this->serviciosid;
    }
}
