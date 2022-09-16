<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MarcaTecnologica
 *
 * @ORM\Table(name="nomencladores.marcas_tecnologicas")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\MarcaTecnologicaRepository")
 */
class MarcaTecnologica
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
     * @return MarcaTecnologica
     */
    public function setNombre($nombre): MarcaTecnologica
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
}
