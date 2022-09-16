<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RecargasEliminadas
 *
 * @ORM\Table(name="datos.recargas_eliminadas", indexes={@ORM\Index(name="IDX_C4E894B0700D1EF6", columns={"id_tarjeta"})})
 * @ORM\Entity
 */
class RecargasEliminadas
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
     * @ORM\Column(name="fecha", type="datetime", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_recarga", type="decimal", precision=8, scale=3, nullable=false)
     */
    private $montoRecarga;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_restante", type="decimal", precision=8, scale=3, nullable=false)
     */
    private $montoRestante;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_usuario", type="string", length=255, nullable=true)
     */
    private $nombreUsuario;

    /**
     * @var string|null
     *
     * @ORM\Column(name="monto_recarga_litros", type="decimal", precision=10, scale=3, nullable=true)
     */
    private $montoRecargaLitros;

    /**
     * @var string|null
     *
     * @ORM\Column(name="monto_restante_litros", type="decimal", precision=10, scale=3, nullable=true)
     */
    private $montoRestanteLitros;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="restaurada", type="boolean", nullable=true)
     */
    private $restaurada = false;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nro_vale", type="string", nullable=true)
     */
    private $nroVale;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nro_factura", type="string", nullable=true)
     */
    private $nroFactura;

    /**
     * @var \Tarjeta
     *
     * @ORM\ManyToOne(targetEntity="Tarjeta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_tarjeta", referencedColumnName="id")
     * })
     */
    private $idTarjeta;



    /**
     * Get id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set fecha.
     *
     * @param \DateTime $fecha
     *
     * @return RecargasEliminadas
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    
        return $this;
    }

    /**
     * Get fecha.
     *
     * @return \DateTime
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * Set montoRecarga.
     *
     * @param string $montoRecarga
     *
     * @return RecargasEliminadas
     */
    public function setMontoRecarga($montoRecarga)
    {
        $this->montoRecarga = $montoRecarga;
    
        return $this;
    }

    /**
     * Get montoRecarga.
     *
     * @return string
     */
    public function getMontoRecarga()
    {
        return $this->montoRecarga;
    }

    /**
     * Set montoRestante.
     *
     * @param string $montoRestante
     *
     * @return RecargasEliminadas
     */
    public function setMontoRestante($montoRestante)
    {
        $this->montoRestante = $montoRestante;
    
        return $this;
    }

    /**
     * Get montoRestante.
     *
     * @return string
     */
    public function getMontoRestante()
    {
        return $this->montoRestante;
    }

    /**
     * Set nombreUsuario.
     *
     * @param string|null $nombreUsuario
     *
     * @return RecargasEliminadas
     */
    public function setNombreUsuario($nombreUsuario = null)
    {
        $this->nombreUsuario = $nombreUsuario;
    
        return $this;
    }

    /**
     * Get nombreUsuario.
     *
     * @return string|null
     */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    /**
     * Set montoRecargaLitros.
     *
     * @param string|null $montoRecargaLitros
     *
     * @return RecargasEliminadas
     */
    public function setMontoRecargaLitros($montoRecargaLitros = null)
    {
        $this->montoRecargaLitros = $montoRecargaLitros;
    
        return $this;
    }

    /**
     * Get montoRecargaLitros.
     *
     * @return string|null
     */
    public function getMontoRecargaLitros()
    {
        return $this->montoRecargaLitros;
    }

    /**
     * Set montoRestanteLitros.
     *
     * @param string|null $montoRestanteLitros
     *
     * @return RecargasEliminadas
     */
    public function setMontoRestanteLitros($montoRestanteLitros = null)
    {
        $this->montoRestanteLitros = $montoRestanteLitros;
    
        return $this;
    }

    /**
     * Get montoRestanteLitros.
     *
     * @return string|null
     */
    public function getMontoRestanteLitros()
    {
        return $this->montoRestanteLitros;
    }

    /**
     * Set restaurada.
     *
     * @param bool|null $restaurada
     *
     * @return RecargasEliminadas
     */
    public function setRestaurada($restaurada = null)
    {
        $this->restaurada = $restaurada;
    
        return $this;
    }

    /**
     * Get restaurada.
     *
     * @return bool|null
     */
    public function getRestaurada()
    {
        return $this->restaurada;
    }

    /**
     * Set nroVale.
     *
     * @param string|null $nroVale
     *
     * @return RecargasEliminadas
     */
    public function setNroVale($nroVale = null)
    {
        $this->nroVale = $nroVale;
    
        return $this;
    }

    /**
     * Get nroVale.
     *
     * @return string|null
     */
    public function getNroVale()
    {
        return $this->nroVale;
    }

    /**
     * Set nroFactura.
     *
     * @param string|null $nroFactura
     *
     * @return RecargasEliminadas
     */
    public function setNroFactura($nroFactura = null)
    {
        $this->nroFactura = $nroFactura;
    
        return $this;
    }

    /**
     * Get nroFactura.
     *
     * @return string|null
     */
    public function getNroFactura()
    {
        return $this->nroFactura;
    }

    /**
     * Set idTarjeta.
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta|null $idTarjeta
     *
     * @return RecargasEliminadas
     */
    public function setIdTarjeta(\Geocuba\PortadoresBundle\Entity\Tarjeta $idTarjeta = null)
    {
        $this->idTarjeta = $idTarjeta;
    
        return $this;
    }

    /**
     * Get idTarjeta.
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta|null
     */
    public function getIdTarjeta()
    {
        return $this->idTarjeta;
    }
}
