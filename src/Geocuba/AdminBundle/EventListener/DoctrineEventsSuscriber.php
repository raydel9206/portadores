<?php

namespace Geocuba\AdminBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\UnitOfWork;
use Geocuba\AdminBundle\Entity\{
    Evento, Usuario
};
use Geocuba\Utils\Constants;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class DoctrineEventSuscriber
 * @package Geocuba\AdminBundle\EventListener
 */
class DoctrineEventsSuscriber implements EventSubscriber
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var array
     */
    private $auditedEntities;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * DoctrineEventSuscriber constructor.
     * @param Logger $logger
     * @param TokenStorageInterface $tokenStorage
     * @param RequestStack $requestStack
     * @param $auditedEntities
     */
    public function __construct(Logger $logger, TokenStorageInterface $tokenStorage, RequestStack $requestStack, $auditedEntities)
    {
        $this->logger = $logger;
        $this->tokenStorage = $tokenStorage;
        $this->requestStack = $requestStack;
        $this->auditedEntities = $auditedEntities;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [Events::onFlush];
    }

    /**
     * @param OnFlushEventArgs $onFlushEventArgs
     */
    public function onFlush(OnFlushEventArgs $onFlushEventArgs)
    {
        if (empty($this->auditedEntities)) {
            return;
        }

        $em = $onFlushEventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        $insertions = $uow->getScheduledEntityInsertions();
        $updates = $uow->getScheduledEntityUpdates();

        foreach ($insertions as $entity) {
            list($entity_name, $userid, $table_name, $schema_name, $old_row_data, $row_data) = $this->_getEventData($em, $entity);

            if (in_array($entity_name, $this->auditedEntities)) {
                $this->_processInsertEvent($em, $uow, $entity, $entity_name, $userid, $table_name, $schema_name, $row_data);
            }
        }

        foreach ($updates as $entity) {
            list($entity_name, $userid, $table_name, $schema_name, $old_row_data, $row_data) = $this->_getEventData($em, $entity);

            if (in_array($entity_name, $this->auditedEntities)) {
                $change_set = $uow->getEntityChangeSet($entity);

                if (array_key_exists('activo', $change_set) && $change_set['activo'][1] === false) { // UPDATING an entity with an 'activo' field equals to FALSE will be considered an UPDATE event
                    $this->_processDeleteEvent($em, $uow, $entity, $entity_name, $userid, $table_name, $schema_name, $row_data);
                } else {
                    $this->_processUpdateEvent($em, $uow, $entity, $entity_name, $userid, $table_name, $schema_name, $old_row_data, $row_data);
                }
            }
        }

        foreach ($uow->getScheduledEntityDeletions() as $entity) {
            list($entity_name, $userid, $table_name, $schema_name, $old_row_data, $row_data) = $this->_getEventData($em, $entity);

            if (in_array($entity_name, $this->auditedEntities)) {
                $this->_processDeleteEvent($em, $uow, $entity, $entity_name, $userid, $table_name, $schema_name, $row_data);
            }
        }

        // TODO: procesar getScheduledCollectionUpdates y getScheduledCollectionUpdates para registrar las collecciones en getScheduledEntityInsertions, getScheduledEntityUpdates y getScheduledEntityDeletions

        foreach ($uow->getScheduledCollectionDeletions() as $col) {
            // error_log('CollectionDeletions');
            // TODO
        }

        foreach ($uow->getScheduledCollectionUpdates() as $col) {
            // error_log('CollectionUpdates');
            // TODO
        }
    }

    /**
     * @param EntityManager $em
     * @param object $entity
     * @return array
     */
    private function _getEventData($em, $entity)
    {
        $entity_name = get_class($entity);
        $userid = null;
        $table_name = null;
        $schema_name = null;
        $row_data = [];
        $old_row_data = [];

        if (in_array($entity_name, $this->auditedEntities)) {
            $user = $this->tokenStorage->getToken()->getUser();
            $userid = $user instanceof Usuario ? $user->getId() : $user;

            $metadata = $em->getClassMetadata($entity_name);
            $table_name = $metadata->getTableName();
            $schema_name = $metadata->getSchemaName();

            foreach ($metadata->getFieldNames() as $field) {
                $value = $metadata->getFieldValue($entity, $field);

                if ($value instanceof \DateTime) {
                    $value = $value->format(Constants::DATETIME_FORMAT);
                }

                $row_data[$field] = $value;
            }
            $old_row_data = $row_data;
        }

        return [$entity_name, $userid, $table_name, $schema_name, $old_row_data, $row_data];
    }

    /**
     * @param EntityManager $em
     * @param UnitOfWork $uow
     * @param object $entity
     * @param $entity_name
     * @param $userid
     * @param $table_name
     * @param $schema_name
     * @param $row_data
     */
    private function _processInsertEvent($em, $uow, $entity, $entity_name, $userid, $table_name, $schema_name, $row_data)
    {
        // error_log('EntityInsertions');

        foreach ($uow->getEntityChangeSet($entity) as $field => $value) {
            list($old_value, $new_value) = $value;

            if (is_bool($new_value)) {
                $new_value = $new_value ? 'TRUE' : 'FALSE';
            }

            if (is_object($new_value)) {
                if ($new_value instanceof \DateTime) {
                    $row_data[$field] = $new_value->format(DATE_ISO8601);
                } else {
                    $row_data[$field] = $this->_getEntityIds($new_value, $em->getClassMetadata(get_class($new_value)));
                }
            } else {
                $row_data[$field] = $new_value;
            }
        }

        $this->_persistEvent($em, $userid, $table_name, $schema_name, $entity_name, $row_data, [], Constants::EVENTO_INSERT);
    }

    /**
     * @param EntityManager $em
     * @param UnitOfWork $uow
     * @param object $entity
     * @param string $entity_name
     * @param string $userid
     * @param string $table_name
     * @param string $schema_name
     * @param array $old_row_data
     * @param array $row_data
     */
    private function _processUpdateEvent($em, $uow, $entity, $entity_name, $userid, $table_name, $schema_name, $old_row_data, $row_data)
    {
        // error_log('EntityUpdates');

        foreach ($uow->getEntityChangeSet($entity) as $field => $value) {
            list($old_value, $new_value) = $value;

            if (is_numeric($old_value) && gettype($new_value) !== gettype($old_value)) {
                $old_value = (string)$old_value;
            }

//            if ($old_value instanceof \DateTime) {
//                $old_value = $old_value->format(DATE_ISO8601);
//            }

            if (is_bool($new_value)) {
                $new_value = $new_value ? 'TRUE' : 'FALSE';
            }

            if (is_bool($old_value)) {
                $old_value = $old_value ? 'TRUE' : 'FALSE';
            }

            if (is_object($new_value)) {
                if ($new_value instanceof \DateTime) {
                    $new_value = $new_value->format(Constants::DATETIME_FORMAT);

                    if ($old_value && $old_value instanceof \DateTime) {
                        $old_value = $old_value->format(Constants::DATETIME_FORMAT);
                    }

                    if ($new_value === $old_value) {
                        continue;
                    }

                    $old_row_data[$field] = $old_value;
                    $row_data[$field] = $new_value;
                } else {
                    $old_value = $this->_getEntityIds($old_value, $em->getClassMetadata(get_class($old_value)));
                    $new_value = $this->_getEntityIds($new_value, $em->getClassMetadata(get_class($new_value)));

                    if ($new_value === $old_value) {
                        continue;
                    }

                    $old_row_data[$field] = $old_value;
                    $row_data[$field] = $new_value;
                }
            } else {
                if ($new_value === $old_value) {
                    continue;
                }

                $old_row_data[$field] = $old_value;
                $row_data[$field] = $new_value;
            }
        }

        $changed_fields = array_udiff_assoc(
            $row_data,
            $old_row_data,
            function ($a, $b) {
                return $a === $b ? 0 : 1;
            }
        );

        if (!empty($changed_fields)) {
            $this->_persistEvent($em, $userid, $table_name, $schema_name, $entity_name, $old_row_data, $changed_fields, Constants::EVENTO_UPDATE);
        }
    }

    /**
     * @param EntityManager $em
     * @param UnitOfWork $uow
     * @param object $entity
     * @param $entity_name
     * @param $userid
     * @param $table_name
     * @param $schema_name
     * @param $row_data
     */
    private function _processDeleteEvent($em, $uow, $entity, $entity_name, $userid, $table_name, $schema_name, $row_data)
    {
        // error_log('EntityDeletions');

        foreach ($uow->getEntityChangeSet($entity) as $field => $value) {
            list($old_value, $new_value) = $value;

            if (is_bool($new_value)) {
                $new_value = $new_value ? 'TRUE' : 'FALSE';
            }

            if (is_object($new_value)) {
                if ($new_value instanceof \DateTime) {
                    $row_data[$field] = $new_value->format(Constants::DATETIME_FORMAT);
                } else {
                    $row_data[$field] = $this->_getEntityIds($new_value, $em->getClassMetadata(get_class($new_value)));
                }
            } else {
                $row_data[$field] = $new_value;
            }
        }

        $this->_persistEvent($em, $userid, $table_name, $schema_name, $entity_name, $row_data, [], Constants::EVENTO_DELETE);
    }

    /**
     * @param object|string $entity
     * @param ClassMetadata $metadata
     * @param bool $json
     * @return array
     */
    private function _getEntityIds($entity, $metadata, $json = true)
    {
        return $json ? json_encode($metadata->getIdentifierValues($entity)) : $metadata->getIdentifierValues($entity);
    }

    /**
     * @param EntityManager $em
     * @param string $user
     * @param string $table
     * @param string $schema
     * @param string $entity
     * @param array $old_data
     * @param array $changed_fields
     * @param string $type
     */
    private function _persistEvent($em, $user, $table, $schema, $entity, $old_data, $changed_fields, $type)
    {
        try {
            $evento = new Evento();
            $evento
                ->setFecha(new \DateTime())
                ->setUsuario($user)
                ->setTabla(empty($schema) ? $table : sprintf('%s.%s', $schema, $table))
                ->setEntidad($entity)
                ->setDatos($old_data)
                ->setTipo($type);

            if ($type === Constants::EVENTO_UPDATE) {
                $evento->setDatosModificados($changed_fields);
            }

            $em->persist($evento);
            $em->getUnitOfWork()->computeChangeSet($em->getClassMetadata(get_class($evento)), $evento);
        } catch (\PDOException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}