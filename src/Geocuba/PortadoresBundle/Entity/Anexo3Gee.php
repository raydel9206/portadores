<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Anexo3Gee
 *
 * @ORM\Table(name="datos.anexo_3_gee", indexes={@ORM\Index(name="IDX_544ABE3F58BC1BE0", columns={"municipio_id"}), @ORM\Index(name="IDX_544ABE3F9D01464C", columns={"unidad_id"}), @ORM\Index(name="IDX_544ABE3F9C833003", columns={"grupo_id"})})
 * @ORM\Entity
 */
class Anexo3Gee
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
     * @ORM\Column(name="kva", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $kva;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cant_oper_sin_carga", type="integer", nullable=true)
     */
    private $cantOperSinCarga;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cant_oper_con_carga", type="integer", nullable=true)
     */
    private $cantOperConCarga;

    /**
     * @var string|null
     *
     * @ORM\Column(name="horas_sin_carga", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $horasSinCarga;

    /**
     * @var string|null
     *
     * @ORM\Column(name="horas_con_carga", type="decimal", precision=12, scale=2, nullable=true)
     */
    private $horasConCarga;

    /**
     * @var string|null
     *
     * @ORM\Column(name="energia_generada", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $energiaGenerada;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comb_consumido_sin_carga", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $combConsumidoSinCarga;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comb_consumido_con_carga", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $combConsumidoConCarga;

    /**
     * @var string|null
     *
     * @ORM\Column(name="comb_consumido_total", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $combConsumidoTotal;

    /**
     * @var string|null
     *
     * @ORM\Column(name="indice_consumo", type="decimal", precision=12, scale=4, nullable=true)
     */
    private $indiceConsumo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="indice_cargabilidad", type="decimal", precision=12, scale=4, nullable=true)
     */
    private $indiceCargabilidad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="porciento_cargabilidad", type="decimal", precision=20, scale=4, nullable=true)
     */
    private $porcientoCargabilidad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="quincena", type="string", length=7)
     */
    private $quincena;

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
     * @var Municipio
     *
     * @ORM\ManyToOne(targetEntity="Municipio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="municipio_id", referencedColumnName="id")
     * })
     */
    private $municipio;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    private $unidad;

    /**
     * @var GrupoElectrogeno
     *
     * @ORM\ManyToOne(targetEntity="GrupoElectrogeno")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="grupo_id", referencedColumnName="id")
     * })
     */
    private $grupo;



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
     * Set kva.
     *
     * @param string|null $kva
     *
     * @return Anexo3Gee
     */
    public function setKva($kva = null): Anexo3Gee
    {
        $this->kva = $kva;
    
        return $this;
    }

    /**
     * Get kva.
     *
     * @return string|null
     */
    public function getKva(): ?string
    {
        return $this->kva;
    }

    /**
     * Set cantOperSinCarga.
     *
     * @param int|null $cantOperSinCarga
     *
     * @return Anexo3Gee
     */
    public function setCantOperSinCarga($cantOperSinCarga = null): Anexo3Gee
    {
        $this->cantOperSinCarga = $cantOperSinCarga;
    
        return $this;
    }

    /**
     * Get cantOperSinCarga.
     *
     * @return int|null
     */
    public function getCantOperSinCarga(): ?int
    {
        return $this->cantOperSinCarga;
    }

    /**
     * Set cantOperConCarga.
     *
     * @param int|null $cantOperConCarga
     *
     * @return Anexo3Gee
     */
    public function setCantOperConCarga($cantOperConCarga = null): Anexo3Gee
    {
        $this->cantOperConCarga = $cantOperConCarga;
    
        return $this;
    }

    /**
     * Get cantOperConCarga.
     *
     * @return int|null
     */
    public function getCantOperConCarga(): ?int
    {
        return $this->cantOperConCarga;
    }

    /**
     * Set horasSinCarga.
     *
     * @param string|null $horasSinCarga
     *
     * @return Anexo3Gee
     */
    public function setHorasSinCarga($horasSinCarga = null): Anexo3Gee
    {
        $this->horasSinCarga = $horasSinCarga;
    
        return $this;
    }

    /**
     * Get horasSinCarga.
     *
     * @return string|null
     */
    public function getHorasSinCarga(): ?string
    {
        return $this->horasSinCarga;
    }

    /**
     * Set horasConCarga.
     *
     * @param string|null $horasConCarga
     *
     * @return Anexo3Gee
     */
    public function setHorasConCarga($horasConCarga = null): Anexo3Gee
    {
        $this->horasConCarga = $horasConCarga;
    
        return $this;
    }

    /**
     * Get horasConCarga.
     *
     * @return string|null
     */
    public function getHorasConCarga(): ?string
    {
        return $this->horasConCarga;
    }

    /**
     * Set energiaGenerada.
     *
     * @param string|null $energiaGenerada
     *
     * @return Anexo3Gee
     */
    public function setEnergiaGenerada($energiaGenerada = null): Anexo3Gee
    {
        $this->energiaGenerada = $energiaGenerada;
    
        return $this;
    }

    /**
     * Get energiaGenerada.
     *
     * @return string|null
     */
    public function getEnergiaGenerada(): ?string
    {
        return $this->energiaGenerada;
    }

    /**
     * Set combConsumidoSinCarga.
     *
     * @param string|null $combConsumidoSinCarga
     *
     * @return Anexo3Gee
     */
    public function setCombConsumidoSinCarga($combConsumidoSinCarga = null): Anexo3Gee
    {
        $this->combConsumidoSinCarga = $combConsumidoSinCarga;
    
        return $this;
    }

    /**
     * Get combConsumidoSinCarga.
     *
     * @return string|null
     */
    public function getCombConsumidoSinCarga(): ?string
    {
        return $this->combConsumidoSinCarga;
    }

    /**
     * Set combConsumidoConCarga.
     *
     * @param string|null $combConsumidoConCarga
     *
     * @return Anexo3Gee
     */
    public function setCombConsumidoConCarga($combConsumidoConCarga = null): Anexo3Gee
    {
        $this->combConsumidoConCarga = $combConsumidoConCarga;
    
        return $this;
    }

    /**
     * Get combConsumidoConCarga.
     *
     * @return string|null
     */
    public function getCombConsumidoConCarga(): ?string
    {
        return $this->combConsumidoConCarga;
    }

    /**
     * Set combConsumidoTotal.
     *
     * @param string|null $combConsumidoTotal
     *
     * @return Anexo3Gee
     */
    public function setCombConsumidoTotal($combConsumidoTotal = null): Anexo3Gee
    {
        $this->combConsumidoTotal = $combConsumidoTotal;
    
        return $this;
    }

    /**
     * Get combConsumidoTotal.
     *
     * @return string|null
     */
    public function getCombConsumidoTotal(): ?string
    {
        return $this->combConsumidoTotal;
    }

    /**
     * Set indiceConsumo.
     *
     * @param string|null $indiceConsumo
     *
     * @return Anexo3Gee
     */
    public function setIndiceConsumo($indiceConsumo = null): Anexo3Gee
    {
        $this->indiceConsumo = $indiceConsumo;
    
        return $this;
    }

    /**
     * Get indiceConsumo.
     *
     * @return string|null
     */
    public function getIndiceConsumo(): ?string
    {
        return $this->indiceConsumo;
    }

    /**
     * Set indiceCargabilidad.
     *
     * @param string|null $indiceCargabilidad
     *
     * @return Anexo3Gee
     */
    public function setIndiceCargabilidad($indiceCargabilidad = null): Anexo3Gee
    {
        $this->indiceCargabilidad = $indiceCargabilidad;
    
        return $this;
    }

    /**
     * Get indiceCargabilidad.
     *
     * @return string|null
     */
    public function getIndiceCargabilidad(): ?string
    {
        return $this->indiceCargabilidad;
    }

    /**
     * Set porcientoCargabilidad.
     *
     * @param string|null $porcientoCargabilidad
     *
     * @return Anexo3Gee
     */
    public function setPorcientoCargabilidad($porcientoCargabilidad = null): Anexo3Gee
    {
        $this->porcientoCargabilidad = $porcientoCargabilidad;
    
        return $this;
    }

    /**
     * Get porcientoCargabilidad.
     *
     * @return string|null
     */
    public function getPorcientoCargabilidad(): ?string
    {
        return $this->porcientoCargabilidad;
    }

    /**
     * Set quincena.
     *
     * @param string $quincena
     *
     * @return Anexo3Gee
     */
    public function setQuincena($quincena): Anexo3Gee
    {
        $this->quincena = $quincena;

        return $this;
    }

    /**
     * Get quincena.
     *
     * @return string
     */
    public function getQuincena(): string
    {
        return $this->quincena;
    }

    /**
     * Set mes.
     *
     * @param int $mes
     *
     * @return Anexo3Gee
     */
    public function setMes($mes): Anexo3Gee
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
     * @return Anexo3Gee
     */
    public function setAnno($anno): Anexo3Gee
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
     * Set municipio.
     *
     * @param Municipio|null $municipio
     *
     * @return Anexo3Gee
     */
    public function setMunicipio(Municipio $municipio = null): Anexo3Gee
    {
        $this->municipio = $municipio;
    
        return $this;
    }

    /**
     * Get municipio.
     *
     * @return Municipio|null
     */
    public function getMunicipio(): ?Municipio
    {
        return $this->municipio;
    }

    /**
     * Set unidad.
     *
     * @param Unidad|null $unidad
     *
     * @return Anexo3Gee
     */
    public function setUnidad(Unidad $unidad = null): Anexo3Gee
    {
        $this->unidad = $unidad;
    
        return $this;
    }

    /**
     * Get unidad.
     *
     * @return Unidad|null
     */
    public function getUnidad(): ?Unidad
    {
        return $this->unidad;
    }

    /**
     * Set grupo.
     *
     * @param GrupoElectrogeno|null $grupo
     *
     * @return Anexo3Gee
     */
    public function setGrupo(GrupoElectrogeno $grupo = null): Anexo3Gee
    {
        $this->grupo = $grupo;
    
        return $this;
    }

    /**
     * Get grupo.
     *
     * @return GrupoElectrogeno|null
     */
    public function getGrupo(): ?GrupoElectrogeno
    {
        return $this->grupo;
    }
}
