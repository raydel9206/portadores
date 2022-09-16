<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GrupoElectrogenoGenerador
 *
 * @ORM\Table(name="nomencladores.grupos_electrogenos_generadores", indexes={@ORM\Index(name="IDX_B53D468DC3A9576E", columns={"modelo_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\GrupoElectrogenoGeneradorRepository")
 */
class GrupoElectrogenoGenerador
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
     * @ORM\Column(name="no_serie", type="string", nullable=false)
     */
    private $noSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="potencia_kva", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $potenciaKva;

    /**
     * @var string
     *
     * @ORM\Column(name="potencia_kw", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $potenciaKw;

    /**
     * @var string
     *
     * @ORM\Column(name="amperaje", type="decimal", precision=12, scale=2, nullable=false)
     */
    private $amperaje;

    /**
     * @var string
     *
     * @ORM\Column(name="reconexion_voltaje", type="string", nullable=false)
     */
    private $reconexionVoltaje;

    /**
     * @var ModeloTecnologico
     *
     * @ORM\ManyToOne(targetEntity="ModeloTecnologico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="modelo_id", referencedColumnName="id")
     * })
     */
    private $modelo;



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
     * Set noSerie.
     *
     * @param string $noSerie
     *
     * @return GrupoElectrogenoGenerador
     */
    public function setNoSerie($noSerie): GrupoElectrogenoGenerador
    {
        $this->noSerie = $noSerie;

        return $this;
    }

    /**
     * Get noSerie.
     *
     * @return string
     */
    public function getNoSerie(): string
    {
        return $this->noSerie;
    }

    /**
     * Set potenciaKva.
     *
     * @param string $potenciaKva
     *
     * @return GrupoElectrogenoGenerador
     */
    public function setPotenciaKva($potenciaKva): GrupoElectrogenoGenerador
    {
        $this->potenciaKva = $potenciaKva;
    
        return $this;
    }

    /**
     * Get potenciaKva.
     *
     * @return string
     */
    public function getPotenciaKva(): string
    {
        return $this->potenciaKva;
    }

    /**
     * Set potenciaKw.
     *
     * @param string $potenciaKw
     *
     * @return GrupoElectrogenoGenerador
     */
    public function setPotenciaKw($potenciaKw): GrupoElectrogenoGenerador
    {
        $this->potenciaKw = $potenciaKw;
    
        return $this;
    }

    /**
     * Get potenciaKw.
     *
     * @return string
     */
    public function getPotenciaKw(): string
    {
        return $this->potenciaKw;
    }

    /**
     * Set amperaje.
     *
     * @param string $amperaje
     *
     * @return GrupoElectrogenoGenerador
     */
    public function setAmperaje($amperaje): GrupoElectrogenoGenerador
    {
        $this->amperaje = $amperaje;
    
        return $this;
    }

    /**
     * Get amperaje.
     *
     * @return string
     */
    public function getAmperaje(): string
    {
        return $this->amperaje;
    }

    /**
     * Set reconexionVoltaje.
     *
     * @param string $reconexionVoltaje
     *
     * @return GrupoElectrogenoGenerador
     */
    public function setReconexionVoltaje($reconexionVoltaje): GrupoElectrogenoGenerador
    {
        $this->reconexionVoltaje = $reconexionVoltaje;
    
        return $this;
    }

    /**
     * Get reconexionVoltaje.
     *
     * @return string
     */
    public function getReconexionVoltaje(): string
    {
        return $this->reconexionVoltaje;
    }

    /**
     * Set modelo.
     *
     * @param ModeloTecnologico $modelo
     *
     * @return GrupoElectrogenoGenerador
     */
    public function setModelo(ModeloTecnologico $modelo = null): GrupoElectrogenoGenerador
    {
        $this->modelo = $modelo;
    
        return $this;
    }

    /**
     * Get modelo.
     *
     * @return ModeloTecnologico
     */
    public function getModelo(): ModeloTecnologico
    {
        return $this->modelo;
    }
}
