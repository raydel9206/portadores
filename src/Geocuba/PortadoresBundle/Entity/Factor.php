<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Factor
 *
 * @ORM\Table(name="nomencladores.factor", indexes={@ORM\Index(name="IDX_504BE483390E39B1", columns={"unidad_medida_id1"}), @ORM\Index(name="IDX_504BE483A007680B", columns={"unidad_medida_id2"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\FactorRepository")
 */
class Factor
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
     * @ORM\Column(name="portador", type="string", length=255, nullable=false)
     */
    private $portador;

    /**
     * @var string
     *
     * @ORM\Column(name="factor_id1", type="decimal", precision=10, scale=0, nullable=false)
     */
    private $factorId1;

    /**
     * @var string
     *
     * @ORM\Column(name="factor_id2", type="decimal", precision=10, scale=0, nullable=false)
     */
    private $factorId2;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible = true;

    /**
     * @var \UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_medida_id1", referencedColumnName="id")
     * })
     */
    private $unidadMedida1;

    /**
     * @var \UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_medida_id2", referencedColumnName="id")
     * })
     */
    private $unidadMedida2;



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
     * Set portador
     *
     * @param string $portador
     *
     * @return Factor
     */
    public function setPortador($portador)
    {
        $this->portador = $portador;

        return $this;
    }

    /**
     * Get portador
     *
     * @return string
     */
    public function getPortador()
    {
        return $this->portador;
    }

    /**
     * Set factorId1
     *
     * @param string $factorId1
     *
     * @return Factor
     */
    public function setFactorId1($factorId1)
    {
        $this->factorId1 = $factorId1;

        return $this;
    }

    /**
     * Get factorId1
     *
     * @return string
     */
    public function getFactorId1()
    {
        return $this->factorId1;
    }

    /**
     * Set factorId2
     *
     * @param string $factorId2
     *
     * @return Factor
     */
    public function setFactorId2($factorId2)
    {
        $this->factorId2 = $factorId2;

        return $this;
    }

    /**
     * Get factorId2
     *
     * @return string
     */
    public function getFactorId2()
    {
        return $this->factorId2;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Factor
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
     * Set unidadMedida1
     *
     * @param \Geocuba\PortadoresBundle\Entity\UnidadMedida $unidadMedida1
     *
     * @return Factor
     */
    public function setUnidadMedida1(\Geocuba\PortadoresBundle\Entity\UnidadMedida $unidadMedida1 = null)
    {
        $this->unidadMedida1 = $unidadMedida1;

        return $this;
    }

    /**
     * Get unidadMedida1
     *
     * @return \Geocuba\PortadoresBundle\Entity\UnidadMedida
     */
    public function getUnidadMedida1()
    {
        return $this->unidadMedida1;
    }

    /**
     * Set unidadMedida2
     *
     * @param \Geocuba\PortadoresBundle\Entity\UnidadMedida $unidadMedida2
     *
     * @return Factor
     */
    public function setUnidadMedida2(\Geocuba\PortadoresBundle\Entity\UnidadMedida $unidadMedida2 = null)
    {
        $this->unidadMedida2 = $unidadMedida2;

        return $this;
    }

    /**
     * Get unidadMedida2
     *
     * @return \Geocuba\PortadoresBundle\Entity\UnidadMedida
     */
    public function getUnidadMedida2()
    {
        return $this->unidadMedida2;
    }
}
