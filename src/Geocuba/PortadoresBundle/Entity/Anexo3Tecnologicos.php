<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Anexo3Tecnologicos
 *
 * @ORM\Table(name="datos.anexo_3_tecnologicos", indexes={@ORM\Index(name="IDX_F11BB2D923BFBED", columns={"equipo_id"}), @ORM\Index(name="IDX_F11BB2D9E677106C", columns={"operario"}), @ORM\Index(name="IDX_F11BB2D952520D07", columns={"responsable"}), @ORM\Index(name="IDX_F11BB2D9BD0F409C", columns={"area_id"}), @ORM\Index(name="IDX_F11BB2D9596E597F", columns={"tipo_combustible_id"})})
 * @ORM\Entity
 */
class Anexo3Tecnologicos
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
     * @var string|null
     *
     * @ORM\Column(name="folio", type="string", nullable=true)
     */
    private $folio;

    /**
     * @var int
     *
     * @ORM\Column(name="mes", type="integer", nullable=false)
     */
    private $mes;

    /**
     * @var int
     *
     * @ORM\Column(name="anno", type="integer", nullable=false)
     */
    private $anno;

    /**
     * @var EquipoTecnologico
     *
     * @ORM\ManyToOne(targetEntity="EquipoTecnologico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="equipo_id", referencedColumnName="id")
     * })
     */
    private $equipo;

    /**
     * @var Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="operario", referencedColumnName="id")
     * })
     */
    private $operario;

    /**
     * @var Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="responsable", referencedColumnName="id")
     * })
     */
    private $responsable;

    /**
     * @var Area
     *
     * @ORM\ManyToOne(targetEntity="Area")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="area_id", referencedColumnName="id")
     * })
     */
    private $area;

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
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set folio.
     *
     * @param string|null $folio
     *
     * @return Anexo3Tecnologicos
     */
    public function setFolio($folio = null): Anexo3Tecnologicos
    {
        $this->folio = $folio;
    
        return $this;
    }

    /**
     * Get folio.
     *
     * @return string
     */
    public function getFolio(): ?string
    {
        return $this->folio;
    }

    /**
     * Set mes.
     *
     * @param int $mes
     *
     * @return Anexo3Tecnologicos
     */
    public function setMes($mes): Anexo3Tecnologicos
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
     * Set anno.
     *
     * @param int $anno
     *
     * @return Anexo3Tecnologicos
     */
    public function setAnno($anno): Anexo3Tecnologicos
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
     * Set equipo.
     *
     * @param EquipoTecnologico $equipo
     *
     * @return Anexo3Tecnologicos
     */
    public function setEquipo(EquipoTecnologico $equipo = null): Anexo3Tecnologicos
    {
        $this->equipo = $equipo;
    
        return $this;
    }

    /**
     * Get equipo.
     *
     * @return EquipoTecnologico
     */
    public function getEquipo(): EquipoTecnologico
    {
        return $this->equipo;
    }

    /**
     * Set operario.
     *
     * @param Persona $operario
     *
     * @return Anexo3Tecnologicos
     */
    public function setOperario(Persona $operario = null): Anexo3Tecnologicos
    {
        $this->operario = $operario;
    
        return $this;
    }

    /**
     * Get operario.
     *
     * @return Persona
     */
    public function getOperario(): Persona
    {
        return $this->operario;
    }

    /**
     * Set responsable.
     *
     * @param Persona $responsable
     *
     * @return Anexo3Tecnologicos
     */
    public function setResponsable(Persona $responsable = null): Anexo3Tecnologicos
    {
        $this->responsable = $responsable;
    
        return $this;
    }

    /**
     * Get responsable.
     *
     * @return Persona
     */
    public function getResponsable(): Persona
    {
        return $this->responsable;
    }

    /**
     * Set area.
     *
     * @param Area $area
     *
     * @return Anexo3Tecnologicos
     */
    public function setArea(Area $area = null): Anexo3Tecnologicos
    {
        $this->area = $area;
    
        return $this;
    }

    /**
     * Get area.
     *
     * @return Area
     */
    public function getArea(): Area
    {
        return $this->area;
    }

    /**
     * Set tipoCombustible.
     *
     * @param TipoCombustible $tipoCombustible
     *
     * @return Anexo3Tecnologicos
     */
    public function setTipoCombustible(TipoCombustible $tipoCombustible = null): Anexo3Tecnologicos
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
}
