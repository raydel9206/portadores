<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Caldera
 *
 * @ORM\Table(name="nomencladores.calderas", indexes={@ORM\Index(name="IDX_F5111E6A261DACAA", columns={"tipo_combustible_recirculacion_id"})})
 * @ORM\Entity
 */
class Caldera extends EquipoTecnologico
{
    /**
     * @var string
     *
     * @ORM\Column(name="norma_recirculacion", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $normaRecirculacion = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="norma_recirculacion_fabricante", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $normaRecirculacionFabricante = '0';

    /**
     * @var EquipoTecnologico
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="EquipoTecnologico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    protected $id;

    /**
     * @var TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible_recirculacion_id", referencedColumnName="id")
     * })
     */
    private $tipoCombustibleRecirculacion;



    /**
     * Set normaRecirculacion.
     *
     * @param string $normaRecirculacion
     *
     * @return Caldera
     */
    public function setNormaRecirculacion($normaRecirculacion): Caldera
    {
        $this->normaRecirculacion = $normaRecirculacion;
    
        return $this;
    }

    /**
     * Get normaRecirculacion.
     *
     * @return string
     */
    public function getNormaRecirculacion(): string
    {
        return $this->normaRecirculacion;
    }

    /**
     * Set normaRecirculacionFabricante.
     *
     * @param string $normaRecirculacionFabricante
     *
     * @return Caldera
     */
    public function setNormaRecirculacionFabricante($normaRecirculacionFabricante): Caldera
    {
        $this->normaRecirculacionFabricante = $normaRecirculacionFabricante;
    
        return $this;
    }

    /**
     * Get normaRecirculacionFabricante.
     *
     * @return string
     */
    public function getNormaRecirculacionFabricante(): string
    {
        return $this->normaRecirculacionFabricante;
    }

    /**
     * Set tipoCombustibleRecirculacion.
     *
     * @param TipoCombustible $tipoCombustibleRecirculacion
     *
     * @return Caldera
     */
    public function setTipoCombustibleRecirculacion(TipoCombustible $tipoCombustibleRecirculacion): Caldera
    {
        $this->tipoCombustibleRecirculacion = $tipoCombustibleRecirculacion;
    
        return $this;
    }

    /**
     * Get tipoCombustibleRecirculacion.
     *
     * @return TipoCombustible
     */
    public function getTipoCombustibleRecirculacion(): TipoCombustible
    {
        return $this->tipoCombustibleRecirculacion;
    }
}
