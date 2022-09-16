<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistorialContableRecarga
 *
 * @ORM\Table(name="datos.historial_contable_recarga", indexes={@ORM\Index(name="IDX_E6CAD226700D1EF6", columns={"id_tarjeta"})})
 * @ORM\Entity
 */
class HistorialContableRecarga
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
     * @ORM\Column(name="monto_recarga", type="decimal", precision=20, scale=3, nullable=false)
     */
    private $montoRecarga;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_restante", type="decimal", precision=20, scale=3, nullable=false)
     */
    private $montoRestante;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_usuario", type="string", length=255, nullable=true)
     */
    private $nombreUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_recarga_litros", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $montoRecargaLitros;

    /**
     * @var string
     *
     * @ORM\Column(name="monto_restante_litros", type="decimal", precision=20, scale=3, nullable=true)
     */
    private $montoRestanteLitros;

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
     * @return HistorialContableRecarga
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
     * Set montoRecarga
     *
     * @param string $montoRecarga
     *
     * @return HistorialContableRecarga
     */
    public function setMontoRecarga($montoRecarga)
    {
        $this->montoRecarga = $montoRecarga;

        return $this;
    }

    /**
     * Get montoRecarga
     *
     * @return string
     */
    public function getMontoRecarga()
    {
        return $this->montoRecarga;
    }

    /**
     * Set montoRestante
     *
     * @param string $montoRestante
     *
     * @return HistorialContableRecarga
     */
    public function setMontoRestante($montoRestante)
    {
        $this->montoRestante = $montoRestante;

        return $this;
    }

    /**
     * Get montoRestante
     *
     * @return string
     */
    public function getMontoRestante()
    {
        return $this->montoRestante;
    }

    /**
     * Set nombreUsuario
     *
     * @param string $nombreUsuario
     *
     * @return HistorialContableRecarga
     */
    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;

        return $this;
    }

    /**
     * Get nombreUsuario
     *
     * @return string
     */
    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    /**
     * Set montoRecargaLitros
     *
     * @param string $montoRecargaLitros
     *
     * @return HistorialContableRecarga
     */
    public function setMontoRecargaLitros($montoRecargaLitros)
    {
        $this->montoRecargaLitros = $montoRecargaLitros;

        return $this;
    }

    /**
     * Get montoRecargaLitros
     *
     * @return string
     */
    public function getMontoRecargaLitros()
    {
        return $this->montoRecargaLitros;
    }

    /**
     * Set montoRestanteLitros
     *
     * @param string $montoRestanteLitros
     *
     * @return HistorialContableRecarga
     */
    public function setMontoRestanteLitros($montoRestanteLitros)
    {
        $this->montoRestanteLitros = $montoRestanteLitros;

        return $this;
    }

    /**
     * Get montoRestanteLitros
     *
     * @return string
     */
    public function getMontoRestanteLitros()
    {
        return $this->montoRestanteLitros;
    }

    /**
     * Set idTarjeta
     *
     * @param \Geocuba\PortadoresBundle\Entity\Tarjeta $idTarjeta
     *
     * @return HistorialContableRecarga
     */
    public function setIdTarjeta(\Geocuba\PortadoresBundle\Entity\Tarjeta $idTarjeta = null)
    {
        $this->idTarjeta = $idTarjeta;

        return $this;
    }

    /**
     * Get idTarjeta
     *
     * @return \Geocuba\PortadoresBundle\Entity\Tarjeta
     */
    public function getIdTarjeta()
    {
        return $this->idTarjeta;
    }
}
