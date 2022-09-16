<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BancoTransformadores
 *
 * @ORM\Table(name="nomencladores.banco_transformadores")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\BancoTransformadoresRepository")
 */
class BancoTransformadores
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
     * @var float
     *
     * @ORM\Column(name="capacidad", type="float", precision=10, scale=0, nullable=false)
     */
    private $capacidad;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible = true;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", nullable=true)
     */
    private $tipo;

    /**
     * @var float
     *
     * @ORM\Column(name="pfe", type="float", precision=10, scale=0, nullable=true)
     */
    private $pfe;

    /**
     * @var float
     *
     * @ORM\Column(name="pcu", type="float", precision=10, scale=0, nullable=true)
     */
    private $pcu;



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
     * Set capacidad
     *
     * @param float $capacidad
     *
     * @return BancoTransformadores
     */
    public function setCapacidad($capacidad)
    {
        $this->capacidad = $capacidad;

        return $this;
    }

    /**
     * Get capacidad
     *
     * @return float
     */
    public function getCapacidad()
    {
        return $this->capacidad;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return BancoTransformadores
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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return BancoTransformadores
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set pfe
     *
     * @param float $pfe
     *
     * @return BancoTransformadores
     */
    public function setPfe($pfe)
    {
        $this->pfe = $pfe;

        return $this;
    }

    /**
     * Get pfe
     *
     * @return float
     */
    public function getPfe()
    {
        return $this->pfe;
    }

    /**
     * Set pcu
     *
     * @param float $pcu
     *
     * @return BancoTransformadores
     */
    public function setPcu($pcu)
    {
        $this->pcu = $pcu;

        return $this;
    }

    /**
     * Get pcu
     *
     * @return float
     */
    public function getPcu()
    {
        return $this->pcu;
    }
}
