<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Plan
 *
 * @ORM\Table(name="datos.plan", indexes={@ORM\Index(name="IDX_474B3960F3E6D02F", columns={"unidad"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\PlanRepository")
 */
class Plan
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
     * @ORM\Column(name="anno", type="string", length=255, nullable=true)
     */
    private $anno;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(name="diesel", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $diesel;

    /**
     * @var string
     *
     * @ORM\Column(name="gasolina", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $gasolina;

    /**
     * @var string
     *
     * @ORM\Column(name="glp", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $glp;

    /**
     * @var string
     *
     * @ORM\Column(name="electricidad", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $electricidad;

    /**
     * @var string
     *
     * @ORM\Column(name="lubricantes", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lubricantes;

    /**
     * @var string
     *
     * @ORM\Column(name="dieselcuc", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $dieselcuc;

    /**
     * @var string
     *
     * @ORM\Column(name="gasolinacuc", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $gasolinacuc;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="lubricantecuc", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $lubricantecuc;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad", referencedColumnName="id")
     * })
     */
    private $unidad;



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
     * Set anno
     *
     * @param string $anno
     *
     * @return Plan
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return string
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Plan
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
     * Set diesel
     *
     * @param string $diesel
     *
     * @return Plan
     */
    public function setDiesel($diesel)
    {
        $this->diesel = $diesel;

        return $this;
    }

    /**
     * Get diesel
     *
     * @return string
     */
    public function getDiesel()
    {
        return $this->diesel;
    }

    /**
     * Set gasolina
     *
     * @param string $gasolina
     *
     * @return Plan
     */
    public function setGasolina($gasolina)
    {
        $this->gasolina = $gasolina;

        return $this;
    }

    /**
     * Get gasolina
     *
     * @return string
     */
    public function getGasolina()
    {
        return $this->gasolina;
    }

    /**
     * Set glp
     *
     * @param string $glp
     *
     * @return Plan
     */
    public function setGlp($glp)
    {
        $this->glp = $glp;

        return $this;
    }

    /**
     * Get glp
     *
     * @return string
     */
    public function getGlp()
    {
        return $this->glp;
    }

    /**
     * Set electricidad
     *
     * @param string $electricidad
     *
     * @return Plan
     */
    public function setElectricidad($electricidad)
    {
        $this->electricidad = $electricidad;

        return $this;
    }

    /**
     * Get electricidad
     *
     * @return string
     */
    public function getElectricidad()
    {
        return $this->electricidad;
    }

    /**
     * Set lubricantes
     *
     * @param string $lubricantes
     *
     * @return Plan
     */
    public function setLubricantes($lubricantes)
    {
        $this->lubricantes = $lubricantes;

        return $this;
    }

    /**
     * Get lubricantes
     *
     * @return string
     */
    public function getLubricantes()
    {
        return $this->lubricantes;
    }

    /**
     * Set dieselcuc
     *
     * @param string $dieselcuc
     *
     * @return Plan
     */
    public function setDieselcuc($dieselcuc)
    {
        $this->dieselcuc = $dieselcuc;

        return $this;
    }

    /**
     * Get dieselcuc
     *
     * @return string
     */
    public function getDieselcuc()
    {
        return $this->dieselcuc;
    }

    /**
     * Set gasolinacuc
     *
     * @param string $gasolinacuc
     *
     * @return Plan
     */
    public function setGasolinacuc($gasolinacuc)
    {
        $this->gasolinacuc = $gasolinacuc;

        return $this;
    }

    /**
     * Get gasolinacuc
     *
     * @return string
     */
    public function getGasolinacuc()
    {
        return $this->gasolinacuc;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return Plan
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

    /**
     * Set lubricantecuc
     *
     * @param string $lubricantecuc
     *
     * @return Plan
     */
    public function setLubricantecuc($lubricantecuc)
    {
        $this->lubricantecuc = $lubricantecuc;

        return $this;
    }

    /**
     * Get lubricantecuc
     *
     * @return string
     */
    public function getLubricantecuc()
    {
        return $this->lubricantecuc;
    }

    /**
     * Set unidad
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $unidad
     *
     * @return Plan
     */
    public function setUnidad(\Geocuba\PortadoresBundle\Entity\Unidad $unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
}
