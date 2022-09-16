<?php

namespace Geocuba\PortadoresBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * MedicionDiariaTanque
 *
 * @ORM\Table(name="datos.mediciones_diarias_tanques", indexes={@ORM\Index(name="IDX_FE2CFEF2E83ABB22", columns={"tanque_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\MedicionDiariaTanqueRepository")
 */
class MedicionDiariaTanque
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
     * @ORM\Column(name="medicion", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $medicion;

    /**
     * @var string
     *
     * @ORM\Column(name="existencia", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $existencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="consumo", type="decimal", precision=12, scale=4, nullable=true)
     */
    private $consumo;

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
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set fecha.
     *
     * @param DateTime $fecha
     *
     * @return MedicionDiariaTanque
     */
    public function setFecha($fecha): MedicionDiariaTanque
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
     * Set medicion.
     *
     * @param string $medicion
     *
     * @return MedicionDiariaTanque
     */
    public function setMedicion($medicion): MedicionDiariaTanque
    {
        $this->medicion = $medicion;
    
        return $this;
    }

    /**
     * Get medicion.
     *
     * @return string
     */
    public function getMedicion(): string
    {
        return $this->medicion;
    }

    /**
     * Set litros.
     *
     * @param string $existencia
     *
     * @return MedicionDiariaTanque
     */
    public function setExistencia($existencia): MedicionDiariaTanque
    {
        $this->existencia = $existencia;
    
        return $this;
    }

    /**
     * Get litros.
     *
     * @return string
     */
    public function getExistencia(): string
    {
        return $this->existencia;
    }

    /**
     * Set consumo.
     *
     * @param string|null $consumo
     *
     * @return MedicionDiariaTanque
     */
    public function setConsumo($consumo = null): MedicionDiariaTanque
    {
        $this->consumo = $consumo;
    
        return $this;
    }

    /**
     * Get consumo.
     *
     * @return string|null
     */
    public function getConsumo(): ?string
    {
        return $this->consumo;
    }

    /**
     * Set tanque.
     *
     * @param Tanque $tanque
     *
     * @return MedicionDiariaTanque
     */
    public function setTanque(Tanque $tanque): MedicionDiariaTanque
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
