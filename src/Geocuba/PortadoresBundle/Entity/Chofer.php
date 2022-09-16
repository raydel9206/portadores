<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chofer
 *
 * @ORM\Table(name="nomencladores.chofer", indexes={@ORM\Index(name="IDX_9C55D719C5233BC6", columns={"npersonaid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ChoferRepository")
 */
class Chofer
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
     * @var integer
     *
     * @ORM\Column(name="nro_licencia", type="string", length=255, nullable=false)
     */
    private $nroLicencia;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_expiracion_licencia", type="date", nullable=false)
     */
    private $fechaExpiracionLicencia;

    /**
     * @var Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="npersonaid", referencedColumnName="id")
     * })
     */
    private $npersonaid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible = true;

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
     * Set nroLicencia
     *
     * @param integer $nroLicencia
     *
     * @return Chofer
     */
    public function setNroLicencia($nroLicencia)
    {
        $this->nroLicencia = $nroLicencia;

        return $this;
    }

    /**
     * Get nroLicencia
     *
     * @return integer
     */
    public function getNroLicencia()
    {
        return $this->nroLicencia;
    }

    /**
     * Set fechaExpiracionLicencia
     *
     * @param \DateTime $fechaExpiracionLicencia
     *
     * @return Chofer
     */
    public function setFechaExpiracionLicencia($fechaExpiracionLicencia)
    {
        $this->fechaExpiracionLicencia = $fechaExpiracionLicencia;

        return $this;
    }

    /**
     * Get fechaExpiracionLicencia
     *
     * @return \DateTime
     */
    public function getFechaExpiracionLicencia()
    {
        return $this->fechaExpiracionLicencia;
    }

    /**
     * Set npersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $npersonaid
     *
     * @return Chofer
     */
    public function setNpersonaid(\Geocuba\PortadoresBundle\Entity\Persona $npersonaid = null)
    {
        $this->npersonaid = $npersonaid;

        return $this;
    }

    /**
     * Get npersonaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getNpersonaid()
    {
        return $this->npersonaid;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Chofer
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
}
