<?php

/*
 * Created by Samuel Moncarey
 * 31/03/2016
 */

namespace App\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManagerInterface;

class Slugger {
    
    public function __construct() {
    }

    public function sluggifyEntity(EntityManagerInterface $entityManager, $entity, $sourceField, $slugField = 'slug') {
        $getSourceMethod = 'get' . Inflector::classify($sourceField);
        $setSlugMethod = 'set' . Inflector::classify($slugField);

        $slugIndex = 0;
        do {
            $slugIndex++;
            $slug = str_replace(' ', '-', str_replace('_','-',Inflector::tableize($entity->$getSourceMethod())) . ($slugIndex > 1 ? "-$slugIndex" : ''));
            $result = $entityManager->getRepository(get_class($entity))->findBy(array(Inflector::tableize($slugField) => $slug));
        } while (!empty($result));

        $entity->$setSlugMethod($slug);
        return $entity;
    }
    
}
