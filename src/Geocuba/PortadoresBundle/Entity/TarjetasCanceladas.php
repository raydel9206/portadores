<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TarjetasCanceladas
 *
 * @ORM\Table(name="datos.tarjetas_canceladas")
 * @ORM\Entity
 */
class TarjetasCanceladas
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
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_cancelacion", type="date", nullable=true)
     */
    private $fechaCancelacion;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_tarjeta", type="decimal", precision=22, scale=0, nullable=true)
     */
    private $nroTarjeta;

    /**
     * @var string
     *
     * @ORM\Column(name="motivo", type="string", nullable=true)
     */
    private $motivo;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", nullable=true)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="nunidad", type="string", nullable=true)
     */
    private $nunidad;

    /**
     * @var string
     *
     * @ORM\Column(name="saldo", type="decimal", precision=19, scale=2, nullable=true)
     */
    private $saldo;



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
     * Set fechaCancelacion
     *
     * @param \DateTime $fechaCancelacion
     *
     * @return TarjetasCanceladas
     */
    public function setFechaCancelacion($fechaCancelacion)
    {
        $this->fechaCancelacion = $fechaCancelacion;

        return $this;
    }

    /**
     * Get fechaCancelacion
     *
     * @return \DateTime
     */
    public function getFechaCancelacion()
    {
        return $this->fechaCancelacion;
    }

    /**
     * Set nroTarjeta
     *
     * @param string $nroTarjeta
     *
     * @return TarjetasCanceladas
     */
    public function setNroTarjeta($nroTarjeta)
    {
        $this->nroTarjeta = $nroTarjeta;

        return $this;
    }

    /**
     * Get nroTarjeta
     *
     * @return string
     */
    public function getNroTarjeta()
    {
        return $this->nroTarjeta;
    }

    /**
     * Set motivo
     *
     * @param string $motivo
     *
     * @return TarjetasCanceladas
     */
    public function setMotivo($motivo)
    {
        $this->motivo = $motivo;

        return $this;
    }

    /**
     * Get motivo
     *
     * @return string
     */
    public function getMotivo()
    {
        return $this->motivo;
    }

    /**
     * Set usuario
     *
     * @param string $usuario
     *
     * @return TarjetasCanceladas
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set nunidad
     *
     * @param string $nunidad
     *
     * @return TarjetasCanceladas
     */
    public function setNunidad($nunidad)
    {
        $this->nunidad = $nunidad;

        return $this;
    }

    /**
     * Get nunidad
     *
     * @return string
     */
    public function getNunidad()
    {
        return $this->nunidad;
    }

    /**
     * Set saldo
     *
     * @param string $saldo
     *
     * @return TarjetasCanceladas
     */
    public function setSaldo($saldo)
    {
        $this->saldo = $saldo;

        return $this;
    }

    /**
     * Get saldo
     *
     * @return string
     */
    public function getSaldo()
    {
        return $this->saldo;
    }
}
