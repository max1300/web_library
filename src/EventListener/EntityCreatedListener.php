<?php

namespace App\EventListener;

use App\Entity\PublishedAtInterface;
use DateTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class EntityCreatedListener
{
    public function prePersist(LifecycleEventArgs $args)
    {
    $entity = $args->getObject();
        //Ressource listener

        if (!$entity instanceof PublishedAtInterface){
            return;
        }
        $entity->setCreatedAt(new DateTime());
    }
}