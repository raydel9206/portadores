<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoCombustible
 *
 * @ORM\Table(name="nomencladores.tipo_combustible", indexes={@ORM\Index(name="IDX_205E1BCE581E2F8", columns={"portadorid"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TipoCombustibleRepository")
 */
class   TipoCombustible
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
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255, nullable=true)
     */
    private $nombre;

//    /**
//     * @var \Moneda
//     *
//     * @ORM\ManyToOne(targetEntity="Moneda")
//     * @ORM\JoinColumns({
//     *   @ORM\JoinColumn(name="moneda", referencedColumnName="id")
//     * })
//     */
//    private $moneda;

    /**
     * @var string
     *
     * @ORM\Column(name="precio", type="decimal", precision=8, scale=2, nullable=false)
     */
    private $precio;

    /**
     * @var string
     *
     * @ORM\Column(name="precio_tiro_directo", type="decimal", precision=8, scale=2, nullable=false)
     */
    private $precioTiroDirecto;

    /**
     * @var string
     *
     * @ORM\Column(name="maximo_tarjeta_dinero", type="decimal", precision=19, scale=3, nullable=false)
     */
    private $maximoTarjetaDinero;

    /**
     * @var string
     *
     * @ORM\Column(name="maximo_tarjeta_litro", type="decimal", precision=19, scale=3, nullable=false)
     */
    private $maximoTarjetaLitro;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=255, nullable=true)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="fila", type="string", nullable=true)
     */
    private $fila;

    /**
     * @var integer
     *
     * @ORM\Column(name="nro", type="integer", nullable=true)
     */
    private $nro;

    /**
     * @var \Portador
     *
     * @ORM\ManyToOne(targetEntity="Portador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portadorid", referencedColumnName="id")
     * })
     */
    private $portadorid;



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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return TipoCombustible
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set precio
     *
     * @param string $precio
     *
     * @return TipoCombustible
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    /**
     * Get precio
     *
     * @return string
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set precioTiroDirecto
     *
     * @param string $precioTiroDirecto
     *
     * @return TipoCombustible
     */
    public function setPrecioTD($precioTiroDirecto)
    {
        $this->precioTiroDirecto = $precioTiroDirecto;

        return $this;
    }

    /**
     * Get precioTiroDirecto
     *
     * @return string
     */
    public function getPrecioTD()
    {
        return $this->precioTiroDirecto;
    }

    /**
     * Set maximoTarjeta
     *
     * @param string $maximoTarjetaDinero
     *
     * @return TipoCombustible
     */
    public function setMaximoTarjetaDinero($maximoTarjetaDinero): TipoCombustible
    {
        $this->maximoTarjetaDinero = $maximoTarjetaDinero;

        return $this;
    }

    /**
     * Get maximoTarjeta
     *
     * @return string
     */
    public function getMaximoTarjetaDinero(): string
    {
        return $this->maximoTarjetaDinero;
    }

    /**
     * Set maximoTarjetaLitro
     *
     * @param string $maximoTarjetaLitro
     *
     * @return TipoCombustible
     */
    public function setMaximoTarjetaLitro($maximoTarjetaLitro): TipoCombustible
    {
        $this->maximoTarjetaLitro = $maximoTarjetaLitro;

        return $this;
    }

    /**
     * Get maximoTarjetaLitro
     *
     * @return string
     */
    public function getMaximoTarjetaLitro(): string
    {
        return $this->maximoTarjetaLitro;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return TipoCombustible
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
     * Set codigo
     *
     * @param string $codigo
     *
     * @return TipoCombustible
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo
     *
     * @return string
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set fila
     *
     * @param string $fila
     *
     * @return TipoCombustible
     */
    public function setFila($fila): TipoCombustible
    {
        $this->fila = $fila;

        return $this;
    }

    /**
     * Get fila
     *
     * @return string
     */
    public function getFila()
    {
        return $this->fila;
    }

    /**
     * Set nro
     *
     * @param integer $nro
     *
     * @return TipoCombustible
     */
    public function setNro($nro)
    {
        $this->nro = $nro;

        return $this;
    }

    /**
     * Get nro
     *
     * @return integer
     */
    public function getNro()
    {
        return $this->nro;
    }

    /**
     * Set portadorid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Portador $portadorid
     *
     * @return TipoCombustible
     */
    public function setPortadorid(\Geocuba\PortadoresBundle\Entity\Portador $portadorid = null)
    {
        $this->portadorid = $portadorid;

        return $this;
    }

    /**
     * Get portadorid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Portador
     */
    public function getPortadorid()
    {
        return $this->portadorid;
    }

//    /**
//     * Set moneda.
//     *
//     * @param \Geocuba\PortadoresBundle\Entity\Moneda|null $moneda
//     *
//     * @return TipoCombustible
//     */
//    public function setMoneda(\Geocuba\PortadoresBundle\Entity\Moneda $moneda = null)
//    {
//        $this->moneda = $moneda;
//
//        return $this;
//    }
//
//    /**
//     * Get moneda.
//     *
//     * @return \Geocuba\PortadoresBundle\Entity\Moneda|null
//     */
//    public function getMoneda()
//    {
//        return $this->moneda;
//    }
}
