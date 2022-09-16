<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Consecutivos
 *
 * @ORM\Table(name="datos.consecutivos", indexes={@ORM\Index(name="IDX_6F7F2AE7975B7D83", columns={"idunidad"})})
 * @ORM\Entity
 */
class Consecutivos
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
     * @ORM\Column(name="no_anticipo", type="integer", nullable=true)
     */
    private $noAnticipo =  1;

    /**
     * @var Unidad
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
     * @return Consecutivos
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
     * Set noAnticipo
     *
     * @param integer $noAnticipo
     *
     * @return Consecutivos
     */
    public function setNoAnticipo($noAnticipo)
    {
        $this->noAnticipo = $noAnticipo;

        return $this;
    }

    /**
     * Get noAnticipo
     *
     * @return integer
     */
    public function getNoAnticipo()
    {
        return $this->noAnticipo;
    }

    /**
     * Set idunidad
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $idunidad
     *
     * @return Consecutivos
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
