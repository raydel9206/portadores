<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Anexo3TecnologicosDesglose
 *
 * @ORM\Table(name="datos.anexo_3_tecnologicos_desglose", indexes={@ORM\Index(name="IDX_FCB8DC4C71B41E51", columns={"anexo_3_tecnologicos_id"})})
 * @ORM\Entity
 */
class Anexo3TecnologicosDesglose
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
     * @var int
     *
     * @ORM\Column(name="dia", type="integer", nullable=false)
     */
    private $dia;

    /**
     * @var string
     *
     * @ORM\Column(name="indice_normado", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $indiceNormado;

    /**
     * @var string
     *
     * @ORM\Column(name="hora_inicio", type="string", nullable=false)
     */
    private $horaInicio;

    /**
     * @var string
     *
     * @ORM\Column(name="hora_parada", type="string", nullable=false)
     */
    private $horaParada;

    /**
     * @var string
     *
     * @ORM\Column(name="tiempo_empleado", type="string", nullable=false)
     */
    private $tiempoEmpleado;

    /**
     * @var string
     *
     * @ORM\Column(name="nivel_act_real", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $nivelActReal;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_debio_consumir", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $combustibleDebioConsumir;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_restante", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $combustibleRestante;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_real", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $combustibleReal;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_abastecido", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $combustibleAbastecido;

    /**
     * @var string
     *
     * @ORM\Column(name="indice_real", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $indiceReal;

    /**
     * @var string
     *
     * @ORM\Column(name="diferencia_real_plan", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $diferenciaRealPlan;

    /**
     * @var string
     *
     * @ORM\Column(name="porciento_desviacion", type="decimal", precision=5, scale=2, nullable=false)
     */
    private $porcientoDesviacion;

    /**
     * @var Anexo3Tecnologicos
     *
     * @ORM\ManyToOne(targetEntity="Anexo3Tecnologicos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="anexo_3_tecnologicos_id", referencedColumnName="id")
     * })
     */
    private $anexo3;

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
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set dia.
     *
     * @param int $dia
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setDia($dia): Anexo3TecnologicosDesglose
    {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia.
     *
     * @return int
     */
    public function getDia(): int
    {
        return $this->dia;
    }

    /**
     * Set indiceNormado.
     *
     * @param string $indiceNormado
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setIndiceNormado($indiceNormado): Anexo3TecnologicosDesglose
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

    /**
     * Set horaInicio.
     *
     * @param string $horaInicio
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setHoraInicio($horaInicio): Anexo3TecnologicosDesglose
    {
        $this->horaInicio = $horaInicio;

        return $this;
    }

    /**
     * Get horaInicio.
     *
     * @return string
     */
    public function getHoraInicio(): string
    {
        return $this->horaInicio;
    }

    /**
     * Set horaParada.
     *
     * @param string $horaParada
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setHoraParada($horaParada): Anexo3TecnologicosDesglose
    {
        $this->horaParada = $horaParada;

        return $this;
    }

    /**
     * Get horaParada.
     *
     * @return string
     */
    public function getHoraParada(): string
    {
        return $this->horaParada;
    }

    /**
     * Set tiempoEmpleado.
     *
     * @param string $tiempoEmpleado
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setTiempoEmpleado($tiempoEmpleado): Anexo3TecnologicosDesglose
    {
        $this->tiempoEmpleado = $tiempoEmpleado;

        return $this;
    }

    /**
     * Get tiempoEmpleado.
     *
     * @return string
     */
    public function getTiempoEmpleado(): string
    {
        return $this->tiempoEmpleado;
    }

    /**
     * Set nivelActReal.
     *
     * @param string $nivelActReal
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setNivelActReal($nivelActReal): Anexo3TecnologicosDesglose
    {
        $this->nivelActReal = $nivelActReal;

        return $this;
    }

    /**
     * Get nivelActReal.
     *
     * @return string
     */
    public function getNivelActReal(): string
    {
        return $this->nivelActReal;
    }

    /**
     * Set combustibleDebioConsumir.
     *
     * @param string $combustibleDebioConsumir
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setCombustibleDebioConsumir($combustibleDebioConsumir): Anexo3TecnologicosDesglose
    {
        $this->combustibleDebioConsumir = $combustibleDebioConsumir;

        return $this;
    }

    /**
     * Get combustibleDebioConsumir.
     *
     * @return string
     */
    public function getCombustibleDebioConsumir(): string
    {
        return $this->combustibleDebioConsumir;
    }

    /**
     * Set combustibleRestante.
     *
     * @param string $combustibleRestante
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setCombustibleRestante($combustibleRestante): Anexo3TecnologicosDesglose
    {
        $this->combustibleRestante = $combustibleRestante;

        return $this;
    }

    /**
     * Get combustibleRestante.
     *
     * @return string
     */
    public function getCombustibleRestante(): string
    {
        return $this->combustibleRestante;
    }

    /**
     * Set combustibleReal.
     *
     * @param string $combustibleReal
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setCombustibleReal($combustibleReal): Anexo3TecnologicosDesglose
    {
        $this->combustibleReal = $combustibleReal;

        return $this;
    }

    /**
     * Get combustibleReal.
     *
     * @return string
     */
    public function getCombustibleReal(): string
    {
        return $this->combustibleReal;
    }

    /**
     * Set combustibleAbastecido.
     *
     * @param string $combustibleAbastecido
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setCombustibleAbastecido($combustibleAbastecido): Anexo3TecnologicosDesglose
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
     * Set indiceReal.
     *
     * @param string $indiceReal
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setIndiceReal($indiceReal): Anexo3TecnologicosDesglose
    {
        $this->indiceReal = $indiceReal;

        return $this;
    }

    /**
     * Get indiceReal.
     *
     * @return string
     */
    public function getIndiceReal(): string
    {
        return $this->indiceReal;
    }

    /**
     * Set diferenciaRealPlan.
     *
     * @param string $diferenciaRealPlan
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setDiferenciaRealPlan($diferenciaRealPlan): Anexo3TecnologicosDesglose
    {
        $this->diferenciaRealPlan = $diferenciaRealPlan;

        return $this;
    }

    /**
     * Get diferenciaRealPlan.
     *
     * @return string
     */
    public function getDiferenciaRealPlan(): string
    {
        return $this->diferenciaRealPlan;
    }

    /**
     * Set porcientoDesviacion.
     *
     * @param string $porcientoDesviacion
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setPorcientoDesviacion($porcientoDesviacion): Anexo3TecnologicosDesglose
    {
        $this->porcientoDesviacion = $porcientoDesviacion;

        return $this;
    }

    /**
     * Get porcientoDesviacion.
     *
     * @return string
     */
    public function getPorcientoDesviacion(): string
    {
        return $this->porcientoDesviacion;
    }

    /**
     * Set anexo3Tecnologicos.
     *
     * @param Anexo3Tecnologicos $anexo3Tecnologicos
     *
     * @return Anexo3TecnologicosDesglose
     */
    public function setAnexo3(Anexo3Tecnologicos $anexo3Tecnologicos): Anexo3TecnologicosDesglose
    {
        $this->anexo3 = $anexo3Tecnologicos;
    
        return $this;
    }

    /**
     * Get anexo3Tecnologicos.
     *
     * @return Anexo3Tecnologicos
     */
    public function getAnexo3(): Anexo3Tecnologicos
    {
        return $this->anexo3;
    }

    /**
     * Set actividad.
     *
     * @param Actividad $actividad|null
     * @return Anexo3TecnologicosDesglose
     */
    public function setActividad(Actividad $actividad): Anexo3TecnologicosDesglose
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
}
