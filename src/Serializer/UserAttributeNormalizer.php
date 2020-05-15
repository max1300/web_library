<?php


namespace App\Serializer;


use App\Entity\User;
use LogicException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserAttributeNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
{

    public const USER_ATTRIBUTE_ALREADY_CALLED = 'USER_ATTRIBUTE_ALREADY_CALLED';

    private $tokenStorage;
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * UserAttributeNormalizer constructor.
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * {@inheritdoc}
     *
     * @param array $context options that normalizers have access to
     */
    public function supportsNormalization($data, $format = null, array $context = [])
    {
        if (isset($context[self::USER_ATTRIBUTE_ALREADY_CALLED])) {
            return false;
        }

        return $data instanceof User;
    }

    public function normalize($object, $format = null, array $context = [])
    {
        if ($this->isUserHimself($object)) {
            $context['groups'][] = "user:get-owner";
        }

        return $this->passOn($object, $format, $context);
    }

    private function isUserHimself($object)
    {
        return $object->getUsername() === $this->tokenStorage->getToken()->getUsername();
    }

    private function passOn($object, $format, $context)
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException(
                sprintf(
                    'Ne peut pas normalizer cet objet "%s" car le serializer injecté nest pas un normalizer',
                    $object
                )
            );
        }
        $context[self::USER_ATTRIBUTE_ALREADY_CALLED] = true;


        return $this->serializer->normalize($object, $format, $context);

    }

    /**
     * Sets the owning Serializer object.
     * @param SerializerInterface $serializer
     */
    public function setSerializer(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
}