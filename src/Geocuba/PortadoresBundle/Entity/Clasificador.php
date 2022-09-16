<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Clasificador
 *
 * @ORM\Table(name="nomencladores.clasificador")
 * @ORM\Entity
 */
class Clasificador
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
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true, options={"default"="1"})
     */
    private $visible = true;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=true)
     */
    private $codigo;


    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre.
     *
     * @param string|null $nombre
     *
     * @return Clasificador
     */
    public function setNombre($nombre = null)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string|null
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set visible.
     *
     * @param bool|null $visible
     *
     * @return Clasificador
     */
    public function setVisible($visible = null)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible.
     *
     * @return bool|null
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set codigo.
     *
     * @param string|null $codigo
     *
     * @return Clasificador
     */
    public function setCodigo($codigo = null)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return string|null
     */
    public function getCodigo()
    {
        return $this->codigo;
    }
}
