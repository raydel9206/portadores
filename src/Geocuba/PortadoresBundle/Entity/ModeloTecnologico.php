<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ModeloTecnologico
 *
 * @ORM\Table(name="nomencladores.modelos_tecnologicos", indexes={@ORM\Index(name="IDX_9F456DDAF6DB0E", columns={"marca_tecnologica_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ModeloTecnologicoRepository")
 */
class ModeloTecnologico
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
     * @ORM\Column(name="nombre", type="string", nullable=false)
     */
    private $nombre;

    /**
     * @var MarcaTecnologica
     *
     * @ORM\ManyToOne(targetEntity="MarcaTecnologica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marca_tecnologica_id", referencedColumnName="id")
     * })
     */
    private $marcaTecnologica;



    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set nombre.
     *
     * @param string $nombre
     *
     * @return ModeloTecnologico
     */
    public function setNombre($nombre): ModeloTecnologico
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre.
     *
     * @return string
     */
    public function getNombre(): string
    {
        return $this->nombre;
    }

    /**
     * Set marcaTecnologica.
     *
     * @param MarcaTecnologica $marcaTecnologica
     *
     * @return ModeloTecnologico
     */
    public function setMarcaTecnologica(MarcaTecnologica $marcaTecnologica): ModeloTecnologico
    {
        $this->marcaTecnologica = $marcaTecnologica;
    
        return $this;
    }

    /**
     * Get marcaTecnologica.
     *
     * @return MarcaTecnologica
     */
    public function getMarcaTecnologica(): MarcaTecnologica
    {
        return $this->marcaTecnologica;
    }
}
