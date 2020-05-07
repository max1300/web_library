<?php

namespace App\EventListener;

use App\Entity\Ressource;
use App\Entity\Comment;
use DateTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class EntityCreatedListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
    $entity = $args->getObject();
        //Ressource listener
        if ($entity instanceof Ressource) {
            $entity->setCreatedAt(new DateTime());
        }
        //Comment listener
        if ($entity instanceof Comment) {
            $entity->setCreatedAt(new DateTime());
        }


    }
}