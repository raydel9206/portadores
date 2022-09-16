<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistroOperacionMontacarga
 *
 * @ORM\Table(name="datos.registro_operaciones_montacargas")
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\RegistroOperacionMontacargaRepository")
 */
class RegistroOperacionMontacarga extends RegistroOperacion
{
    /**
     * @var string
     *
     * @ORM\Column(name="horametro_arranque", type="string", length=20, nullable=false)
     */
    private $horametroArranque;

    /**
     * @var string
     *
     * @ORM\Column(name="horametro_parada", type="string", length=20, nullable=false)
     */
    private $horametroParada;


    /**
     * @var RegistroOperacion
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="RegistroOperacion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id", referencedColumnName="id")
     * })
     */
    private $id;


    /**
     * Set horametroArranque.
     *
     * @param string $horametroArranque
     *
     * @return RegistroOperacionMontacarga
     */
    public function setHorametroArranque($horametroArranque): RegistroOperacionMontacarga
    {
        $this->horametroArranque = $horametroArranque;

        return $this;
    }

    /**
     * Get horametroArranque.
     *
     * @return string
     */
    public function getHorametroArranque(): string
    {
        return $this->horametroArranque;
    }

    /**
     * Set horametroParada.
     *
     * @param string $horametroParada
     *
     * @return RegistroOperacionMontacarga
     */
    public function setHorametroParada($horametroParada): RegistroOperacionMontacarga
    {
        $this->horametroParada = $horametroParada;

        return $this;
    }

    /**
     * Get horametroParada.
     *
     * @return string
     */
    public function getHorametroParada(): string
    {
        return $this->horametroParada;
    }

    /**
     * @param bool $horas
     *
     * @return mixed Si $horas === true retorna string 'hora:min'. Si $horas === false retorna int $minutos.
     */
    public function getTiempoTrabajado($horas = true)
    {
        $horametroArrArranque = explode(':', $this->horametroArranque);
        $horametroArrParada = explode(':', $this->horametroParada);

        $time1 = date_create_from_format('H:i', '00:' . $horametroArrArranque[1]);
        $time2 = date_create_from_format('H:i', ($horametroArrParada[0] - $horametroArrArranque[0]) . ':' . $horametroArrParada[1]);

        $minutos = ($time2->getTimestamp() - $time1->getTimestamp()) / 60;

        if ($horas) return (int)($minutos / 60) . ':' . $minutos % 60;

        return $minutos;
    }
}
