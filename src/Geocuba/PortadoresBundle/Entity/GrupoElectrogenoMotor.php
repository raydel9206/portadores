<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GrupoElectrogenoMotor
 *
 * @ORM\Table(name="nomencladores.grupos_electrogenos_motores", indexes={@ORM\Index(name="IDX_746ABF6FC3A9576E", columns={"modelo_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\GrupoElectrogenoMotorRepository")
 */
class GrupoElectrogenoMotor
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
     * @ORM\Column(name="hp", type="integer", nullable=false)
     */
    private $hp;

    /**
     * @var int
     *
     * @ORM\Column(name="rpm", type="integer", nullable=false)
     */
    private $rpm;

    /**
     * @var string
     *
     * @ORM\Column(name="no_serie", type="string", nullable=false)
     */
    private $noSerie;

    /**
     * @var ModeloTecnologico
     *
     * @ORM\ManyToOne(targetEntity="ModeloTecnologico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modelo_id", referencedColumnName="id")
     * })
     */
    private $modelo;



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
     * Set hp.
     *
     * @param int $hp
     *
     * @return GrupoElectrogenoMotor
     */
    public function setHp($hp): GrupoElectrogenoMotor
    {
        $this->hp = $hp;
    
        return $this;
    }

    /**
     * Get hp.
     *
     * @return int
     */
    public function getHp(): int
    {
        return $this->hp;
    }

    /**
     * Set rpm.
     *
     * @param int $rpm
     *
     * @return GrupoElectrogenoMotor
     */
    public function setRpm($rpm): GrupoElectrogenoMotor
    {
        $this->rpm = $rpm;
    
        return $this;
    }

    /**
     * Get rpm.
     *
     * @return int
     */
    public function getRpm(): int
    {
        return $this->rpm;
    }

    /**
     * Set noSerie.
     *
     * @param string $noSerie
     *
     * @return GrupoElectrogenoMotor
     */
    public function setNoSerie($noSerie): GrupoElectrogenoMotor
    {
        $this->noSerie = $noSerie;
    
        return $this;
    }

    /**
     * Get noSerie.
     *
     * @return string
     */
    public function getNoSerie(): string
    {
        return $this->noSerie;
    }

    /**
     * Set modelo.
     *
     * @param ModeloTecnologico $modelo
     *
     * @return GrupoElectrogenoMotor
     */
    public function setModelo(ModeloTecnologico $modelo = null): GrupoElectrogenoMotor
    {
        $this->modelo = $modelo;
    
        return $this;
    }

    /**
     * Get modelo.
     *
     * @return ModeloTecnologico
     */
    public function getModelo(): ModeloTecnologico
    {
        return $this->modelo;
    }
}
