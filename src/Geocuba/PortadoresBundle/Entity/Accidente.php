<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Accidente
 *
 * @ORM\Table(name="datos.accidente", indexes={@ORM\Index(name="IDX_2306F3F760C04838", columns={"vehiculoid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\AccidenteRepository")
 */
class Accidente
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
     * @ORM\Column(name="asignado", type="string", length=255, nullable=false)
     */
    private $asignado;

    /**
     * @var string
     *
     * @ORM\Column(name="nota_informativa", type="string", length=255, nullable=true)
     */
    private $notaInformativa;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_accidente", type="date", nullable=false)
     */
    private $fechaAccidente;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_indemnizado", type="date", nullable=true)
     */
    private $fechaIndemnizado;

    /**
     * @var string
     *
     * @ORM\Column(name="importe_indemnizacion", type="decimal", precision=20, scale=2, nullable=true)
     */
    private $importeIndemnizacion;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vehiculoid", referencedColumnName="id")
     * })
     */
    private $vehiculoid;



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
     * Set asignado
     *
     * @param string $asignado
     *
     * @return Accidente
     */
    public function setAsignado($asignado)
    {
        $this->asignado = $asignado;

        return $this;
    }

    /**
     * Get asignado
     *
     * @return string
     */
    public function getAsignado()
    {
        return $this->asignado;
    }

    /**
     * Set notaInformativa
     *
     * @param string $notaInformativa
     *
     * @return Accidente
     */
    public function setNotaInformativa($notaInformativa)
    {
        $this->notaInformativa = $notaInformativa;

        return $this;
    }

    /**
     * Get notaInformativa
     *
     * @return string
     */
    public function getNotaInformativa()
    {
        return $this->notaInformativa;
    }

    /**
     * Set fechaAccidente
     *
     * @param \DateTime $fechaAccidente
     *
     * @return Accidente
     */
    public function setFechaAccidente($fechaAccidente)
    {
        $this->fechaAccidente = $fechaAccidente;

        return $this;
    }

    /**
     * Get fechaAccidente
     *
     * @return \DateTime
     */
    public function getFechaAccidente()
    {
        return $this->fechaAccidente;
    }

    /**
     * Set fechaIndemnizado
     *
     * @param \DateTime $fechaIndemnizado
     *
     * @return Accidente
     */
    public function setFechaIndemnizado($fechaIndemnizado)
    {
        $this->fechaIndemnizado = $fechaIndemnizado;

        return $this;
    }

    /**
     * Get fechaIndemnizado
     *
     * @return \DateTime
     */
    public function getFechaIndemnizado()
    {
        return $this->fechaIndemnizado;
    }

    /**
     * Set importeIndemnizacion
     *
     * @param string $importeIndemnizacion
     *
     * @return Accidente
     */
    public function setImporteIndemnizacion($importeIndemnizacion)
    {
        $this->importeIndemnizacion = $importeIndemnizacion;

        return $this;
    }

    /**
     * Get importeIndemnizacion
     *
     * @return string
     */
    public function getImporteIndemnizacion()
    {
        return $this->importeIndemnizacion;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Accidente
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
     * Set vehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculoid
     *
     * @return Accidente
     */
    public function setVehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $vehiculoid = null)
    {
        $this->vehiculoid = $vehiculoid;

        return $this;
    }

    /**
     * Get vehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getVehiculoid()
    {
        return $this->vehiculoid;
    }
}
