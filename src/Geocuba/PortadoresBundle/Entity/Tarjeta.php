<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tarjeta
 *
 * @ORM\Table(name="nomencladores.tarjeta", indexes={@ORM\Index(name="IDX_8DCAFBD13C7CEFFE", columns={"nunidadid"}), @ORM\Index(name="IDX_8DCAFBD12425E505", columns={"nmonedaid"}), @ORM\Index(name="IDX_8DCAFBD18FB90810", columns={"ncajaid"}), @ORM\Index(name="IDX_8DCAFBD167417F7E", columns={"ntipo_combustibleid"}), @ORM\Index(name="IDX_8DCAFBD1C5233BC6", columns={"npersonaid"}), @ORM\Index(name="IDX_8DCAFBD1D6AFCBCA", columns={"centrocosto"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TarjetaRepository")
 */
class Tarjeta
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
     * @ORM\Column(name="importe", type="decimal", precision=20, scale=2, nullable=false)
     */
    private $importe;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_registro", type="date", nullable=false)
     */
    private $fechaRegistro;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_vencimieno", type="date", nullable=false)
     */
    private $fechaVencimieno;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_baja", type="date", nullable=true)
     */
    private $fechaBaja;

    /**
     * @var string
     *
     * @ORM\Column(name="causa_baja", type="string", length=700, nullable=true)
     */
    private $causaBaja;

    /**
     * @var boolean
     *
     * @ORM\Column(name="reserva", type="boolean", nullable=false)
     */
    private $reserva;

    /**
     * @var integer
     *
     * @ORM\Column(name="pin", type="integer", nullable=true)
     */
    private $pin;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=false)
     */
    private $visible;

    /**
     * @var boolean
     *
     * @ORM\Column(name="exepcional", type="boolean", nullable=true)
     */
    private $exepcional;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="decimal", precision=1, scale=0, nullable=true)
     */
    private $estado;

    /**
     * @var string
     *
     * @ORM\Column(name="nro_tarjeta", type="string", length=255, nullable=true)
     */
    private $nroTarjeta;

    /**
     * @var \Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nunidadid", referencedColumnName="id")
     * })
     */
    private $nunidadid;

    /**
     * @var \Moneda
     *
     * @ORM\ManyToOne(targetEntity="Moneda")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="nmonedaid", referencedColumnName="id")
     * })
     */
    private $nmonedaid;

    /**
     * @var \Caja
     *
     * @ORM\ManyToOne(targetEntity="Caja")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ncajaid", referencedColumnName="id")
     * })
     */
    private $ncajaid;

    /**
     * @var \TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ntipo_combustibleid", referencedColumnName="id")
     * })
     */
    private $ntipoCombustibleid;

    /**
     * @var \Persona
     *
     * @ORM\ManyToOne(targetEntity="Persona")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="npersonaid", referencedColumnName="id")
     * })
     */
    private $npersonaid;

    /**
     * @var \CentroCosto
     *
     * @ORM\ManyToOne(targetEntity="CentroCosto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="centrocosto", referencedColumnName="id")
     * })
     */
    private $centrocosto;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="TarjetaVehiculo", mappedBy="ntarjetaid")
     */
    private $ntarjetanvehiculoid;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="TarjetaPersona", mappedBy="ntarjetaid")
     */
    private $ntarjetanpersonaid;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ntarjetanvehiculoid = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ntarjetanpersonaid = new \Doctrine\Common\Collections\ArrayCollection();
    }


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
     * Set importe
     *
     * @param string $importe
     *
     * @return Tarjeta
     */
    public function setImporte($importe)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe
     *
     * @return string
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set fechaRegistro
     *
     * @param \DateTime $fechaRegistro
     *
     * @return Tarjeta
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    /**
     * Get fechaRegistro
     *
     * @return \DateTime
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set fechaVencimieno
     *
     * @param \DateTime $fechaVencimieno
     *
     * @return Tarjeta
     */
    public function setFechaVencimieno($fechaVencimieno)
    {
        $this->fechaVencimieno = $fechaVencimieno;

        return $this;
    }

    /**
     * Get fechaVencimieno
     *
     * @return \DateTime
     */
    public function getFechaVencimieno()
    {
        return $this->fechaVencimieno;
    }

    /**
     * Set fechaBaja
     *
     * @param \DateTime $fechaBaja
     *
     * @return Tarjeta
     */
    public function setFechaBaja($fechaBaja)
    {
        $this->fechaBaja = $fechaBaja;

        return $this;
    }

    /**
     * Get fechaBaja
     *
     * @return \DateTime
     */
    public function getFechaBaja()
    {
        return $this->fechaBaja;
    }

    /**
     * Set causaBaja
     *
     * @param string $causaBaja
     *
     * @return Tarjeta
     */
    public function setCausaBaja($causaBaja)
    {
        $this->causaBaja = $causaBaja;

        return $this;
    }

    /**
     * Get causaBaja
     *
     * @return string
     */
    public function getCausaBaja()
    {
        return $this->causaBaja;
    }

    /**
     * Set reserva
     *
     * @param boolean $reserva
     *
     * @return Tarjeta
     */
    public function setReserva($reserva)
    {
        $this->reserva = $reserva;

        return $this;
    }

    /**
     * Get reserva
     *
     * @return boolean
     */
    public function getReserva()
    {
        return $this->reserva;
    }

    /**
     * Set pin
     *
     * @param integer $pin
     *
     * @return Tarjeta
     */
    public function setPin($pin)
    {
        $this->pin = $pin;

        return $this;
    }

    /**
     * Get pin
     *
     * @return integer
     */
    public function getPin()
    {
        return $this->pin;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     *
     * @return Tarjeta
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
     * Set exepcional
     *
     * @param boolean $exepcional
     *
     * @return Tarjeta
     */
    public function setExepcional($exepcional)
    {
        $this->exepcional = $exepcional;

        return $this;
    }

    /**
     * Get exepcional
     *
     * @return boolean
     */
    public function getExepcional()
    {
        return $this->exepcional;
    }

    /**
     * Set estado
     *
     * @param string $estado
     *
     * @return Tarjeta
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;

        return $this;
    }

    /**
     * Get estado
     *
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * Set nroTarjeta
     *
     * @param string $nroTarjeta
     *
     * @return Tarjeta
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
     * Set nunidadid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad $nunidadid
     *
     * @return Tarjeta
     */
    public function setUnidadid(\Geocuba\PortadoresBundle\Entity\Unidad $nunidadid = null)
    {
        $this->nunidadid = $nunidadid;

        return $this;
    }

    /**
     * Get nunidadid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad
     */
    public function getUnidadid()
    {
        return $this->nunidadid;
    }

    /**
     * Set nmonedaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Moneda $nmonedaid
     *
     * @return Tarjeta
     */
    public function setMonedaid(\Geocuba\PortadoresBundle\Entity\Moneda $nmonedaid = null)
    {
        $this->nmonedaid = $nmonedaid;

        return $this;
    }

    /**
     * Get nmonedaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Moneda
     */
    public function getMonedaid()
    {
        return $this->nmonedaid;
    }

    /**
     * Set ncajaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Caja $ncajaid
     *
     * @return Tarjeta
     */
    public function setCajaid(\Geocuba\PortadoresBundle\Entity\Caja $ncajaid = null)
    {
        $this->ncajaid = $ncajaid;

        return $this;
    }

    /**
     * Get ncajaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Caja
     */
    public function getCajaid()
    {
        return $this->ncajaid;
    }

    /**
     * Set ntipoCombustibleid
     *
     * @param \Geocuba\PortadoresBundle\Entity\TipoCombustible $ntipoCombustibleid
     *
     * @return Tarjeta
     */
    public function setTipoCombustibleid(\Geocuba\PortadoresBundle\Entity\TipoCombustible $ntipoCombustibleid = null)
    {
        $this->ntipoCombustibleid = $ntipoCombustibleid;

        return $this;
    }

    /**
     * Get ntipoCombustibleid
     *
     * @return \Geocuba\PortadoresBundle\Entity\TipoCombustible
     */
    public function getTipoCombustibleid()
    {
        return $this->ntipoCombustibleid;
    }

    /**
     * Set npersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\Persona $npersonaid
     *
     * @return Tarjeta
     */
    public function setPersonaid(\Geocuba\PortadoresBundle\Entity\Persona $npersonaid = null)
    {
        $this->npersonaid = $npersonaid;

        return $this;
    }

    /**
     * Get npersonaid
     *
     * @return \Geocuba\PortadoresBundle\Entity\Persona
     */
    public function getPersonaid()
    {
        return $this->npersonaid;
    }

    /**
     * Set centrocosto
     *
     * @param \Geocuba\PortadoresBundle\Entity\CentroCosto $centrocosto
     *
     * @return Tarjeta
     */
    public function setCentrocosto(\Geocuba\PortadoresBundle\Entity\CentroCosto $centrocosto = null)
    {
        $this->centrocosto = $centrocosto;

        return $this;
    }

    /**
     * Get centrocosto
     *
     * @return \Geocuba\PortadoresBundle\Entity\CentroCosto
     */
    public function getCentrocosto()
    {
        return $this->centrocosto;
    }


    /**
     * Add ntarjetanvehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\TarjetaVehiculo $ntarjetanvehiculoid
     *
     * @return Tarjeta
     */
    public function addTarjetaVehiculoid(\Geocuba\PortadoresBundle\Entity\TarjetaVehiculo $ntarjetanvehiculoid)
    {
        $this->ntarjetanvehiculoid[] = $ntarjetanvehiculoid;

        return $this;
    }

    /**
     * Remove ntarjetanvehiculoid
     *
     * @param \Geocuba\PortadoresBundle\Entity\TarjetaVehiculo $ntarjetanvehiculoid
     */
    public function removeTarjetaVehiculoid(\Geocuba\PortadoresBundle\Entity\TarjetaVehiculo $ntarjetanvehiculoid)
    {
        $this->ntarjetanvehiculoid->removeElement($ntarjetanvehiculoid);
    }

    /**
     * Get ntarjetanvehiculoid
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTarjetaVehiculoid()
    {
        return $this->ntarjetanvehiculoid;
    }

    /**
     * Add ntarjetanpersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\TarjetaPersona $ntarjetanpersonaid
     *
     * @return Tarjeta
     */
    public function addNtarjetanpersonaid(\Geocuba\PortadoresBundle\Entity\TarjetaPersona $ntarjetanpersonaid)
    {
        $this->ntarjetanpersonaid[] = $ntarjetanpersonaid;

        return $this;
    }

    /**
     * Remove ntarjetanpersonaid
     *
     * @param \Geocuba\PortadoresBundle\Entity\TarjetaPersona $ntarjetanpersonaid
     */
    public function removeNtarjetanpersonaid(\Geocuba\PortadoresBundle\Entity\TarjetaPersona $ntarjetanpersonaid)
    {
        $this->ntarjetanpersonaid->removeElement($ntarjetanpersonaid);
    }

    /**
     * Get ntarjetanpersonaid
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getNtarjetanpersonaid()
    {
        return $this->ntarjetanpersonaid;
    }


}
