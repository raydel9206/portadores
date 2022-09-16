<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistroCombustiblePlanificacion
 *
 * @ORM\Table(name="datos.registro_combustible_planificacion", indexes={@ORM\Index(name="IDX_EE966ECE33AF74F4", columns={"monedaid"}), @ORM\Index(name="IDX_EE966ECE8D29C97A", columns={"registro_combustible_id"})})
 * @ORM\Entity
 */
class RegistroCombustiblePlanificacion
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="string", length=150, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Geocuba\AdminBundle\Util\CustomIdGenerator")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=true)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="recibido", type="decimal", precision=8, scale=2, nullable=true)
     */
    private $recibido = '0.00';

//    /**
//     * @var string
//     *
//     * @ORM\Column(name="saldo", type="decimal", precision=8, scale=2, nullable=true)
//     */
//    private $saldo = '0.00';

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @var Moneda
     *
     * @ORM\ManyToOne(targetEntity="Moneda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="monedaid", referencedColumnName="id")
     * })
     */
    private $monedaid;

    /**
     * @var RegistroCombustible
     *
     * @ORM\ManyToOne(targetEntity="RegistroCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="registro_combustible_id", referencedColumnName="id")
     * })
     */
    private $registroCombustible;



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
     * @return RegistroCombustiblePlanificacion
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
     * Set recibido
     *
     * @param string $recibido
     *
     * @return RegistroCombustiblePlanificacion
     */
    public function setRecibido($recibido)
    {
        $this->recibido = $recibido;

        return $this;
    }

    /**
     * Get recibido
     *
     * @return string
     */
    public function getRecibido()
    {
        return $this->recibido;
    }

//    /**
//     * Set saldo
//     *
//     * @param string $saldo
//     *
//     * @return RegistroCombustiblePlanificacion
//     */
//    public function setSaldo($saldo)
//    {
//        $this->saldo = $saldo;
//
//        return $this;
//    }
//
//    /**
//     * Get saldo
//     *
//     * @return string
//     */
//    public function getSaldo()
//    {
//        return $this->saldo;
//    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return RegistroCombustiblePlanificacion
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set monedaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda $monedaid
     *
     * @return RegistroCombustiblePlanificacion
     */
    public function setMonedaid($monedaid = null)
    {
        $this->monedaid = $monedaid;

        return $this;
    }

    /**
     * Get monedaid
     *
     * @return Moneda
     */
    public function getMonedaid()
    {
        return $this->monedaid;
    }

    /**
     * Set registroCombustible
     *
     * @param \Geocuba\PortadoresBundle\Entity\RegistroCombustible $registroCombustible
     *
     * @return RegistroCombustiblePlanificacion
     */
    public function setRegistroCombustible($registroCombustible = null)
    {
        $this->registroCombustible = $registroCombustible;

        return $this;
    }

    /**
     * Get registroCombustible
     *
     * @return RegistroCombustible
     */
    public function getRegistroCombustible()
    {
        return $this->registroCombustible;
    }
}
