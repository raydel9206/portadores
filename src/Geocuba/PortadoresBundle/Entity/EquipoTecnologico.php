<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EquipoTecnologico
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"equipoTecnologico" = "EquipoTecnologico", "caldera" = "Caldera", "grupo_electrogeno" = "GrupoElectrogeno"})
 *
 * @ORM\Table(name="nomencladores.equipos_tecnologicos", indexes={@ORM\Index(name="IDX_904DF90A6014FACA", columns={"actividad_id"}), @ORM\Index(name="IDX_904DF90A596E597F", columns={"tipo_combustible_id"}), @ORM\Index(name="IDX_904DF90AB8C97C75", columns={"modelo_tecnologico_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\EquipoTecnologicoRepository")
 */
class EquipoTecnologico
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", nullable=false)
     */
    protected $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_inventario", type="string", nullable=false)
     */
    protected $nroInventario;

    /**
     * @var string
     *
     * @ORM\Column(name="norma", type="decimal", precision=12, scale=4, nullable=false)
     */
    protected $norma;

    /**
     * @var string
     *
     * @ORM\Column(name="norma_fabricante", type="decimal", precision=12, scale=4, nullable=false)
     */
    protected $normaFabricante;

    /**
     * @var Actividad
     *
     * @ORM\ManyToOne(targetEntity="Actividad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="actividad_id", referencedColumnName="id")
     * })
     */
    protected $actividad;

    /**
     * @var TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible_id", referencedColumnName="id")
     * })
     */
    protected $tipoCombustible;

    /**
     * @var ModeloTecnologico
     *
     * @ORM\ManyToOne(targetEntity="ModeloTecnologico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modelo_tecnologico_id", referencedColumnName="id")
     * })
     */
    protected $modeloTecnologico;

    /**
     * @var DenominacionTecnologica
     *
     * @ORM\ManyToOne(targetEntity="DenominacionTecnologica")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="denominacion_tecnologica_id", referencedColumnName="id")
     * })
     */
    protected $denominacionTecnologica;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    protected $unidad;


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
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return EquipoTecnologico
     */
    public function setDescripcion($descripcion): EquipoTecnologico
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * Set nroInventario.
     *
     * @param string $nroInventario
     *
     * @return EquipoTecnologico
     */
    public function setNroInventario($nroInventario): EquipoTecnologico
    {
        $this->nroInventario = $nroInventario;
    
        return $this;
    }

    /**
     * Get nroInventario.
     *
     * @return string
     */
    public function getNroInventario(): string
    {
        return $this->nroInventario;
    }

    /**
     * Set norma.
     *
     * @param string $norma
     *
     * @return EquipoTecnologico
     */
    public function setNorma($norma): EquipoTecnologico
    {
        $this->norma = $norma;
    
        return $this;
    }

    /**
     * Get norma.
     *
     * @return string
     */
    public function getNorma(): string
    {
        return $this->norma;
    }

    /**
     * Set normaFabricante.
     *
     * @param string $normaFabricante
     *
     * @return EquipoTecnologico
     */
    public function setNormaFabricante($normaFabricante): EquipoTecnologico
    {
        $this->normaFabricante = $normaFabricante;
    
        return $this;
    }

    /**
     * Get normaFabricante.
     *
     * @return string
     */
    public function getNormaFabricante(): string
    {
        return $this->normaFabricante;
    }

    /**
     * Set actividad.
     *
     * @param Actividad $actividad
     *
     * @return EquipoTecnologico
     */
    public function setActividad(Actividad $actividad): EquipoTecnologico
    {
        $this->actividad = $actividad;
    
        return $this;
    }

    /**
     * Get actividad.
     *
     * @return Actividad
     */
    public function getActividad(): Actividad
    {
        return $this->actividad;
    }

    /**
     * Set tipoCombustible.
     *
     * @param TipoCombustible $tipoCombustible
     *
     * @return EquipoTecnologico
     */
    public function setTipoCombustible(TipoCombustible $tipoCombustible): EquipoTecnologico
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
     * Set modeloTecnologico.
     *
     * @param ModeloTecnologico $modeloTecnologico
     *
     * @return EquipoTecnologico
     */
    public function setModeloTecnologico(ModeloTecnologico $modeloTecnologico): EquipoTecnologico
    {
        $this->modeloTecnologico = $modeloTecnologico;
    
        return $this;
    }

    /**
     * Get modeloTecnologico.
     *
     * @return ModeloTecnologico
     */
    public function getModeloTecnologico(): ModeloTecnologico
    {
        return $this->modeloTecnologico;
    }

    /**
     * Set denominacionTecnologica.
     *
     * @param DenominacionTecnologica $denominacionTecnologica
     *
     * @return EquipoTecnologico
     */
    public function setDenominacionTecnologica(DenominacionTecnologica $denominacionTecnologica): EquipoTecnologico
    {
        $this->denominacionTecnologica = $denominacionTecnologica;

        return $this;
    }

    /**
     * Get denominacionTecnologica.
     *
     * @return DenominacionTecnologica
     */
    public function getDenominacionTecnologica(): DenominacionTecnologica
    {
        return $this->denominacionTecnologica;
    }

    /**
     * Set unidad.
     *
     * @param Unidad $unidad
     *
     * @return EquipoTecnologico
     */
    public function setUnidad(Unidad $unidad): EquipoTecnologico
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad.
     *
     * @return Unidad
     */
    public function getUnidad(): Unidad
    {
        return $this->unidad;
    }
}
