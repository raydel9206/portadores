<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

/**
 * FactorConversion
 *
 * @ORM\Table(name="nomencladores.factores_conversion", indexes={@ORM\Index(name="IDX_E06F73B6BEF6566F", columns={"portador_id"}), @ORM\Index(name="IDX_E06F73B6A0C3D87A", columns={"de_um"}), @ORM\Index(name="IDX_E06F73B6A475B6A1", columns={"a_um"})})
 * @ORM\Entity
 */
class FactorConversion
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
     * @ORM\Column(name="factor", type="decimal", precision=14, scale=6, nullable=false)
     */
    private $factor;

    /**
     * @var Portador
     *
     * @ORM\ManyToOne(targetEntity="Portador")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="portador_id", referencedColumnName="id")
     * })
     */
    private $portador;

    /**
     * @var UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="de_um", referencedColumnName="id")
     * })
     */
    private $deUm;

    /**
     * @var UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="a_um", referencedColumnName="id")
     * })
     */
    private $aUm;


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
     * Set factor.
     *
     * @param string $factor
     *
     * @return FactorConversion
     */
    public function setFactor($factor): FactorConversion
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * Get factor.
     *
     * @return string
     */
    public function getFactor(): string
    {
        return $this->factor;
    }

    /**
     * Set portador.
     *
     * @param Portador $portador
     *
     * @return FactorConversion
     */
    public function setPortador(Portador $portador = null): FactorConversion
    {
        $this->portador = $portador;

        return $this;
    }

    /**
     * Get portador.
     *
     * @return Portador
     */
    public function getPortador(): Portador
    {
        return $this->portador;
    }

    /**
     * Set deUm.
     *
     * @param UnidadMedida $deUm
     *
     * @return FactorConversion
     */
    public function setDeUm(UnidadMedida $deUm = null): FactorConversion
    {
        $this->deUm = $deUm;

        return $this;
    }

    /**
     * Get deUm.
     *
     * @return UnidadMedida
     */
    public function getDeUm(): UnidadMedida
    {
        return $this->deUm;
    }

    /**
     * Set aUm.
     *
     * @param UnidadMedida $aUm
     *
     * @return FactorConversion
     */
    public function setAUm(UnidadMedida $aUm = null): FactorConversion
    {
        $this->aUm = $aUm;

        return $this;
    }

    /**
     * Get aUm.
     *
     * @return UnidadMedida
     */
    public function getAUm(): UnidadMedida
    {
        return $this->aUm;
    }

    public static function getFactorByUm($em, $portador_id, $de_um_id, $a_um_id, $inversed = false)
    {
        if ($de_um_id === $a_um_id) return 1;

        /**
         * @var EntityManager $em
         * @var FactorConversion $um
         */
        $um = $em->getRepository('PortadoresBundle:FactorConversion')->findOneBy([
            'portador' => $portador_id,
            'deUm' => $de_um_id,
            'aUm' => $a_um_id
        ]);

        if (!$um) {
            $um = $em->getRepository('PortadoresBundle:FactorConversion')->findOneBy([
                'portador' => $portador_id,
                'deUm' => $a_um_id,
                'aUm' => $de_um_id
            ]);
            $inversed = true;
        }

        if (!$um) return false;

        return $inversed ? 1 / $um->getFactor() : $um->getFactor();
    }
}
