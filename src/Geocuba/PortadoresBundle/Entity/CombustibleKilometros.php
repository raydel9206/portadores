<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CombustibleKilometros
 *
 * @ORM\Table(name="datos.combustible_kilometros", indexes={@ORM\Index(name="IDX_944FF11E4D489839", columns={"ntarjetaid"}), @ORM\Index(name="IDX_944FF11E5AC78F83", columns={"anexo_unicoid"}), @ORM\Index(name="IDX_944FF11EC5233BC6", columns={"npersonaid"})})
 * @ORM\Entity
 */
class CombustibleKilometros
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="kilometraje", type="float", precision=10, scale=0, nullable=false)
     */
    private $kilometraje;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_abastecido", type="float", precision=10, scale=0, nullable=false)
     */
    private $combustibleAbastecido;

    /**
     * @var string
     *
     * @ORM\Column(name="combustible_estimado_tanque", type="float", precision=10, scale=0, nullable=false)
     */
    private $combustibleEstimadoTanque;

    /**
     * @var string
     *
     * @ORM\Column(name="anexo_unicokilometraje_proximo_mantenimiento", type="float", precision=10, scale=0, nullable=true)
     */
    private $anexoUnicokilometrajeProximoMantenimiento;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ntarjetaid", referencedColumnName="id")
     * })
     */
    private $ntarjetaid;

    /**
     * @var \AnexoUnico
     *
     * @ORM\ManyToOne(targetEntity="AnexoUnico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="anexo_unicoid", referencedColumnName="id")
     * })
     */
    private $anexoUnicoid;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="npersonaid", referencedColumnName="id")
     * })
     */
    private $npersonaid;



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
     * Set fecha
     *
     * @param \DateTime $fecha
     *
     * @return CombustibleKilometros
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;

        return $this;
    }

    /**
     * Get fecha
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set kilometraje
     *
     * @param string $kilometraje
     *
     * @return CombustibleKilometros
     */
    public function setKilometraje($kilometraje)
    {
        $this->kilometraje = $kilometraje;

        return $this;
    }

    /**
     * Get kilometraje
     *
     * @return string
     */
    public function getKilometraje()
    {
        return $this->kilometraje;
    }

    /**
     * Set combustibleAbastecido
     *
     * @param string $combustibleAbastecido
     *
     * @return CombustibleKilometros
     */
    public function setCombustibleAbastecido($combustibleAbastecido)
    {
        $this->combustibleAbastecido = $combustibleAbastecido;

        return $this;
    }

    /**
     * Get combustibleAbastecido
     *
     * @return string
     */
    public function getCombustibleAbastecido()
    {
        return $this->combustibleAbastecido;
    }

    /**
     * Set combustibleEstimadoTanque
     *
     * @param string $combustibleEstimadoTanque
     *
     * @return CombustibleKilometros
     */
    public function setCombustibleEstimadoTanque($combustibleEstimadoTanque)
    {
        $this->combustibleEstimadoTanque = $combustibleEstimadoTanque;

        return $this;
    }

    /**
     * Get combustibleEstimadoTanque
     *
     * @return string
     */
    public function getCombustibleEstimadoTanque()
    {
        return $this->combustibleEstimadoTanque;
    }

    /**
     * Set anexoUnicokilometrajeProximoMantenimiento
     *
     * @param string $anexoUnicokilometrajeProximoMantenimiento
     *
     * @return CombustibleKilometros
     */
    public function setAnexoUnicokilometrajeProximoMantenimiento($anexoUnicokilometrajeProximoMantenimiento)
    {
        $this->anexoUnicokilometrajeProximoMantenimiento = $anexoUnicokilometrajeProximoMantenimiento;

        return $this;
    }

    /**
     * Get anexoUnicokilometrajeProximoMantenimiento
     *
     * @return string
     */
    public function getAnexoUnicokilometrajeProximoMantenimiento()
    {
        return $this->anexoUnicokilometrajeProximoMantenimiento;
    }

    /**
     * Set ntarjetaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid
     *
     * @return CombustibleKilometros
     */
    public function setNtarjetaid(\Geocuba\PortadoresBundle\Entity\Tarjeta $ntarjetaid = null)
    {
        $this->ntarjetaid = $ntarjetaid;

        return $this;
    }

    /**
     * Get ntarjetaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getNtarjetaid()
    {
        return $this->ntarjetaid;
    }

    /**
     * Set anexoUnicoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\AnexoUnico $anexoUnicoid
     *
     * @return CombustibleKilometros
     */
    public function setAnexoUnicoid(\Geocuba\PortadoresBundle\Entity\AnexoUnico $anexoUnicoid = null)
    {
        $this->anexoUnicoid = $anexoUnicoid;

        return $this;
    }

    /**
     * Get anexoUnicoid
     *
     * @return \Geocuba\PortadoresBundle\Entity\AnexoUnico
     */
    public function getAnexoUnicoid()
    {
        return $this->anexoUnicoid;
    }

    /**
     * Set npersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $npersonaid
     *
     * @return CombustibleKilometros
     */
    public function setNpersonaid(\Geocuba\PortadoresBundle\Entity\Persona $npersonaid = null)
    {
        $this->npersonaid = $npersonaid;

        return $this;
    }

    /**
     * Get npersonaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getNpersonaid()
    {
        return $this->npersonaid;
    }
}
