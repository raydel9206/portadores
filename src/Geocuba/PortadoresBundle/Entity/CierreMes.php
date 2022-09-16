<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CierreMes
 *
 * @ORM\Table(name="datos.cierre_mes", indexes={@ORM\Index(name="IDX_9B439BFB975B7D83", columns={"idunidad"})})
 * @ORM\Entity
 */
class CierreMes
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
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=false)
     */
    private $anno;

    /**
     * @var integer
     *
     * @ORM\Column(name="mes", type="integer", nullable=false)
     */
    private $mes;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cerrado", type="boolean", nullable=true)
     */
    private $cerrado = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="disponible", type="boolean", nullable=true)
     */
    private $disponible = false;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idunidad", referencedColumnName="id")
     * })
     */
    private $idunidad;



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
     * Set anno
     *
     * @param integer $anno
     *
     * @return CierreMes
     */
    public function setAnno($anno)
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return integer
     */
    public function getAnno()
    {
        return $this->anno;
    }

    /**
     * Set mes
     *
     * @param integer $mes
     *
     * @return CierreMes
     */
    public function setMes($mes)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return integer
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set cerrado
     *
     * @param boolean $cerrado
     *
     * @return CierreMes
     */
    public function setCerrado($cerrado)
    {
        $this->cerrado = $cerrado;

        return $this;
    }

    /**
     * Get cerrado
     *
     * @return boolean
     */
    public function getCerrado()
    {
        return $this->cerrado;
    }

    /**
     * Set disponible
     *
     * @param boolean $disponible
     *
     * @return CierreMes
     */
    public function setDisponible($disponible)
    {
        $this->disponible = $disponible;

        return $this;
    }

    /**
     * Get disponible
     *
     * @return boolean
     */
    public function getDisponible()
    {
        return $this->disponible;
    }

    /**
     * Set idunidad
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $idunidad
     *
     * @return CierreMes
     */
    public function setIdunidad(\Geocuba\PortadoresBundle\Entity\Unidad $idunidad = null)
    {
        $this->idunidad = $idunidad;

        return $this;
    }

    /**
     * Get idunidad
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getIdunidad()
    {
        return $this->idunidad;
    }
}
