<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AutolecturaTresescalas
 *
 * @ORM\Table(name="datos.autolectura_tresescalas", indexes={@ORM\Index(name="IDX_9BB01329409D1C63", columns={"serviciosid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\AutolecturaTresescalasRepository")
 */
class AutolecturaTresescalas
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
     * @ORM\Column(name="lectura_pico", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lecturaPico;

    /**
     * @var string
     *
     * @ORM\Column(name="lectura_mad", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lecturaMad;

    /**
     * @var string
     *
     * @ORM\Column(name="lectura_reactivo", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lecturaReactivo;

    /**
     * @var string
     *
     * @ORM\Column(name="lectura_maxdem_mad", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lecturaMaxdemMad;

    /**
     * @var string
     *
     * @ORM\Column(name="lectura_maxdem_pico", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lecturaMaxdemPico;

    /**
     * @var string
     *
     * @ORM\Column(name="lectura_maxdem_dia", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lecturaMaxdemDia;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_total_mad", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumoTotalMad;

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
     * @ORM\Column(name="consumo_total_plan", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumoTotalPlan;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_pico_plan", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumoPicoPlan;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_pico_real", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumoPicoReal;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_pico_porciento", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumoPicoPorciento;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", nullable=true)
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $consumo;

    /**
     * @var string
     *
     * @ORM\Column(name="anno", type="string", nullable=true)
     */
    private $anno;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cambio_metro", type="boolean", nullable=true)
     */
    private $cambioMetro;

	/**
     * @var boolean
     *
     * @ORM\Column(name="ultima", type="boolean", nullable=true)
     */
    private $ultima = true;

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
     * @return AutolecturaTresescalas
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
     * @return AutolecturaTresescalas
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
     * Set lecturaPico
     *
     * @param string $lecturaPico
     *
     * @return AutolecturaTresescalas
     */
    public function setLecturaPico($lecturaPico)
    {
        $this->lecturaPico = $lecturaPico;

        return $this;
    }

    /**
     * Get lecturaPico
     *
     * @return string
     */
    public function getLecturaPico()
    {
        return $this->lecturaPico;
    }

    /**
     * Set lecturaMad
     *
     * @param string $lecturaMad
     *
     * @return AutolecturaTresescalas
     */
    public function setLecturaMad($lecturaMad)
    {
        $this->lecturaMad = $lecturaMad;

        return $this;
    }

    /**
     * Get lecturaMad
     *
     * @return string
     */
    public function getLecturaMad()
    {
        return $this->lecturaMad;
    }

    /**
     * Set lecturaReactivo
     *
     * @param string $lecturaReactivo
     *
     * @return AutolecturaTresescalas
     */
    public function setLecturaReactivo($lecturaReactivo)
    {
        $this->lecturaReactivo = $lecturaReactivo;

        return $this;
    }

    /**
     * Get lecturaReactivo
     *
     * @return string
     */
    public function getLecturaReactivo()
    {
        return $this->lecturaReactivo;
    }

    /**
     * Set lecturaMaxdemMad
     *
     * @param string $lecturaMaxdemMad
     *
     * @return AutolecturaTresescalas
     */
    public function setLecturaMaxdemMad($lecturaMaxdemMad)
    {
        $this->lecturaMaxdemMad = $lecturaMaxdemMad;

        return $this;
    }

    /**
     * Get lecturaMaxdemMad
     *
     * @return string
     */
    public function getLecturaMaxdemMad()
    {
        return $this->lecturaMaxdemMad;
    }

    /**
     * Set lecturaMaxdemPico
     *
     * @param string $lecturaMaxdemPico
     *
     * @return AutolecturaTresescalas
     */
    public function setLecturaMaxdemPico($lecturaMaxdemPico)
    {
        $this->lecturaMaxdemPico = $lecturaMaxdemPico;

        return $this;
    }

    /**
     * Get lecturaMaxdemPico
     *
     * @return string
     */
    public function getLecturaMaxdemPico()
    {
        return $this->lecturaMaxdemPico;
    }

    /**
     * Set lecturaMaxdemDia
     *
     * @param string $lecturaMaxdemDia
     *
     * @return AutolecturaTresescalas
     */
    public function setLecturaMaxdemDia($lecturaMaxdemDia)
    {
        $this->lecturaMaxdemDia = $lecturaMaxdemDia;

        return $this;
    }

    /**
     * Get lecturaMaxdemDia
     *
     * @return string
     */
    public function getLecturaMaxdemDia()
    {
        return $this->lecturaMaxdemDia;
    }

    /**
     * Set consumoTotalMad
     *
     * @param string $consumoTotalMad
     *
     * @return AutolecturaTresescalas
     */
    public function setConsumoTotalMad($consumoTotalMad)
    {
        $this->consumoTotalMad = $consumoTotalMad;

        return $this;
    }

    /**
     * Get consumoTotalMad
     *
     * @return string
     */
    public function getConsumoTotalMad()
    {
        return $this->consumoTotalMad;
    }

    /**
     * Set consumoTotalDia
     *
     * @param string $consumoTotalDia
     *
     * @return AutolecturaTresescalas
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
     * @return AutolecturaTresescalas
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
     * @return AutolecturaTresescalas
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
     * Set consumoTotalPlan
     *
     * @param string $consumoTotalPlan
     *
     * @return AutolecturaTresescalas
     */
    public function setConsumoTotalPlan($consumoTotalPlan)
    {
        $this->consumoTotalPlan = $consumoTotalPlan;

        return $this;
    }

    /**
     * Get consumoTotalPlan
     *
     * @return string
     */
    public function getConsumoTotalPlan()
    {
        return $this->consumoTotalPlan;
    }

    /**
     * Set consumoPicoPlan
     *
     * @param string $consumoPicoPlan
     *
     * @return AutolecturaTresescalas
     */
    public function setConsumoPicoPlan($consumoPicoPlan)
    {
        $this->consumoPicoPlan = $consumoPicoPlan;

        return $this;
    }

    /**
     * Get consumoPicoPlan
     *
     * @return string
     */
    public function getConsumoPicoPlan()
    {
        return $this->consumoPicoPlan;
    }

    /**
     * Set consumoPicoReal
     *
     * @param string $consumoPicoReal
     *
     * @return AutolecturaTresescalas
     */
    public function setConsumoPicoReal($consumoPicoReal)
    {
        $this->consumoPicoReal = $consumoPicoReal;

        return $this;
    }

    /**
     * Get consumoPicoReal
     *
     * @return string
     */
    public function getConsumoPicoReal()
    {
        return $this->consumoPicoReal;
    }

    /**
     * Set consumoPicoPorciento
     *
     * @param string $consumoPicoPorciento
     *
     * @return AutolecturaTresescalas
     */
    public function setConsumoPicoPorciento($consumoPicoPorciento)
    {
        $this->consumoPicoPorciento = $consumoPicoPorciento;

        return $this;
    }

    /**
     * Get consumoPicoPorciento
     *
     * @return string
     */
    public function getConsumoPicoPorciento()
    {
        return $this->consumoPicoPorciento;
    }

    /**
     * Set mes
     *
     * @param string $mes
     *
     * @return AutolecturaTresescalas
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
     * Set consumo
     *
     * @param string $consumo
     *
     * @return AutolecturaTresescalas
     */
    public function setConsumo($consumo)
    {
        $this->consumo = $consumo;

        return $this;
    }

    /**
     * Get consumo
     *
     * @return string
     */
    public function getConsumo()
    {
        return $this->consumo;
    }

    /**
     * Set anno
     *
     * @param string $anno
     *
     * @return AutolecturaTresescalas
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
     * Set cambioMetro
     *
     * @param boolean $cambioMetro
     *
     * @return AutolecturaTresescalas
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
     * Set ultima
     *
     * @param boolean $ultima
     *
     * @return AutolecturaTresescalas
     */
    public function setUltima($ultima)
    {
        $this->ultima = $ultima;

        return $this;
    }

    /**
     * Get ultima
     *
     * @return boolean
     */
    public function getUltima()
    {
        return $this->ultima;
    }

    /**
     * Set serviciosid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Servicio $serviciosid
     *
     * @return AutolecturaTresescalas
     */
    public function setServiciosid(\Geocuba\PortadoresBundle\Entity\Servicio $serviciosid = null)
    {
        $this->serviciosid = $serviciosid;

        return $this;
    }

    /**
     * Get serviciosid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Servicio
     */
    public function getServiciosid()
    {
        return $this->serviciosid;
    }
}
