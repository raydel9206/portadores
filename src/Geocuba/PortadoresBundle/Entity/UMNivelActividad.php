<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UMNivelActividad
 *
 * @ORM\Table(name="nomencladores.um_nivel_actividad")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\UMNivelActividadRepository")
 */
class UMNivelActividad
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
     * @ORM\Column(name="nivel_actividad", type="string", length=255, nullable=true)
     */
    private $nivelActividad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
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
     * Set nivelActividad
     *
     * @param string $nivelActividad
     *
     * @return UMNivelActividad
     */
    public function setNivelActividad($nivelActividad)
    {
        $this->nivelActividad = $nivelActividad;

        return $this;
    }

    /**
     * Get nivelActividad
     *
     * @return string
     */
    public function getNivelActividad()
    {
        return $this->nivelActividad;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return UMNivelActividad
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
