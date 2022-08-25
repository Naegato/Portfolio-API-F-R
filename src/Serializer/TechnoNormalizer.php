<?php

namespace App\Serializer;

use App\Entity\Techno;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Vich\UploaderBundle\Storage\StorageInterface;

class TechnoNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'AppTechnoNormalizerAlreadyCalled';

    public function __construct(private $rootpath,private StorageInterface $storage)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return !isset($context[self::ALREADY_CALLED]) && $data instanceof Techno;
    }

    /**
     * @param Techno $object
     * @param string|null $format
     * @param array $context
     * @return array|\ArrayObject|bool|float|int|string|void|null
     */
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $object->setBase64(base64_encode(file_get_contents($this->rootpath.'/public'.$this->storage->resolveUri($object,'file'))));
        $object->setTime((new \DateTime())->format('Y') - $object->getTime());
        $context[self::ALREADY_CALLED] = true;
        return $this->normalizer->normalize($object, $format, $context);
    }
}