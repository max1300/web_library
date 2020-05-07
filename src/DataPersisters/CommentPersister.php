<?php


namespace App\DataPersisters;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Comment;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CommentPersister implements DataPersisterInterface
{
    protected $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Is the data supported by the persister?
     * @param $data
     * @return bool
     */
    public function supports($data): bool
    {
        return $data instanceof Comment;
    }

    /**
     * Persists the data.
     *
     * @param $data
     * @return object|void Void will not be supported in API Platform 3, an object should always be returned
     * @throws Exception
     */
    public function persist($data)
    {
        $data->setCreatedAt(new DateTime());
        $this->manager->persist($data);
        $this->manager->flush();
    }

    /**
     * Removes the data.
     * @param $data
     */
    public function remove($data)
    {
        $this->manager->remove($data);
        $this->manager->flush();
    }
}