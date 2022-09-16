<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedicionAfore
 *
 * @ORM\Table(name="nomencladores.mediciones_afore", indexes={@ORM\Index(name="IDX_C5873E37E83ABB22", columns={"tanque_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\MedicionAforeRepository")
 */
class MedicionAfore
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
     * @var string
     *
     * @ORM\Column(name="nivel", type="decimal", precision=10, scale=4, nullable=false)
     */
    private $nivel;

    /**
     * @var string
     *
     * @ORM\Column(name="existencia", type="decimal", precision=16, scale=4, nullable=false)
     */
    private $existencia;

    /**
     * @var Tanque
     *
     * @ORM\ManyToOne(targetEntity="Tanque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tanque_id", referencedColumnName="id")
     * })
     */
    private $tanque;

    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nivelCm.
     *
     * @param string $nivel
     *
     * @return MedicionAfore
     */
    public function setNivel($nivel): MedicionAfore
    {
        $this->nivel = $nivel;
    
        return $this;
    }

    /**
     * Get nivel.
     *
     * @return string
     */
    public function getNivel(): string
    {
        return $this->nivel;
    }

    /**
     * Set existencia.
     *
     * @param string $existencia
     *
     * @return MedicionAfore
     */
    public function setExistencia($existencia): MedicionAfore
    {
        $this->existencia = $existencia;
    
        return $this;
    }

    /**
     * Get existenciaM3.
     *
     * @return string
     */
    public function getExistencia(): string
    {
        return $this->existencia;
    }

    /**
     * Set tanque.
     *
     * @param Tanque $tanque
     *
     * @return MedicionAfore
     */
    public function setTanque(Tanque $tanque = null): MedicionAfore
    {
        $this->tanque = $tanque;
    
        return $this;
    }

    /**
     * Get tanque.
     *
     * @return Tanque
     */
    public function getTanque(): Tanque
    {
        return $this->tanque;
    }
}
