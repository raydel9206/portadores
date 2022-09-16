<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GrupoElectrogeno
 *
 * @ORM\Table(name="nomencladores.grupos_electrogenos", indexes={@ORM\Index(name="IDX_8DC6DC309D01464C", columns={"unidad_id"}), @ORM\Index(name="IDX_8DC6DC3058BC1BE0", columns={"municipio_id"}), @ORM\Index(name="IDX_8DC6DC30C3A9576E", columns={"modelo_id"}), @ORM\Index(name="IDX_8DC6DC30CCC381E3", columns={"generador_id"}), @ORM\Index(name="IDX_8DC6DC3080D58D71", columns={"motor_id"})})
 * @ORM\Entity
 */
class GrupoElectrogeno extends EquipoTecnologico
{
    /**
     * @var string
     *
     * @ORM\Column(name="no_serie", type="string", nullable=false)
     */
    private $noSerie;

    /**
     * @var string
     *
     * @ORM\Column(name="propietario", type="string", nullable=false)
     */
    private $propietario;

    /**
     * @var int
     *
     * @ORM\Column(name="anno_fabricacion", type="integer", nullable=false)
     */
    private $annoFabricacion;

    /**
     * @var string
     *
     * @ORM\Column(name="localidad", type="string", nullable=false)
     */
    private $localidad;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", nullable=false)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="transferencial", type="string", nullable=false)
     */
    private $transferencial;

    /**
     * @var string
     *
     * @ORM\Column(name="conexion", type="string", nullable=false)
     */
    private $conexion;

    /**
     * @var int
     *
     * @ORM\Column(name="voltaje_salida", type="integer", nullable=false)
     */
    private $voltajeSalida;

    /**
     * @var int
     *
     * @ORM\Column(name="voltaje_neutro", type="integer", nullable=false)
     */
    private $voltajeNeutro;

    /**
     * @var int
     *
     * @ORM\Column(name="capacidad_kva", type="integer", nullable=false)
     */
    private $capacidadKva;

    /**
     * @var string
     *
     * @ORM\Column(name="respaldo_brinda", type="string", nullable=false)
     */
    private $respaldoBrinda;

    /**
     * @var bool
     *
     * @ORM\Column(name="analizador_redes", type="boolean", nullable=false)
     */
    private $analizadorRedes;

    /**
     * @var bool
     *
     * @ORM\Column(name="sincronizado_sen", type="boolean", nullable=false)
     */
    private $sincronizadoSen;

    /**
     * @var string
     *
     * @ORM\Column(name="estado_tecnico", type="string", nullable=false)
     */
    private $estadoTecnico;

    /**
     * @var string
     *
     * @ORM\Column(name="entidad", type="string", nullable=false)
     */
    private $entidad;

    /**
     * @var string
     *
     * @ORM\Column(name="cap_tanque_int", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $capTanqueInt;

    /**
     * @var string
     *
     * @ORM\Column(name="indice_cargabilidad", type="decimal", precision=12, scale=4, nullable=false)
     */
    private $indiceCargabilidad;

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
     * @var GrupoElectrogenoGenerador
     *
     * @ORM\ManyToOne(targetEntity="GrupoElectrogenoGenerador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="generador_id", referencedColumnName="id")
     * })
     */
    private $generador;

    /**
     * @var GrupoElectrogenoMotor
     *
     * @ORM\ManyToOne(targetEntity="GrupoElectrogenoMotor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="motor_id", referencedColumnName="id")
     * })
     */
    private $motor;

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
     * Set noSerie.
     *
     * @param string $noSerie
     *
     * @return GrupoElectrogeno
     */
    public function setNoSerie($noSerie): GrupoElectrogeno
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
     * Set propietario.
     *
     * @param string $propietario
     *
     * @return GrupoElectrogeno
     */
    public function setPropietario($propietario): GrupoElectrogeno
    {
        $this->propietario = $propietario;
    
        return $this;
    }

    /**
     * Get propietario.
     *
     * @return string
     */
    public function getPropietario(): string
    {
        return $this->propietario;
    }

    /**
     * Set annoFabricacion.
     *
     * @param int $annoFabricacion
     *
     * @return GrupoElectrogeno
     */
    public function setAnnoFabricacion($annoFabricacion): GrupoElectrogeno
    {
        $this->annoFabricacion = $annoFabricacion;
    
        return $this;
    }

    /**
     * Get annoFabricacion.
     *
     * @return int
     */
    public function getAnnoFabricacion(): int
    {
        return $this->annoFabricacion;
    }

    /**
     * Set localidad.
     *
     * @param string $localidad
     *
     * @return GrupoElectrogeno
     */
    public function setLocalidad($localidad): GrupoElectrogeno
    {
        $this->localidad = $localidad;
    
        return $this;
    }

    /**
     * Get localidad.
     *
     * @return string
     */
    public function getLocalidad(): string
    {
        return $this->localidad;
    }

    /**
     * Set direccion.
     *
     * @param string $direccion
     *
     * @return GrupoElectrogeno
     */
    public function setDireccion($direccion): GrupoElectrogeno
    {
        $this->direccion = $direccion;
    
        return $this;
    }

    /**
     * Get direccion.
     *
     * @return string
     */
    public function getDireccion(): string
    {
        return $this->direccion;
    }

    /**
     * Set transferencial.
     *
     * @param string $transferencial
     *
     * @return GrupoElectrogeno
     */
    public function setTransferencial($transferencial): GrupoElectrogeno
    {
        $this->transferencial = $transferencial;
    
        return $this;
    }

    /**
     * Get transferencial.
     *
     * @return string
     */
    public function getTransferencial(): string
    {
        return $this->transferencial;
    }

    /**
     * Set conexion.
     *
     * @param string $conexion
     *
     * @return GrupoElectrogeno
     */
    public function setConexion($conexion): GrupoElectrogeno
    {
        $this->conexion = $conexion;
    
        return $this;
    }

    /**
     * Get conexion.
     *
     * @return string
     */
    public function getConexion(): string
    {
        return $this->conexion;
    }

    /**
     * Set voltajeSalida.
     *
     * @param int $voltajeSalida
     *
     * @return GrupoElectrogeno
     */
    public function setVoltajeSalida($voltajeSalida): GrupoElectrogeno
    {
        $this->voltajeSalida = $voltajeSalida;
    
        return $this;
    }

    /**
     * Get voltajeSalida.
     *
     * @return int
     */
    public function getVoltajeSalida(): int
    {
        return $this->voltajeSalida;
    }

    /**
     * Set voltajeNeutro.
     *
     * @param int $voltajeNeutro
     *
     * @return GrupoElectrogeno
     */
    public function setVoltajeNeutro($voltajeNeutro): GrupoElectrogeno
    {
        $this->voltajeNeutro = $voltajeNeutro;
    
        return $this;
    }

    /**
     * Get voltajeNeutro.
     *
     * @return int
     */
    public function getVoltajeNeutro(): int
    {
        return $this->voltajeNeutro;
    }

    /**
     * Set capacidadKva.
     *
     * @param int $capacidadKva
     *
     * @return GrupoElectrogeno
     */
    public function setCapacidadKva($capacidadKva): GrupoElectrogeno
    {
        $this->capacidadKva = $capacidadKva;
    
        return $this;
    }

    /**
     * Get capacidadKva.
     *
     * @return int
     */
    public function getCapacidadKva(): int
    {
        return $this->capacidadKva;
    }

    /**
     * Set respaldoBrinda.
     *
     * @param string $respaldoBrinda
     *
     * @return GrupoElectrogeno
     */
    public function setRespaldoBrinda($respaldoBrinda): GrupoElectrogeno
    {
        $this->respaldoBrinda = $respaldoBrinda;
    
        return $this;
    }

    /**
     * Get respaldoBrinda.
     *
     * @return string
     */
    public function getRespaldoBrinda(): string
    {
        return $this->respaldoBrinda;
    }

    /**
     * Set analizadorRedes.
     *
     * @param bool $analizadorRedes
     *
     * @return GrupoElectrogeno
     */
    public function setAnalizadorRedes($analizadorRedes): GrupoElectrogeno
    {
        $this->analizadorRedes = $analizadorRedes;
    
        return $this;
    }

    /**
     * Get analizadorRedes.
     *
     * @return bool
     */
    public function getAnalizadorRedes(): bool
    {
        return $this->analizadorRedes;
    }

    /**
     * Set sincronizadoSen.
     *
     * @param bool $sincronizadoSen
     *
     * @return GrupoElectrogeno
     */
    public function setSincronizadoSen($sincronizadoSen): GrupoElectrogeno
    {
        $this->sincronizadoSen = $sincronizadoSen;
    
        return $this;
    }

    /**
     * Get sincronizadoSen.
     *
     * @return bool
     */
    public function getSincronizadoSen(): bool
    {
        return $this->sincronizadoSen;
    }

    /**
     * Set estadoTecnico.
     *
     * @param string $estadoTecnico
     *
     * @return GrupoElectrogeno
     */
    public function setEstadoTecnico($estadoTecnico): GrupoElectrogeno
    {
        $this->estadoTecnico = $estadoTecnico;
    
        return $this;
    }

    /**
     * Get estadoTecnico.
     *
     * @return string
     */
    public function getEstadoTecnico(): string
    {
        return $this->estadoTecnico;
    }

    /**
     * Set entidad.
     *
     * @param string $entidad
     *
     * @return GrupoElectrogeno
     */
    public function setEntidad($entidad): GrupoElectrogeno
    {
        $this->entidad = $entidad;
    
        return $this;
    }

    /**
     * Get entidad.
     *
     * @return string
     */
    public function getEntidad(): string
    {
        return $this->entidad;
    }

    /**
     * Set captanqueint.
     *
     * @param string $captanqueint
     *
     * @return GrupoElectrogeno
     */
    public function setCapTanqueInt($captanqueint): GrupoElectrogeno
    {
        $this->capTanqueInt = $captanqueint;
    
        return $this;
    }

    /**
     * Get captanqueint.
     *
     * @return string
     */
    public function getCapTanqueInt(): string
    {
        return $this->capTanqueInt;
    }

    /**
     * Set indiceCargabilidad.
     *
     * @param string $indiceCargabilidad
     *
     * @return GrupoElectrogeno
     */
    public function setIndiceCargabilidad($indiceCargabilidad): GrupoElectrogeno
    {
        $this->indiceCargabilidad = $indiceCargabilidad;
    
        return $this;
    }

    /**
     * Get indiceCargabilidad.
     *
     * @return string
     */
    public function getIndiceCargabilidad(): string
    {
        return $this->indiceCargabilidad;
    }

    /**
     * Set municipio.
     *
     * @param Municipio $municipio
     *
     * @return GrupoElectrogeno
     */
    public function setMunicipio(Municipio $municipio): GrupoElectrogeno
    {
        $this->municipio = $municipio;
    
        return $this;
    }

    /**
     * Get municipio.
     *
     * @return Municipio
     */
    public function getMunicipio(): Municipio
    {
        return $this->municipio;
    }

    /**
     * Set generador.
     *
     * @param GrupoElectrogenoGenerador $generador
     *
     * @return GrupoElectrogeno
     */
    public function setGenerador(GrupoElectrogenoGenerador $generador): GrupoElectrogeno
    {
        $this->generador = $generador;
    
        return $this;
    }

    /**
     * Get generador.
     *
     * @return GrupoElectrogenoGenerador
     */
    public function getGenerador(): GrupoElectrogenoGenerador
    {
        return $this->generador;
    }

    /**
     * Set motor.
     *
     * @param GrupoElectrogenoMotor|null $motor
     *
     * @return GrupoElectrogeno
     */
    public function setMotor(GrupoElectrogenoMotor $motor): GrupoElectrogeno
    {
        $this->motor = $motor;
    
        return $this;
    }

    /**
     * Get motor.
     *
     * @return GrupoElectrogenoMotor
     */
    public function getMotor(): GrupoElectrogenoMotor
    {
        return $this->motor;
    }
}
