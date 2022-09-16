<?php

namespace Geocuba\AdminBundle\Util;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;

/**
 * Class CustomIdGenerator
 * @package Geocuba\AdminBundle\Util
 */
class CustomIdGenerator extends AbstractIdGenerator
{

    /**
     * @param EntityManager $em
     * @param \Doctrine\ORM\Mapping\Entity $entity
     * @return string
     */
    public function generate(EntityManager $em, $entity)
    {
        return $em->getConnection()->fetchAll('SELECT clone.get_id()')[0]['get_id'];
    }
}