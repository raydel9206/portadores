<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarifa
 *
 * @ORM\Table(name="nomencladores.tarifa")
 * @ORM\Entity
 */
class Tarifa
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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(name="horario_dia", type="string", length=500, nullable=true)
     */
    private $horarioDia;

    /**
     * @var string
     *
     * @ORM\Column(name="horario_pico", type="string", length=500, nullable=true)
     */
    private $horarioPico;

    /**
     * @var string
     *
     * @ORM\Column(name="horario_madrugada", type="string", length=500, nullable=true)
     */
    private $horarioMadrugada;

    /**
     * @var string
     *
     * @ORM\Column(name="cv_pico", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $cvPico;

    /**
     * @var string
     *
     * @ORM\Column(name="cv_madrugada", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $cvMadrugada;

    /**
     * @var string
     *
     * @ORM\Column(name="cv_dia", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $cvDia;

    /**
     * @var string
     *
     * @ORM\Column(name="cf", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $cf;

    /**
     * @var integer
     *
     * @ORM\Column(name="precio_x_kW", type="integer", nullable=true)
     */
    private $precioXKw;

    /**
     * @var string
     *
     * @ORM\Column(name="grupo", type="string", nullable=true)
     */
    private $grupo;

    /**
     * @var string
     *
     * @ORM\Column(name="cv_cualquierhorario", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $cvCualquierhorario;



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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Tarifa
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Tarifa
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
     * Set horarioDia
     *
     * @param string $horarioDia
     *
     * @return Tarifa
     */
    public function setHorarioDia($horarioDia)
    {
        $this->horarioDia = $horarioDia;

        return $this;
    }

    /**
     * Get horarioDia
     *
     * @return string
     */
    public function getHorarioDia()
    {
        return $this->horarioDia;
    }

    /**
     * Set horarioPico
     *
     * @param string $horarioPico
     *
     * @return Tarifa
     */
    public function setHorarioPico($horarioPico)
    {
        $this->horarioPico = $horarioPico;

        return $this;
    }

    /**
     * Get horarioPico
     *
     * @return string
     */
    public function getHorarioPico()
    {
        return $this->horarioPico;
    }

    /**
     * Set horarioMadrugada
     *
     * @param string $horarioMadrugada
     *
     * @return Tarifa
     */
    public function setHorarioMadrugada($horarioMadrugada)
    {
        $this->horarioMadrugada = $horarioMadrugada;

        return $this;
    }

    /**
     * Get horarioMadrugada
     *
     * @return string
     */
    public function getHorarioMadrugada()
    {
        return $this->horarioMadrugada;
    }

    /**
     * Set cvPico
     *
     * @param string $cvPico
     *
     * @return Tarifa
     */
    public function setCvPico($cvPico)
    {
        $this->cvPico = $cvPico;

        return $this;
    }

    /**
     * Get cvPico
     *
     * @return string
     */
    public function getCvPico()
    {
        return $this->cvPico;
    }

    /**
     * Set cvMadrugada
     *
     * @param string $cvMadrugada
     *
     * @return Tarifa
     */
    public function setCvMadrugada($cvMadrugada)
    {
        $this->cvMadrugada = $cvMadrugada;

        return $this;
    }

    /**
     * Get cvMadrugada
     *
     * @return string
     */
    public function getCvMadrugada()
    {
        return $this->cvMadrugada;
    }

    /**
     * Set cvDia
     *
     * @param string $cvDia
     *
     * @return Tarifa
     */
    public function setCvDia($cvDia)
    {
        $this->cvDia = $cvDia;

        return $this;
    }

    /**
     * Get cvDia
     *
     * @return string
     */
    public function getCvDia()
    {
        return $this->cvDia;
    }

    /**
     * Set cf
     *
     * @param string $cf
     *
     * @return Tarifa
     */
    public function setCf($cf)
    {
        $this->cf = $cf;

        return $this;
    }

    /**
     * Get cf
     *
     * @return string
     */
    public function getCf()
    {
        return $this->cf;
    }

    /**
     * Set precioXKw
     *
     * @param integer $precioXKw
     *
     * @return Tarifa
     */
    public function setPrecioXKw($precioXKw)
    {
        $this->precioXKw = $precioXKw;

        return $this;
    }

    /**
     * Get precioXKw
     *
     * @return integer
     */
    public function getPrecioXKw()
    {
        return $this->precioXKw;
    }

    /**
     * Set grupo
     *
     * @param string $grupo
     *
     * @return Tarifa
     */
    public function setGrupo($grupo)
    {
        $this->grupo = $grupo;

        return $this;
    }

    /**
     * Get grupo
     *
     * @return string
     */
    public function getGrupo()
    {
        return $this->grupo;
    }

    /**
     * Set cvCualquierhorario
     *
     * @param string $cvCualquierhorario
     *
     * @return Tarifa
     */
    public function setCvCualquierhorario($cvCualquierhorario)
    {
        $this->cvCualquierhorario = $cvCualquierhorario;

        return $this;
    }

    /**
     * Get cvCualquierhorario
     *
     * @return string
     */
    public function getCvCualquierhorario()
    {
        return $this->cvCualquierhorario;
    }
}
