<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VehiculoTipoMantenimiento
 *
 * @ORM\Table(name="nomencladores.vehiculo_tipo_mantenimiento", indexes={@ORM\Index(name="IDX_3F797F05EA6F2C9C", columns={"nvehiculoid"}), @ORM\Index(name="IDX_3F797F0511925AB3", columns={"tipo_mantenimientoid"})})
 * @ORM\Entity
 */
class VehiculoTipoMantenimiento
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
     * @ORM\Column(name="kilometros", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $kilometros;

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
     *   @ORM\JoinColumn(name="nvehiculoid", referencedColumnName="id")
     * })
     */
    private $nvehiculoid;

    /**
     * @var \TipoMantenimiento
     *
     * @ORM\ManyToOne(targetEntity="TipoMantenimiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_mantenimientoid", referencedColumnName="id")
     * })
     */
    private $tipoMantenimientoid;



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
     * Set kilometros
     *
     * @param string $kilometros
     *
     * @return VehiculoTipoMantenimiento
     */
    public function setKilometros($kilometros)
    {
        $this->kilometros = $kilometros;

        return $this;
    }

    /**
     * Get kilometros
     *
     * @return string
     */
    public function getKilometros()
    {
        return $this->kilometros;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return VehiculoTipoMantenimiento
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
     * Set nvehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid
     *
     * @return VehiculoTipoMantenimiento
     */
    public function setNvehiculoid(\Geocuba\PortadoresBundle\Entity\Vehiculo $nvehiculoid = null)
    {
        $this->nvehiculoid = $nvehiculoid;

        return $this;
    }

    /**
     * Get nvehiculoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Vehiculo
     */
    public function getNvehiculoid()
    {
        return $this->nvehiculoid;
    }

    /**
     * Set tipoMantenimientoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoMantenimiento $tipoMantenimientoid
     *
     * @return VehiculoTipoMantenimiento
     */
    public function setTipoMantenimientoid(\Geocuba\PortadoresBundle\Entity\TipoMantenimiento $tipoMantenimientoid = null)
    {
        $this->tipoMantenimientoid = $tipoMantenimientoid;

        return $this;
    }

    /**
     * Get tipoMantenimientoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoMantenimiento
     */
    public function getTipoMantenimientoid()
    {
        return $this->tipoMantenimientoid;
    }
}
