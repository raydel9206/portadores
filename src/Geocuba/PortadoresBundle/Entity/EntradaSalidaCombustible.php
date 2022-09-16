<?php

namespace Geocuba\PortadoresBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * EntradaSalidaCombustible
 *
 * @ORM\Table(name="datos.entradas_salidas_combustible", indexes={@ORM\Index(name="IDX_45BF8C36E83ABB22", columns={"tanque_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\EntradaSalidaCombustibleRepository")
 */
class EntradaSalidaCombustible
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
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="medicion_antes", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $medicionAntes;

    /**
     * @var string
     *
     * @ORM\Column(name="existencia_antes", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $existenciaAntes;

    /**
     * @var string
     *
     * @ORM\Column(name="medicion_despues", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $medicionDespues;

    /**
     * @var string
     *
     * @ORM\Column(name="existencia_despues", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $existenciaDespues;

    /**
     * @var string
     *
     * @ORM\Column(name="cantidad", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $cantidad;

    /**
     * @var bool
     *
     * @ORM\Column(name="entrada", type="boolean", nullable=false, options={"default"="1"})
     */
    private $entrada = true;

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
     * @return EntradaSalidaCombustible
     */
    public function setFecha($fecha): EntradaSalidaCombustible
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
     * Set medicionAntes.
     *
     * @param string $medicionAntes
     *
     * @return EntradaSalidaCombustible
     */
    public function setMedicionAntes($medicionAntes): EntradaSalidaCombustible
    {
        $this->medicionAntes = $medicionAntes;
    
        return $this;
    }

    /**
     * Get medicionAntes.
     *
     * @return string
     */
    public function getMedicionAntes(): string
    {
        return $this->medicionAntes;
    }

    /**
     * Set existenciaAntes.
     *
     * @param string $existenciaAntes
     *
     * @return EntradaSalidaCombustible
     */
    public function setExistenciaAntes($existenciaAntes): EntradaSalidaCombustible
    {
        $this->existenciaAntes = $existenciaAntes;
    
        return $this;
    }

    /**
     * Get existenciaAntes.
     *
     * @return string
     */
    public function getExistenciaAntes(): string
    {
        return $this->existenciaAntes;
    }

    /**
     * Set medicionDespues.
     *
     * @param string $medicionDespues
     *
     * @return EntradaSalidaCombustible
     */
    public function setMedicionDespues($medicionDespues): EntradaSalidaCombustible
    {
        $this->medicionDespues = $medicionDespues;
    
        return $this;
    }

    /**
     * Get medicionDespues.
     *
     * @return string
     */
    public function getMedicionDespues(): string
    {
        return $this->medicionDespues;
    }

    /**
     * Set existenciaDespues.
     *
     * @param string $existenciaDespues
     *
     * @return EntradaSalidaCombustible
     */
    public function setExistenciaDespues($existenciaDespues): EntradaSalidaCombustible
    {
        $this->existenciaDespues = $existenciaDespues;
    
        return $this;
    }

    /**
     * Get existenciaDespues.
     *
     * @return string
     */
    public function getExistenciaDespues(): string
    {
        return $this->existenciaDespues;
    }

    /**
     * Set cantidad.
     *
     * @param string $cantidad
     *
     * @return EntradaSalidaCombustible
     */
    public function setCantidad($cantidad): EntradaSalidaCombustible
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return string
     */
    public function getCantidad(): string
    {
        return $this->cantidad;
    }

    /**
     * Set entrada.
     *
     * @param bool $entrada
     *
     * @return EntradaSalidaCombustible
     */
    public function setEntrada($entrada): EntradaSalidaCombustible
    {
        $this->entrada = $entrada;
    
        return $this;
    }

    /**
     * Get entrada.
     *
     * @return bool
     */
    public function getEntrada(): bool
    {
        return $this->entrada;
    }

    /**
     * Set tanque.
     *
     * @param Tanque $tanque
     *
     * @return EntradaSalidaCombustible
     */
    public function setTanque(Tanque $tanque = null): EntradaSalidaCombustible
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
