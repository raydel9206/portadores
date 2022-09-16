<?php

namespace Geocuba\PortadoresBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Debug\Debug;

/**
 * Tanque
 *
 * @ORM\Table(name="nomencladores.tanques", indexes={@ORM\Index(name="IDX_44D24D21596E597F", columns={"tipo_combustible_id"})})
 * @ORM\Entity(repositoryClass="Geocuba\PortadoresBundle\Repository\TanqueRepository")
 */
class Tanque
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
     * @ORM\Column(name="numero_inventario", type="string", nullable=false)
     */
    private $numeroInventario;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", nullable=false)
     */
    private $descripcion;

    /**
     * @var string
     *
     * @ORM\Column(name="capacidad", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $capacidad = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="existencia", type="decimal", precision=20, scale=4, nullable=false)
     */
    private $existencia = '0';

    /**
     * @var bool
     *
     * @ORM\Column(name="cilindro", type="boolean", nullable=false)
     */
    private $cilindro;

    /**
     * @var TipoCombustible
     *
     * @ORM\ManyToOne(targetEntity="TipoCombustible")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_combustible_id", referencedColumnName="id")
     * })
     */
    private $tipoCombustible;

    /**
     * @var UnidadMedida
     *
     * @ORM\ManyToOne(targetEntity="UnidadMedida")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_medida_id", referencedColumnName="id")
     * })
     */
    private $unidadMedida;

    /**
     * @var Unidad
     *
     * @ORM\ManyToOne(targetEntity="Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidad_id", referencedColumnName="id")
     * })
     */
    private $unidad;

    /** @OneToMany(targetEntity="MedicionAfore", mappedBy="tanque", orphanRemoval=true, cascade={"persist"}) */
    private $medicionesAfore;

    /** @OneToMany(targetEntity="MedicionDiariaTanque", mappedBy="tanque", orphanRemoval=true, cascade={"persist"}) */
    private $medicionesDiarias;

    /** @OneToMany(targetEntity="EntradaSalidaCombustible", mappedBy="tanque", orphanRemoval=true, cascade={"persist"}) */
    private $entradasSalidas;


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
     * Set numeroSerie.
     *
     * @param string $numeroInventario
     *
     * @return Tanque
     */
    public function setNumeroInventario($numeroInventario): Tanque
    {
        $this->numeroInventario = $numeroInventario;

        return $this;
    }

    /**
     * Get numeroSerie.
     *
     * @return string
     */
    public function getNumeroInventario(): string
    {
        return $this->numeroInventario;
    }

    /**
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return Tanque
     */
    public function setDescripcion($descripcion): Tanque
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string
     */
    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    /**
     * Set capacidad.
     *
     * @param string $capacidad
     *
     * @return Tanque
     */
    public function setCapacidad($capacidad): Tanque
    {
        $this->capacidad = $capacidad;

        return $this;
    }

    /**
     * Get capacidad.
     *
     * @return string
     */
    public function getCapacidad(): string
    {
        return $this->capacidad;
    }

    /**
     * Set existencia.
     *
     * @param string $existencia
     *
     * @return Tanque
     */
    public function setExistencia($existencia): Tanque
    {
        $this->existencia = $existencia;

        return $this;
    }

    /**
     * Get existencia.
     *
     * @return string
     */
    public function getExistencia(): string
    {
        return $this->existencia;
    }

    /**
     * Set cilindro.
     *
     * @param bool $cilindro
     *
     * @return Tanque
     */
    public function setCilindro($cilindro): Tanque
    {
        $this->cilindro = $cilindro;

        return $this;
    }

    /**
     * Get cilindro.
     *
     * @return bool
     */
    public function getCilindro(): bool
    {
        return $this->cilindro;
    }

    /**
     * Set tipoCombustible.
     *
     * @param TipoCombustible $tipoCombustible
     *
     * @return Tanque
     */
    public function setTipoCombustible(TipoCombustible $tipoCombustible = null): Tanque
    {
        $this->tipoCombustible = $tipoCombustible;

        return $this;
    }

    /**
     * Get tipoCombustible.
     *
     * @return TipoCombustible
     */
    public function getTipoCombustible(): TipoCombustible
    {
        return $this->tipoCombustible;
    }

    /**
     * Set unidad.
     *
     * @param Unidad $unidad
     * @return Tanque
     */
    public function setUnidad(Unidad $unidad): Tanque
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad.
     *
     * @return Unidad
     */
    public function getUnidad(): Unidad
    {
        return $this->unidad;
    }

    /**
     * Set unidadMedida.
     *
     * @param UnidadMedida $unidadMedida
     * @return Tanque
     */
    public function setUnidadMedida(UnidadMedida $unidadMedida): Tanque
    {
        $this->unidadMedida = $unidadMedida;

        return $this;
    }

    /**
     * Get unidadMedida.
     *
     * @return UnidadMedida
     */
    public function getUnidadMedida(): UnidadMedida
    {
        return $this->unidadMedida;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->medicionesAfore = new ArrayCollection();
        $this->medicionesDiarias = new ArrayCollection();
        $this->entradasSalidas = new ArrayCollection();
    }

    /**
     * Add medicionesAfore.
     *
     * @param MedicionAfore $medicionAfore
     *
     * @return Tanque
     */
    public function addMedicionAfore(MedicionAfore $medicionAfore): Tanque
    {
        $medicionAfore->setTanque($this);
        $this->medicionesAfore[] = $medicionAfore;

        return $this;
    }

    /**
     * Remove medicionesAfore.
     *
     * @param MedicionAfore $medicionAfore
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMedicionAfore(MedicionAfore $medicionAfore): bool
    {
        return $this->medicionesAfore->removeElement($medicionAfore);
    }

    /**
     * Remove medicionesAfore.
     *
     * @param string $id
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMedicionAforeById($id): bool
    {
        $elements = $this->medicionesAfore->filter(static function ($medicion) use ($id) {
            /** @var MedicionAfore $medicion */
            return $medicion->getId() === $id;
        });
        if (count($elements) < 1) return false;
        return $this->medicionesAfore->removeElement($elements[$elements->getKeys()[0]]);
    }

    /**
     * Get medicionesAfore.
     *
     * @return Collection
     */
    public function getMedicionesAfore(): Collection
    {
        return $this->medicionesAfore;
    }

    /**
     * Add medicionDiaria.
     *
     * @param MedicionDiariaTanque $medicionDiaria
     *
     * @return Tanque
     */
    public function addMedicionDiaria(MedicionDiariaTanque $medicionDiaria): Tanque
    {
        $medicionDiaria->setTanque($this);
        $this->medicionesDiarias[] = $medicionDiaria;

        return $this;
    }

    /**
     * Remove medicionDiaria.
     *
     * @param MedicionDiariaTanque $medicionDiaria
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMedicionDiaria(MedicionDiariaTanque $medicionDiaria): bool
    {
        return $this->medicionesDiarias->removeElement($medicionDiaria);
    }

    /**
     * Remove medicionDiaria.
     *
     * @param string $id
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeMedicionDiariaById($id): bool
    {
        $elements = $this->medicionesDiarias->filter(static function ($medicion) use ($id) {
            /** @var MedicionDiariaTanque $medicion */
            return $medicion->getId() === $id;
        });
        if (count($elements) < 1) return false;
        return $this->medicionesDiarias->removeElement($elements[$elements->getKeys()[0]]);
    }

    /**
     * Get medicionesDiaria.
     *
     * @return Collection
     */
    public function getMedicionesDiarias(): Collection
    {
        return $this->medicionesDiarias;
    }

    /**
     * Add entradaSalida.
     *
     * @param EntradaSalidaCombustible $entradaSalida
     *
     * @return Tanque
     */
    public function addEntradaSalida(EntradaSalidaCombustible $entradaSalida): Tanque
    {
        $entradaSalida->setTanque($this);
        $this->entradasSalidas[] = $entradaSalida;

        return $this;
    }

    /**
     * Remove entradaSalida.
     *
     * @param EntradaSalidaCombustible $entradaSalida
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEntradaSalida(EntradaSalidaCombustible $entradaSalida): bool
    {
        return $this->entradasSalidas->removeElement($entradaSalida);
    }

    /**
     * Remove entradaSalida.
     *
     * @param string $id
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeEntradaSalidaById($id): bool
    {
        $elements = $this->entradasSalidas->filter(static function ($entradaSalida) use ($id) {
            /** @var EntradaSalidaCombustible $entradaSalida */
            return $entradaSalida->getId() === $id;
        });
        if (count($elements) < 1) return false;
        return $this->entradasSalidas->removeElement($elements[$elements->getKeys()[0]]);
    }

    /**
     * Get entradaSalida.
     *
     * @return Collection
     */
    public function getEntradasSalidas(): Collection
    {
        return $this->entradasSalidas;
    }

    public function calcularNivel($nivel): float
    {
        $afore = $this->getMedicionesAfore()->filter(static function ($medicionAfore) use ($nivel) {
            /** @var MedicionAfore $medicionAfore */
            return (float)$medicionAfore->getNivel() === (float)$nivel;
        });

        if (!$afore->isEmpty()) {
            $existencia = $afore->first()->getExistencia();
        } else {
            $afores = $this->getMedicionesAfore()->partition(static function ($index, $medicion) use ($nivel) {
                /** @var MedicionAfore $medicion */
                return $medicion->getNivel() > (float)$nivel;
            });
            $aforeMenor = $afores[0]->toArray();
            $aforeMayor = $afores[1]->toArray();

            if (!count($aforeMenor) || !count($aforeMayor)) return -1;

            usort($aforeMenor, static function ($a, $b) {
                return $a->getNivel() > $b->getNivel();
            });
            usort($aforeMayor, static function ($a, $b) {
                return $a->getNivel() < $b->getNivel();
            });

            $existencia =
                ((float)$nivel - $aforeMenor[0]->getNivel()) /
                (($aforeMayor[0]->getNivel() - $aforeMenor[0]->getNivel()) / ($aforeMayor[0]->getExistencia() - $aforeMenor[0]->getExistencia()))
                + $aforeMenor[0]->getExistencia();
        }

        return $existencia;
    }

    public function calcularExistencia($em): float
    {
        /**
         * @var EntityManager $em
         * @var MedicionDiariaTanque[] $lastMedicion
         */
        $qb = $em->createQueryBuilder();
        $lastMedicion = $qb->select('medicion')
            ->from('PortadoresBundle:MedicionDiariaTanque', 'medicion')
            ->where($qb->expr()->eq('medicion.tanque', ':id'))
            ->setParameter('id', $this->id)
            ->orderBy('medicion.fecha', 'DESC')
            ->getQuery()->getResult();

        if (empty($lastMedicion)) return 0;

        $entradasSalidas = $qb->select('entradaSalida')
            ->from('PortadoresBundle:EntradaSalidaCombustible', 'entradaSalida')
            ->where($qb->expr()->eq('entradaSalida.tanque', ':id'))
            ->andWhere($qb->expr()->gte('entradaSalida.fecha', ':fecha'))
            ->setParameters([
                'id' => $this->id,
                'fecha' => $lastMedicion[0]->getFecha()->format('Y-m-d') . ' 00:00:00'
            ])
            ->getQuery()->getResult();

        $total = $entradasSalidas ? array_reduce($entradasSalidas, static function ($temp, $entradaSalida) {
            /** @var EntradaSalidaCombustible $entradaSalida */
            return $temp + (float) $entradaSalida->getCantidad();
        }, 0) : 0;

        return $lastMedicion[0]->getExistencia() + $total;
    }
}
