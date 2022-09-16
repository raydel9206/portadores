<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * DistribucionCombustibleTecnologicos
 *
 * @ORM\Table(name="datos.distribucion_combustible_tecnologicos", indexes={@ORM\Index(name="IDX_C5740939596E597F", columns={"tipo_combustible_id"}), @ORM\Index(name="IDX_C57409399D01464C", columns={"unidad_id"})})
 * @ORM\Entity
 */
class DistribucionCombustibleTecnologicos
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
     * @var int
     *
     * @ORM\Column(name="anno", type="integer", nullable=false)
     */
    private $anno;

    /**
     * @var int
     *
     * @ORM\Column(name="mes", type="integer", nullable=false)
     */
    private $mes;

    /**
     * @var TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible_id", referencedColumnName="id")
     * })
     */
    private $tipoCombustible;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    private $unidad;

    /** @OneToMany(targetEntity="DistribucionCombustibleTecnologicosDesglose", mappedBy="distribucionCombustibleTecnologicos", orphanRemoval=true, cascade={"persist"}) */
    private $desglose;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->desglose = new ArrayCollection();
    }

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
     * Set anno.
     *
     * @param int $anno
     *
     * @return DistribucionCombustibleTecnologicos
     */
    public function setAnno($anno): DistribucionCombustibleTecnologicos
    {
        $this->anno = $anno;
    
        return $this;
    }

    /**
     * Get anno.
     *
     * @return int
     */
    public function getAnno(): int
    {
        return $this->anno;
    }

    /**
     * Set mes.
     *
     * @param int $mes
     *
     * @return DistribucionCombustibleTecnologicos
     */
    public function setMes($mes): DistribucionCombustibleTecnologicos
    {
        $this->mes = $mes;
    
        return $this;
    }

    /**
     * Get mes.
     *
     * @return int
     */
    public function getMes(): int
    {
        return $this->mes;
    }

    /**
     * Set tipoCombustible.
     *
     * @param TipoCombustible $tipoCombustible
     *
     * @return DistribucionCombustibleTecnologicos
     */
    public function setTipoCombustible(TipoCombustible $tipoCombustible): DistribucionCombustibleTecnologicos
    {
        $this->tipoCombustible = $tipoCombustible;
    
        return $this;
    }

    /**
     * Get tipoCombustible.
     *
     * @return TipoCombustible
     */
    public function getTipoCombustible(): TipoCombustible
    {
        return $this->tipoCombustible;
    }

    /**
     * Set unidad.
     *
     * @param Unidad $unidad
     *
     * @return DistribucionCombustibleTecnologicos
     */
    public function setUnidad(Unidad $unidad) : DistribucionCombustibleTecnologicos
    {
        $this->unidad = $unidad;
    
        return $this;
    }

    /**
     * Get unidad.
     *
     * @return Unidad
     */
    public function getUnidad() : Unidad
    {
        return $this->unidad;
    }
}
