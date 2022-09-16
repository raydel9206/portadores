<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PieFirma
 *
 * @ORM\Table(name="nomencladores.pie_firma", indexes={@ORM\Index(name="IDX_EE02CC9925B77781", columns={"autoriza"}), @ORM\Index(name="IDX_EE02CC994D6ADD7A", columns={"confecciona"}), @ORM\Index(name="IDX_EE02CC99910F8140", columns={"revisa"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\PieFirmaRepository")
 */
class PieFirma
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
     * @ORM\Column(name="documento", type="string", length=255, nullable=false)
     */
    private $documento;

    /**
     * @var npersona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="autoriza", referencedColumnName="id")
     * })
     */
    private $autoriza;

    /**
     * @var npersona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="confecciona", referencedColumnName="id")
     * })
     */
    private $confecciona;

    /**
     * @var npersona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="revisa", referencedColumnName="id")
     * })
     */
    private $revisa;

    /**
     * @var npersona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cajera", referencedColumnName="id")
     * })
     */
    private $cajera;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;

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
     * Set documento
     *
     * @param string $documento
     *
     * @return PieFirma
     */
    public function setDocumento($documento)
    {
        $this->documento = $documento;

        return $this;
    }

    /**
     * Get documento
     *
     * @return string
     */
    public function getDocumento()
    {
        return $this->documento;
    }

    /**
     * Set autoriza
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $autoriza
     *
     * @return PieFirma
     */
    public function setAutoriza(\Geocuba\PortadoresBundle\Entity\Persona $autoriza = null)
    {
        $this->autoriza = $autoriza;

        return $this;
    }

    /**
     * Get autoriza
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getAutoriza()
    {
        return $this->autoriza;
    }

    /**
     * Set confecciona
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $confecciona
     *
     * @return PieFirma
     */
    public function setConfecciona(\Geocuba\PortadoresBundle\Entity\Persona $confecciona = null)
    {
        $this->confecciona = $confecciona;

        return $this;
    }

    /**
     * Get confecciona
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getConfecciona()
    {
        return $this->confecciona;
    }

    /**
     * Set revisa
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $revisa
     *
     * @return PieFirma
     */
    public function setRevisa(\Geocuba\PortadoresBundle\Entity\Persona $revisa = null)
    {
        $this->revisa = $revisa;

        return $this;
    }

    /**
     * Get revisa
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getRevisa()
    {
        return $this->revisa;
    }

    /**
     * Set cajera
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $cajera
     *
     * @return PieFirma
     */
    public function setCajera(\Geocuba\PortadoresBundle\Entity\Persona $cajera = null)
    {
        $this->cajera = $cajera;

        return $this;
    }

    /**
     * Get cajera
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getCajera()
    {
        return $this->cajera;
    }

    /**
     * Set nunidadid.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $nunidadid
     *
     * @return PieFirma
     */
    public function setNunidadid(\Geocuba\PortadoresBundle\Entity\Unidad $nunidadid = null)
    {
        $this->nunidadid = $nunidadid;

        return $this;
    }

    /**
     * Get nunidadid.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getNunidadid()
    {
        return $this->nunidadid;
    }
}
