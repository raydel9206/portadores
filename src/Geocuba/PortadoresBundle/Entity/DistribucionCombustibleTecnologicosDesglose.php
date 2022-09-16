<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DistribucionCombustibleTecnologicosDesglose
 *
 * @ORM\Table(name="datos.distribucion_combustible_tecnologicos_desglose", indexes={@ORM\Index(name="IDX_AF07EEE19EB4512C", columns={"distribucion_combustible_tecnologicos_id"}), @ORM\Index(name="IDX_AF07EEE123BFBED", columns={"equipo_id"})})
 * @ORM\Entity
 */
class DistribucionCombustibleTecnologicosDesglose
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
     * @var string
     *
     * @ORM\Column(name="cantidad", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $cantidad;

    /**
     * @var string
     *
     * @ORM\Column(name="indice_consumo", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $indiceConsumo;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_comb", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $precioComb;

    /**
     * @var DistribucionCombustibleTecnologicos
     *
     * @ORM\ManyToOne(targetEntity="DistribucionCombustibleTecnologicos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="distribucion_combustible_tecnologicos_id", referencedColumnName="id")
     * })
     */
    private $distribucionCombustibleTecnologicos;

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
     * Get id.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Set cantidad.
     *
     * @param string $cantidad
     *
     * @return DistribucionCombustibleTecnologicosDesglose
     */
    public function setCantidad($cantidad): DistribucionCombustibleTecnologicosDesglose
    {
        $this->cantidad = $cantidad;
    
        return $this;
    }

    /**
     * Get cantidad.
     *
     * @return string
     */
    public function getCantidad(): string
    {
        return $this->cantidad;
    }

    /**
     * Set indiceConsumo.
     *
     * @param string $indiceConsumo
     *
     * @return DistribucionCombustibleTecnologicosDesglose
     */
    public function setIndiceConsumo($indiceConsumo): DistribucionCombustibleTecnologicosDesglose
    {
        $this->indiceConsumo = $indiceConsumo;
    
        return $this;
    }

    /**
     * Get indiceConsumo.
     *
     * @return string
     */
    public function getIndiceConsumo(): string
    {
        return $this->indiceConsumo;
    }

    /**
     * Set precioComb.
     *
     * @param string $precioComb
     *
     * @return DistribucionCombustibleTecnologicosDesglose
     */
    public function setPrecioComb($precioComb): DistribucionCombustibleTecnologicosDesglose
    {
        $this->precioComb = $precioComb;
    
        return $this;
    }

    /**
     * Get precioComb.
     *
     * @return string
     */
    public function getPrecioComb(): string
    {
        return $this->precioComb;
    }

    /**
     * Set distribucionCombustibleTecnologicos.
     *
     * @param DistribucionCombustibleTecnologicos $distribucionCombustibleTecnologicos
     *
     * @return DistribucionCombustibleTecnologicosDesglose
     */
    public function setDistribucionCombustibleTecnologicos(DistribucionCombustibleTecnologicos $distribucionCombustibleTecnologicos): DistribucionCombustibleTecnologicosDesglose
    {
        $this->distribucionCombustibleTecnologicos = $distribucionCombustibleTecnologicos;
    
        return $this;
    }

    /**
     * Get distribucionCombustibleTecnologicos.
     *
     * @return DistribucionCombustibleTecnologicos
     */
    public function getDistribucionCombustibleTecnologicos(): DistribucionCombustibleTecnologicos
    {
        return $this->distribucionCombustibleTecnologicos;
    }

    /**
     * Set equipo.
     *
     * @param EquipoTecnologico $equipo
     *
     * @return DistribucionCombustibleTecnologicosDesglose
     */
    public function setEquipo(EquipoTecnologico $equipo): DistribucionCombustibleTecnologicosDesglose
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
}
