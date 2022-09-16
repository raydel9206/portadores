<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ActaRespMaterial
 *
 * @ORM\Table(name="datos.acta_resp_material")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\ActaRespMaterialRepository")
 */
class ActaRespMaterial
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="mes", type="string", nullable=true)
     */
    private $mes;

    /**
     * @var integer
     *
     * @ORM\Column(name="anno", type="integer", nullable=true)
     */
    private $anno;

    /**
     * @var string
     *
     * @ORM\Column(name="tarjeta", type="string", nullable=false)
     */
    private $tarjeta;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="entregaid", referencedColumnName="id")
     * })
     */
    private $entregaid;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="recibeid", referencedColumnName="id")
     * })
     */
    private $recibeid;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;



    /**
     * Get string
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return ActaRespMaterial
     */
    public function setFecha($fecha): ActaRespMaterial
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha(): \DateTime
    {
        return $this->fecha;
    }



    /**
     * Set mes
     *
     * @param string $mes
     *
     * @return ActaRespMaterial
     */
    public function setMes($mes): ActaRespMaterial
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes
     *
     * @return string
     */
    public function getMes(): string
    {
        return $this->mes;
    }

    /**
     * Set anno
     *
     * @param integer $anno
     *
     * @return ActaRespMaterial
     */
    public function setAnno($anno): ActaRespMaterial
    {
        $this->anno = $anno;

        return $this;
    }

    /**
     * Get anno
     *
     * @return integer
     */
    public function getAnno(): int
    {
        return $this->anno;
    }

    /**
     * Set tarjeta
     *
     * @param string $tarjeta
     *
     * @return ActaRespMaterial
     */
    public function setTarjeta($tarjeta): ActaRespMaterial
    {
        $this->tarjeta = $tarjeta;

        return $this;
    }

    /**
     * Get tarjeta
     *
     * @return string
     */
    public function getTarjeta(): string
    {
        return $this->tarjeta;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return ActaRespMaterial
     */
    public function setVisible($visible): ActaRespMaterial
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible(): bool
    {
        return $this->visible;
    }

    /**
     * Set entregaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $entregaid
     *
     * @return ActaRespMaterial
     */
    public function setEntregaid(\Geocuba\PortadoresBundle\Entity\Persona $entregaid = null): ActaRespMaterial
    {
        $this->entregaid = $entregaid;

        return $this;
    }

    /**
     * Get entregaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getEntregaid(): Persona
    {
        return $this->entregaid;
    }

    /**
     * Set recibeid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $recibeid
     *
     * @return ActaRespMaterial
     */
    public function setRecibeid(\Geocuba\PortadoresBundle\Entity\Persona $recibeid = null): ActaRespMaterial
    {
        $this->recibeid = $recibeid;

        return $this;
    }

    /**
     * Get recibeid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getRecibeid(): Persona
    {
        return $this->recibeid;
    }



    /**
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return ActaRespMaterial
     */
    public function setUnidadid(\Geocuba\PortadoresBundle\Entity\Unidad $nunidadid = null): ActaRespMaterial
    {
        $this->nunidadid = $nunidadid;

        return $this;
    }

    /**
     * Get nunidadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getUnidadid(): Unidad
    {
        return $this->nunidadid;
    }
    
}
