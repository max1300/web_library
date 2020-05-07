<?php


namespace App\DataPersisters;


use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserPersister implements DataPersisterInterface
{
    protected $manager;
    protected $encoder;

    /**
     * UserPersister constructor.
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    /**
     * Is the data supported by the persister?
     * @param $data
     * @return bool
     */
    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * Persists the data.
     *
     * @param $data
     * @return object|void Void will not be supported in API Platform 3, an object should always be returned
     */
    public function persist($data)
    {
        if(!$data instanceof User) {
            return null;
        }

        if ($data->getPassword()) {
            $data->setPassword($this->encoder->encodePassword($data, $data->getPassword()));
        }

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