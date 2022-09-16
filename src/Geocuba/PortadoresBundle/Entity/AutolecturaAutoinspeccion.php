<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AutolecturaAutoinspeccion
 *
 * @ORM\Table(name="datos.autolectura_autoinspeccion")
 * @ORM\Entity
 */
class AutolecturaAutoinspeccion
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
     * @ORM\Column(name="dia", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $dia;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="durante_horario_pico", type="string", length=500, nullable=true)
     */
    private $duranteHorarioPico;

    /**
     * @var string
     *
     * @ORM\Column(name="fuera_horario_pico", type="string", length=500, nullable=true)
     */
    private $fueraHorarioPico;

    /**
     * @var string
     *
     * @ORM\Column(name="responsable", type="string", length=500, nullable=true)
     */
    private $responsable;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;



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
     * Set dia
     *
     * @param string $dia
     *
     * @return AutolecturaAutoinspeccion
     */
    public function setDia($dia)
    {
        $this->dia = $dia;

        return $this;
    }

    /**
     * Get dia
     *
     * @return string
     */
    public function getDia()
    {
        return $this->dia;
    }

    /**
     * Set mes
     *
     * @param string $mes
     *
     * @return AutolecturaAutoinspeccion
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
     * Set duranteHorarioPico
     *
     * @param string $duranteHorarioPico
     *
     * @return AutolecturaAutoinspeccion
     */
    public function setDuranteHorarioPico($duranteHorarioPico)
    {
        $this->duranteHorarioPico = $duranteHorarioPico;

        return $this;
    }

    /**
     * Get duranteHorarioPico
     *
     * @return string
     */
    public function getDuranteHorarioPico()
    {
        return $this->duranteHorarioPico;
    }

    /**
     * Set fueraHorarioPico
     *
     * @param string $fueraHorarioPico
     *
     * @return AutolecturaAutoinspeccion
     */
    public function setFueraHorarioPico($fueraHorarioPico)
    {
        $this->fueraHorarioPico = $fueraHorarioPico;

        return $this;
    }

    /**
     * Get fueraHorarioPico
     *
     * @return string
     */
    public function getFueraHorarioPico()
    {
        return $this->fueraHorarioPico;
    }

    /**
     * Set responsable
     *
     * @param string $responsable
     *
     * @return AutolecturaAutoinspeccion
     */
    public function setResponsable($responsable)
    {
        $this->responsable = $responsable;

        return $this;
    }

    /**
     * Get responsable
     *
     * @return string
     */
    public function getResponsable()
    {
        return $this->responsable;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return AutolecturaAutoinspeccion
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
}
