<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistroCombustibleAnalisis
 *
 * @ORM\Table(name="datos.registro_combustible_analisis", indexes={@ORM\Index(name="IDX_4CC0148EDD62CDDE", columns={"conceptoid"}), @ORM\Index(name="IDX_4CC0148E8D29C97A", columns={"registro_combustible_id"})})
 * @ORM\Entity
 */
class RegistroCombustibleAnalisis
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=150, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="semana", type="string", length=150, nullable=true)
     */
    private $semana;

    /**
     * @var integer
     *
     * @ORM\Column(name="numerosemana", type="integer")
     */
    private $numerosemana;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $combustible = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="lubricante", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $lubricante = '0.00';

    /**
     * @var string
     *
     * @ORM\Column(name="km", type="decimal", precision=15, scale=2, nullable=true)
     */
    private $km = '0.00';

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var ConceptoRegistroCombustible
     *
     * @ORM\ManyToOne(targetEntity="ConceptoRegistroCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="conceptoid", referencedColumnName="id")
     * })
     */
    private $conceptoid;

    /**
     * @var RegistroCombustible
     *
     * @ORM\ManyToOne(targetEntity="RegistroCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registro_combustible_id", referencedColumnName="id")
     * })
     */
    private $registroCombustible;

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
     * Set semana
     *
     * @param string $semana
     *
     * @return RegistroCombustibleAnalisis
     */
    public function setSemana($semana)
    {
        $this->semana = $semana;

        return $this;
    }

    /**
     * Get semana
     *
     * @return string
     */
    public function getSemana()
    {
        return $this->semana;
    }

    /**
     * Set combustible
     *
     * @param string $combustible
     *
     * @return RegistroCombustibleAnalisis
     */
    public function setCombustible($combustible)
    {
        $this->combustible = $combustible;

        return $this;
    }

    /**
     * Get combustible
     *
     * @return string
     */
    public function getCombustible()
    {
        return $this->combustible;
    }

    /**
     * Set lubricante
     *
     * @param string $lubricante
     *
     * @return RegistroCombustibleAnalisis
     */
    public function setLubricante($lubricante)
    {
        $this->lubricante = $lubricante;

        return $this;
    }

    /**
     * Get lubricante
     *
     * @return string
     */
    public function getLubricante()
    {
        return $this->lubricante;
    }

    /**
     * Set km
     *
     * @param string $km
     *
     * @return RegistroCombustibleAnalisis
     */
    public function setKm($km)
    {
        $this->km = $km;

        return $this;
    }

    /**
     * Get km
     *
     * @return string
     */
    public function getKm()
    {
        return $this->km;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return RegistroCombustibleAnalisis
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
     * Set conceptoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\ConceptoRegistroCombustible $conceptoid
     *
     * @return RegistroCombustibleAnalisis
     */
    public function setConceptoid($conceptoid = null)
    {
        $this->conceptoid = $conceptoid;

        return $this;
    }

    /**
     * Get conceptoid
     *
     * @return ConceptoRegistroCombustible
     */
    public function getConceptoid()
    {
        return $this->conceptoid;
    }

    /**
     * Set registroCombustible
     *
     * @param \Geocuba\PortadoresBundle\Entity\RegistroCombustible $registroCombustible
     *
     * @return RegistroCombustibleAnalisis
     */
    public function setRegistroCombustible($registroCombustible = null)
    {
        $this->registroCombustible = $registroCombustible;

        return $this;
    }

    /**
     * Get registroCombustible
     *
     * @return \Geocuba\PortadoresBundle\Entity\RegistroCombustible
     */
    public function getRegistroCombustible()
    {
        return $this->registroCombustible;
    }

    /**
     * Set numerosemana
     *
     * @param integer $numerosemana
     *
     * @return RegistroCombustibleAnalisis
     */
    public function setNumerosemana($numerosemana)
    {
        $this->numerosemana = $numerosemana;

        return $this;
    }

    /**
     * Get numerosemana
     *
     * @return integer
     */
    public function getNumerosemana()
    {
        return $this->numerosemana;
    }
}
