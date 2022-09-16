<?php

namespace Geocuba\PortadoresBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * RegistroOperacionCaldera
 *
 * @ORM\Table(name="datos.registro_operaciones_calderas")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\RegistroOperacionCalderaRepository")
 */
class RegistroOperacionCaldera extends RegistroOperacion
{
    /**
     * @var DateTime
     *
     * @ORM\Column(name="hora_arranque_recirculacion", type="time", nullable=false)
     */
    private $horaArranqueRecirculacion;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="hora_parada_recirculacion", type="time", nullable=false)
     */
    private $horaParadaRecirculacion;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_real_recirculacion", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $consumoRealRecirculacion;

    /**
     * @var string
     *
     * @ORM\Column(name="consumo_normado_recirculacion", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $consumoNormadoRecirculacion;

    /**
     * @var RegistroOperacion
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="RegistroOperacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nivel_act_real_recirculacion", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $nivelActRealRecirculacion = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="indice_normado_recirculacion", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $indiceNormadoRecirculacion = '0';



    /**
     * Set horaArranqueRecirculacion.
     *
     * @param DateTime $horaArranqueRecirculacion
     *
     * @return RegistroOperacionCaldera
     */
    public function setHoraArranqueRecirculacion($horaArranqueRecirculacion): RegistroOperacionCaldera
    {
        $this->horaArranqueRecirculacion = $horaArranqueRecirculacion;
    
        return $this;
    }

    /**
     * Get horaArranqueRecirculacion.
     *
     * @return DateTime
     */
    public function getHoraArranqueRecirculacion(): DateTime
    {
        return $this->horaArranqueRecirculacion;
    }

    /**
     * Set horaParadaRecirculacion.
     *
     * @param DateTime $horaParadaRecirculacion
     *
     * @return RegistroOperacionCaldera
     */
    public function setHoraParadaRecirculacion($horaParadaRecirculacion): RegistroOperacionCaldera
    {
        $this->horaParadaRecirculacion = $horaParadaRecirculacion;
    
        return $this;
    }

    /**
     * Get horaParadaRecirculacion.
     *
     * @return DateTime
     */
    public function getHoraParadaRecirculacion(): DateTime
    {
        return $this->horaParadaRecirculacion;
    }

    /**
     * Set consumoRealRecirculacion.
     *
     * @param string $consumoRealRecirculacion
     *
     * @return RegistroOperacionCaldera
     */
    public function setConsumoRealRecirculacion($consumoRealRecirculacion): RegistroOperacionCaldera
    {
        $this->consumoRealRecirculacion = $consumoRealRecirculacion;
    
        return $this;
    }

    /**
     * Get consumoRealRecirculacion.
     *
     * @return string
     */
    public function getConsumoRealRecirculacion(): string
    {
        return $this->consumoRealRecirculacion;
    }

    /**
     * Set consumoNormadoRecirculacion.
     *
     * @param string $consumoNormadoRecirculacion
     *
     * @return RegistroOperacionCaldera
     */
    public function setConsumoNormadoRecirculacion($consumoNormadoRecirculacion): RegistroOperacionCaldera
    {
        $this->consumoNormadoRecirculacion = $consumoNormadoRecirculacion;
    
        return $this;
    }

    /**
     * Get consumoNormadoRecirculacion.
     *
     * @return string
     */
    public function getConsumoNormadoRecirculacion(): string
    {
        return $this->consumoNormadoRecirculacion;
    }

    public function getTiempoTrabajadoRecirc($horas = true)
    {
        $minutos = ($this->horaParadaRecirculacion->getTimestamp() - $this->horaArranqueRecirculacion->getTimestamp()) / 60;

        if ($horas) return (int)($minutos / 60) . ':' . $minutos % 60;

        return $minutos;
    }

    /**
     * Set nivelActRealRecirculacion.
     *
     * @param string $nivelActRealRecirculacion
     *
     * @return RegistroOperacionCaldera
     */
    public function setNivelActRealRecirculacion($nivelActRealRecirculacion): RegistroOperacionCaldera
    {
        $this->nivelActRealRecirculacion = $nivelActRealRecirculacion;

        return $this;
    }

    /**
     * Get nivelActRealRecirculacion.
     *
     * @return string
     */
    public function getNivelActRealRecirculacion(): string
    {
        return $this->nivelActRealRecirculacion;
    }

    /**
     * Set indiceNormadoRecirculacion.
     *
     * @param string $indiceNormadoRecirculacion
     *
     * @return RegistroOperacionCaldera
     */
    public function setIndiceNormadoRecirculacion($indiceNormadoRecirculacion): RegistroOperacionCaldera
    {
        $this->indiceNormadoRecirculacion = $indiceNormadoRecirculacion;

        return $this;
    }

    /**
     * Get indiceNormadoRecirculacion.
     *
     * @return string
     */
    public function getIndiceNormadoRecirculacion(): string
    {
        return $this->indiceNormadoRecirculacion;
    }
}
