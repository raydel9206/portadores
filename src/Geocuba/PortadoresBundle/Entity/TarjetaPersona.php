<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarjetaPersona
 *
 * @ORM\Table(name="nomencladores.tarjeta_persona", indexes={@ORM\Index(name="IDX_2934DBC04D489839", columns={"ntarjetaid"}), @ORM\Index(name="IDX_2934DBC0C5233BC6", columns={"npersonaid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TarjetaPersonaRepository")
 */
class TarjetaPersona
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
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ntarjetaid", referencedColumnName="id")
     * })
     */
    private $ntarjetaid;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="npersonaid", referencedColumnName="id")
     * })
     */
    private $npersonaid;



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
     * @return TarjetaPersona
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
     * Set ntarjetaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid
     *
     * @return TarjetaPersona
     */
    public function setTarjetaid(\Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid = null)
    {
        $this->ntarjetaid = $ntarjetaid;

        return $this;
    }

    /**
     * Get ntarjetaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getTarjetaid()
    {
        return $this->ntarjetaid;
    }

    /**
     * Set npersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $npersonaid
     *
     * @return TarjetaPersona
     */
    public function setPersonaid(\Geocuba\PortadoresBundle\Entity\Persona $npersonaid = null)
    {
        $this->npersonaid = $npersonaid;

        return $this;
    }

    /**
     * Get npersonaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getPersonaid()
    {
        return $this->npersonaid;
    }
}
