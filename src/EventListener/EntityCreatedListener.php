<?php

namespace App\EventListener;

use App\Entity\PublishedAtInterface;
use DateTime;
use Doctrine\Persistence\Event\LifecycleEventArgs;

//pour automatiqer la création de date et ne pas se prendre la tête à la créer nous même
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