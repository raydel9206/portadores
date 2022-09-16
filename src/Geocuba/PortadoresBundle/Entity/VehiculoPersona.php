<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VehiculoPersona
 *
 * @ORM\Table(name="nomencladores.vehiculo_persona", indexes={@ORM\Index(name="IDX_2AB7E12D632F5E0E", columns={"idvehiculo"}), @ORM\Index(name="IDX_2AB7E12D8EE1E4F5", columns={"idpersona"})})
 * @ORM\Entity
 */
class VehiculoPersona
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
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var \Vehiculo
     *
     * @ORM\ManyToOne(targetEntity="Vehiculo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idvehiculo", referencedColumnName="id")
     * })
     */
    private $idvehiculo;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idpersona", referencedColumnName="id")
     * })
     */
    private $idpersona;



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
     * Set visible
     *
     * @param boolean $visible
     *
     * @return VehiculoPersona
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
     * Set idvehiculo
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $idvehiculo
     *
     * @return VehiculoPersona
     */
    public function setIdvehiculo(\Geocuba\PortadoresBundle\Entity\Vehiculo $idvehiculo = null)
    {
        $this->idvehiculo = $idvehiculo;

        return $this;
    }

    /**
     * Get idvehiculo
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getIdvehiculo()
    {
        return $this->idvehiculo;
    }

    /**
     * Set idpersona
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $idpersona
     *
     * @return VehiculoPersona
     */
    public function setIdpersona(\Geocuba\PortadoresBundle\Entity\Persona $idpersona = null)
    {
        $this->idpersona = $idpersona;

        return $this;
    }

    /**
     * Get idpersona
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getIdpersona()
    {
        return $this->idpersona;
    }
}
