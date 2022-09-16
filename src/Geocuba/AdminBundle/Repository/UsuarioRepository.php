<?php

namespace Geocuba\AdminBundle\Repository;

use Doctrine\ORM\{
    AbstractQuery, EntityRepository, NonUniqueResultException
};
use Geocuba\AdminBundle\Entity\Usuario;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Class UsuarioRepository
 * @package Geocuba\AdminBundle\Repository
 */
class UsuarioRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * Loads the user for the given username.
     *
     * This method must return null if the user is not found.
     *
     * @param string $username The username
     *
     * @return UserInterface|null
     */
    public function loadUserByUsername($username)
    {
        $qb = $this->createQueryBuilder('usuario');
        $qb->where(
            $qb->expr()->andX(
                $qb->expr()->eq('usuario.usuario', $qb->expr()->literal($username)),
                $qb->expr()->eq('usuario.activo', ':active')
            )
        )->setCacheable(true);

        try {
            $user = $qb
                ->setParameter('active', TRUE)
                ->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);

            if (null === $user) {
                $users = $qb
                    ->setParameter('active', FALSE)
                    ->getQuery()->getResult(AbstractQuery::HYDRATE_OBJECT);

                $user = !empty($users) ? $users[0] : null;

                if (null === $user) {
                    throw new UsernameNotFoundException(sprintf('User "%s" not found.', $username));
                }

                return $user;
            }

            return $user;
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }

    /**
     * Devuelve todos los usuarios asociados al grupo especificado.
     *
     * @param array $groups_ids
     * @return Usuario[]
     */
    public function findAllByGrupos($groups_ids)
    {
        $qb = $this->createQueryBuilder('usuario');

        // remove duplicated users array_unique
        return $qb
            ->leftJoin('usuario.grupos', 'grupos')
            ->where(
                $qb->expr()->in('grupos.id', $groups_ids)
            )
            ->andWhere('usuario.activo = TRUE')
            ->getQuery()->setCacheable(true)->getResult();
    }

//    /**
//     * @param string $query
//     * @param null|int $limit
//     * @param int $start
//     * @return User[]
//     */
//    protected function _findByValue($query, $limit = null, $start = 0)
//    {
//        $query = preg_quote(trim(mb_strtolower($query, 'utf-8')));
//
//        // error_log(print_r($query, true));
//
//        // Search by: username, firstname, middlename, lastname, email and department's name.
//        $qb = $this->createQueryBuilder('user')->leftJoin('user.department_id', 'department');
//        $result = $qb
//            ->where(
//                $qb->expr()->orX(
//                    $qb->expr()->eq("ltree_operator(user.username,'~', :query)", $qb->expr()->literal(true)),
//                    $qb->expr()->eq("ltree_operator(user.firstname,'~', :query)", $qb->expr()->literal(true)),
//                    $qb->expr()->eq("ltree_operator(user.middlename,'~', :query)", $qb->expr()->literal(true)),
//                    $qb->expr()->eq("ltree_operator(user.lastname,'~', :query)", $qb->expr()->literal(true)),
//                    $qb->expr()->eq("ltree_operator(user.email,'~', :query)", $qb->expr()->literal(true)),
//                    $qb->expr()->eq("ltree_operator(department.name,'~', :query)", $qb->expr()->literal(true)),
//                    $qb->expr()->eq("ltree_operator(concat(user.firstname, ' ', user.middlename, ' ', user.middlename),'~', :query)", $qb->expr()->literal(true))
//                )
//            )
//            ->setParameter('query', $query)
//            ->getQuery()->getResult();
//
//        // error_log(print_r($qb->getQuery()->getSQL(), true));
//
//        $ids = [];
//        foreach ($result as $user) {
//            /** @var User $user */
//            $ids[] = $user->getId();
//        }
//
//        // Search by: groups names (TODO: how to do this in the query builder?)
//        foreach ($this->createQueryBuilder('user')->leftJoin('user.group_id', 'group')->getQuery()->getResult() as $user) {
//            /** @var User $user */
//            if (!in_array($user->getId(), $ids) && $user->getGroup()->exists(function ($k, Group $group) use ($query) {
//                    return mb_strpos(trim(mb_strtolower($group->getName(), 'utf-8')), $query, 0, 'utf-8') !== false;
//                })
//            ) {
//                $result[] = $user;
//            }
//        }
//
//        // Ordery by: username ASC
//        usort($result, function (User $a, User $b) {
//            return strcmp($a->getUsername(), $b->getUsername());
//        });
//
//        if ($limit) {
//            $result = array_slice($result, $start, $limit);
//        }
//
//        return $result;
//    }
}
