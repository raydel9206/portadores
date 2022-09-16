<?php

namespace Geocuba\PortadoresBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * RegistroOperacion
 *
 * @ORM\Table(name="datos.registro_operaciones")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\RegistroOperacionRepository")
 *
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"equipo_tecnologico" = "RegistroOperacion", "montacarga" = "RegistroOperacionMontacarga", "caldera" = "RegistroOperacionCaldera"})
 */
class RegistroOperacion
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
     * @var DateTime
     *
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_real", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $consumoReal;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_normado", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $consumoNormado;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="hora_arranque", type="time", nullable=false)
     */
    private $horaArranque;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="hora_parada", type="time", nullable=false)
     */
    private $horaParada;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_inicial", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $combustibleInicial;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_abastecido", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $combustibleAbastecido;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_final", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $combustibleFinal;

    /**
     * @var EquipoTecnologico
     *
     * @ORM\ManyToOne(targetEntity="EquipoTecnologico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="equipo_tecnologico_id", referencedColumnName="id")
     * })
     */
    private $equipoTecnologico;

    /**
     * @var Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="actividad_id", referencedColumnName="id")
     * })
     */
    private $actividad;

    /**
     * @var string
     *
     * @ORM\Column(name="nivel_act_real", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $nivelActividadReal;

    /**
     * @var string
     *
     * @ORM\Column(name="indice_normado", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $indiceNormado;



    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set fecha.
     *
     * @param DateTime $fecha
     *
     * @return RegistroOperacion
     */
    public function setFecha($fecha): RegistroOperacion
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha.
     *
     * @return DateTime
     */
    public function getFecha(): DateTime
    {
        return $this->fecha;
    }

    /**
     * Set consumoReal.
     *
     * @param string $consumoReal
     *
     * @return RegistroOperacion
     */
    public function setConsumoReal($consumoReal): RegistroOperacion
    {
        $this->consumoReal = $consumoReal;
    
        return $this;
    }

    /**
     * Get consumoReal.
     *
     * @return string
     */
    public function getConsumoReal(): string
    {
        return $this->consumoReal;
    }

    /**
     * Set consumoNormado.
     *
     * @param string $consumoNormado
     *
     * @return RegistroOperacion
     */
    public function setConsumoNormado($consumoNormado): RegistroOperacion
    {
        $this->consumoNormado = $consumoNormado;
    
        return $this;
    }

    /**
     * Get consumoNormado.
     *
     * @return string
     */
    public function getConsumoNormado(): string
    {
        return $this->consumoNormado;
    }

    /**
     * Set horaArranque.
     *
     * @param DateTime $horaArranque
     *
     * @return RegistroOperacion
     */
    public function setHoraArranque($horaArranque): RegistroOperacion
    {
        $this->horaArranque = $horaArranque;

        return $this;
    }

    /**
     * Get horaArranque.
     *
     * @return DateTime
     */
    public function getHoraArranque(): DateTime
    {
        return $this->horaArranque;
    }

    /**
     * Set horaParada.
     *
     * @param DateTime $horaParada
     *
     * @return RegistroOperacion
     */
    public function setHoraParada($horaParada): RegistroOperacion
    {
        $this->horaParada = $horaParada;

        return $this;
    }

    /**
     * Get horaParada.
     *
     * @return DateTime
     */
    public function getHoraParada(): DateTime
    {
        return $this->horaParada;
    }

    /**
     * Set combustibleInicial.
     *
     * @param string $combustibleInicial
     *
     * @return RegistroOperacion
     */
    public function setCombustibleInicial($combustibleInicial): RegistroOperacion
    {
        $this->combustibleInicial = $combustibleInicial;

        return $this;
    }

    /**
     * Get combustibleInicial.
     *
     * @return string
     */
    public function getCombustibleInicial(): string
    {
        return $this->combustibleInicial;
    }

    /**
     * Set combustibleAbastecido.
     *
     * @param string $combustibleAbastecido
     *
     * @return RegistroOperacion
     */
    public function setCombustibleAbastecido($combustibleAbastecido): RegistroOperacion
    {
        $this->combustibleAbastecido = $combustibleAbastecido;

        return $this;
    }

    /**
     * Get combustibleAbastecido.
     *
     * @return string
     */
    public function getCombustibleAbastecido(): string
    {
        return $this->combustibleAbastecido;
    }

    /**
     * Set combustibleFinal.
     *
     * @param string $combustibleFinal
     *
     * @return RegistroOperacion
     */
    public function setCombustibleFinal($combustibleFinal): RegistroOperacion
    {
        $this->combustibleFinal = $combustibleFinal;

        return $this;
    }

    /**
     * Get combustibleFinal.
     *
     * @return string
     */
    public function getCombustibleFinal(): string
    {
        return $this->combustibleFinal;
    }

    /**
     * Set equipoTecnologico.
     *
     * @param EquipoTecnologico $equipoTecnologico
     * @return RegistroOperacion
     */
    public function setEquipoTecnologico(EquipoTecnologico $equipoTecnologico): RegistroOperacion
    {
        $this->equipoTecnologico = $equipoTecnologico;

        return $this;
    }

    /**
     * Get equipoTecnologico.
     *
     * @return EquipoTecnologico
     */
    public function getEquipoTecnologico(): EquipoTecnologico
    {
        return $this->equipoTecnologico;
    }

    /**
     * Set actividad.
     *
     * @param Actividad $actividad|null
     * @return RegistroOperacion
     */
    public function setActividad(Actividad $actividad): RegistroOperacion
    {
        $this->actividad = $actividad;

        return $this;
    }

    /**
     * Get actividad.
     *
     * @return Actividad|null
     */
    public function getActividad(): ?Actividad
    {
        return $this->actividad;
    }

    /**
     * @param bool $horas
     *
     * @return mixed Si $horas === true retorna string 'hora:min'. Si $horas === false retorna int $minutos.
     */
    public function getTiempoTrabajado($horas = true)
    {
        $minutos = ($this->horaParada->getTimestamp() - $this->horaArranque->getTimestamp()) / 60;

        if ($horas) return (int)($minutos / 60) . ':' . $minutos % 60;

        return $minutos;
    }

    /**
     * Set nivelActividadReal.
     *
     * @param $nivelActividadReal
     * @return RegistroOperacion
     */
    public function setNivelActividadReal($nivelActividadReal): RegistroOperacion
    {
        $this->nivelActividadReal = $nivelActividadReal;

        return $this;
    }

    /**
     * Get nivelActividadReal.
     *
     * @return string
     */
    public function getNivelActividadReal(): string
    {
        return $this->nivelActividadReal;
    }

    /**
     * Set indiceNormado.
     *
     * @param $indiceNormado
     * @return RegistroOperacion
     */
    public function setIndiceNormado($indiceNormado): RegistroOperacion
    {
        $this->indiceNormado = $indiceNormado;

        return $this;
    }

    /**
     * Get indiceNormado.
     *
     * @return string
     */
    public function getIndiceNormado(): string
    {
        return $this->indiceNormado;
    }
}
