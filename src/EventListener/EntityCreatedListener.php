<?php

namespace App\EventListener;

use App\Entity\Ressource;
use DateTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class EntityCreatedListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
    $entity = $args->getObject();

    if ($entity instanceof Ressource) {
        $entity->setCreatedAt(new DateTime());
    }
    
    }
}