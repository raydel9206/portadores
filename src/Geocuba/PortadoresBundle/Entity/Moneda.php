<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Moneda
 *
 * @ORM\Table(name="nomencladores.moneda")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\MonedaRepository")
 */
class Moneda
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
     * @ORM\Column(name="nombre", type="string", length=255, nullable=false)
     */
    private $nombre;

    /**
     * @var boolean
     *
     * @ORM\Column(name="unica", type="boolean", nullable=false)
     */
    private $unica;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;



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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Nmoneda
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set unica
     *
     * @param boolean $unica
     *
     * @return Nmoneda
     */
    public function setUnica($unica)
    {
        $this->unica = $unica;

        return $this;
    }

    /**
     * Get unica
     *
     * @return boolean
     */
    public function getUnica()
    {
        return $this->unica;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Nmoneda
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
