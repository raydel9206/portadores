<?php

namespace Geocuba\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Geocuba\Utils\Functions;
use Symfony\Component\Security\Core\User\{
    AdvancedUserInterface, EquatableInterface, UserInterface
};

/**
 * Usuario
 *
 * @ORM\Table(name="admin.usuario", indexes={@ORM\Index(name="usuario_usuario", columns={"usuario"}), @ORM\Index(name="usuario_id", columns={"id"}), @ORM\Index(name="usuario_activo", columns={"activo"}), @ORM\Index(name="idx_usename_active", columns={"usuario", "activo"})})
 * @ORM\Entity(repositoryClass="Geocuba\AdminBundle\Repository\UsuarioRepository")
 */
class Usuario implements AdvancedUserInterface, EquatableInterface, \Serializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="SEQUENCE")
     * @ORM\SequenceGenerator(sequenceName="admin.usuario_id_seq", allocationSize=1, initialValue=1)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="usuario", type="string", length=50, nullable=false)
     */
    private $usuario;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre_completo", type="string", length=150, nullable=false)
     */
    private $nombreCompleto;

    /**
     * @var string
     *
     * @ORM\Column(name="cargo", type="string", length=150, nullable=false)
     */
    private $cargo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_creacion", type="datetime", nullable=false)
     */
    private $fechaCreacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha_modificacion", type="datetime", nullable=false)
     */
    private $fechaModificacion;

    /**
     * @var string
     *
     * @ORM\Column(name="contrasena", type="string", length=255, nullable=false)
     */
    private $contrasena;

    /**
     * @var string
     *
     * @ORM\Column(name="sal", type="string", length=255, nullable=false)
     */
    private $sal;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false)
     */
    private $activo;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Grupo", inversedBy="usuarios")
     * @ORM\JoinTable(name="admin.grupo_usuario",
     *   joinColumns={
     *     @ORM\JoinColumn(name="usuarios_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="grupos_id", referencedColumnName="id")
     *   }
     * )
     */
    private $grupos;

    /**
     * @var \Geocuba\PortadoresBundle\Entity\Unidad|null
     *
     * @ORM\ManyToOne(targetEntity="\Geocuba\PortadoresBundle\Entity\Unidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="unidadid", referencedColumnName="id")
     * })
     */
    private $unidad;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sal = hash('sha512', uniqid(null, true)); // $this->salt = md5(uniqid(null, true));
        $this->grupos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fechaCreacion = new \DateTime();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set usuario.
     *
     * @param string $usuario
     *
     * @return Usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario.
     *
     * @return string
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set nombreCompleto.
     *
     * @param string $nombreCompleto
     *
     * @return Usuario
     */
    public function setNombreCompleto($nombreCompleto)
    {
        $this->nombreCompleto = $nombreCompleto;

        return $this;
    }

    /**
     * Get nombreCompleto.
     *
     * @return string
     */
    public function getNombreCompleto()
    {
        return $this->nombreCompleto;
    }

    /**
     * Set cargo.
     *
     * @param string $cargo
     *
     * @return Usuario
     */
    public function setCargo($cargo)
    {
        $this->cargo = $cargo;

        return $this;
    }

    /**
     * Get nombreCompleto.
     *
     * @return string
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return Usuario
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fechaCreacion.
     *
     * @param \DateTime $fechaCreacion
     *
     * @return Usuario
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion.
     *
     * @return \DateTime
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaModificacion.
     *
     * @param \DateTime $fechaModificacion
     *
     * @return Usuario
     */
    public function setFechaModificacion($fechaModificacion)
    {
        $this->fechaModificacion = $fechaModificacion;

        return $this;
    }

    /**
     * Get fechaModificacion.
     *
     * @return \DateTime
     */
    public function getFechaModificacion()
    {
        return $this->fechaModificacion;
    }

    /**
     * Set contrasena.
     *
     * @param string $contrasena
     *
     * @return Usuario
     */
    public function setContrasena($contrasena)
    {
        $this->contrasena = $contrasena;

        return $this;
    }

    /**
     * Get contrasena.
     *
     * @return string
     */
    public function getContrasena()
    {
        return $this->contrasena;
    }

    /**
     * Set sal.
     *
     * @param string $sal
     *
     * @return Usuario
     */
    public function setSal($sal)
    {
        $this->sal = $sal;

        return $this;
    }

    /**
     * Get sal.
     *
     * @return string
     */
    public function getSal()
    {
        return $this->sal;
    }

    /**
     * Set activo.
     *
     * @param bool $activo
     *
     * @return Usuario
     */
    public function setActivo($activo)
    {
        $this->activo = $activo;

        return $this;
    }

    /**
     * Get activo.
     *
     * @return bool
     */
    public function getActivo()
    {
        return $this->activo;
    }

    /**
     * Add grupo.
     *
     * @param \Geocuba\AdminBundle\Entity\Grupo $grupo
     *
     * @return Usuario
     */
    public function addGrupo(\Geocuba\AdminBundle\Entity\Grupo $grupo)
    {
        $this->grupos[] = $grupo;

        return $this;
    }

    /**
     * Remove grupo.
     *
     * @param \Geocuba\AdminBundle\Entity\Grupo $grupo
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeGrupo(\Geocuba\AdminBundle\Entity\Grupo $grupo)
    {
        return $this->grupos->removeElement($grupo);
    }

    /**
     * Get grupos.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGrupos()
    {
        return $this->grupos;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize([$this->id, $this->usuario, $this->contrasena, $this->sal]);
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list ($this->id, $this->usuario, $this->contrasena, $this->sal) = unserialize($serialized);
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        return $this->grupos->toArray();
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->activo;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof Usuario) {
            return false;
        }

        if ($this->activo) {
            return false;
        }

        if ($this->usuario !== $user->getUsername()) {
            return false;
        }

        if ($this->contrasena !== $user->getPassword()) {
            return false;
        }

        if ($this->sal !== $user->getSalt()) {
            return false;
        }

        return true;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->contrasena;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return $this->sal;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->usuario;
    }

    /**
     * Mapea el objeto a un arreglo donde cada elemento es una asociación atributo - valor del objeto.
     * @param bool $simple
     * @return array
     */
    public function toArray($simple = true)
    {
        $grupos = $simple ? null : $this->grupos->map(function (Grupo $grupo) {
            return $grupo->toArray();
        })->toArray();

        return [
            'id' => $this->id,
            'usuario' => $this->usuario,
            'nombre_completo' => $this->nombreCompleto,
            'cargo' => $this->cargo,
            'email' => $this->getEmail(),

            'fecha_creacion' => Functions::formatDateTime($this->fechaCreacion),
            'fecha_modificacion' => Functions::formatDateTime($this->fechaModificacion),

            'grupos' => $grupos,

            'activo' => $this->activo,
            'unidad' => $this->unidad->getId()
        ];
    }

    /**
     * Set unidad
     *
     * @param \Geocuba\PortadoresBundle\Entity\Unidad|null $unidad
     *
     * @return Usuario
     */
    public function setUnidad($unidad = null)
    {
        $this->unidad = $unidad;

        return $this;
    }

    /**
     * Get unidad
     *
     * @return \Geocuba\PortadoresBundle\Entity\Unidad|null
     */
    public function getUnidad()
    {
        return $this->unidad;
    }
}