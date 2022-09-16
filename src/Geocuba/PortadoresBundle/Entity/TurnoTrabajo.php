<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TurnoTrabajo
 *
 * @ORM\Table(name="nomencladores.turnotrabajo")
 * @ORM\Entity
 */
class TurnoTrabajo
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
     * @var integer
     *
     * @ORM\Column(name="turno", type="integer", nullable=true)
     */
    private $turno;

    /**
     * @var integer
     *
     * @ORM\Column(name="horas", type="integer", nullable=true)
     */
    private $horas;

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
     * Set turno
     *
     * @param integer $turno
     *
     * @return TurnoTrabajo
     */
    public function setTurno($turno)
    {
        $this->turno = $turno;

        return $this;
    }

    /**
     * Get turno
     *
     * @return integer
     */
    public function getTurno()
    {
        return $this->turno;
    }

    /**
     * Set horas
     *
     * @param integer $horas
     *
     * @return TurnoTrabajo
     */
    public function setHoras($horas)
    {
        $this->horas = $horas;

        return $this;
    }

    /**
     * Get horas
     *
     * @return integer
     */
    public function getHoras()
    {
        return $this->horas;
    }
}
