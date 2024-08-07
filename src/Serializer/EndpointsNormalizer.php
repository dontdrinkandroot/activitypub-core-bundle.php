<?php

namespace Dontdrinkandroot\ActivityPubCoreBundle\Serializer;

use Dontdrinkandroot\ActivityPubCoreBundle\Model\Type\Property\Endpoints;
use Dontdrinkandroot\ActivityPubCoreBundle\Model\Type\Property\Uri;
use Override;
use stdClass;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;

class EndpointsNormalizer implements SerializerAwareInterface, DenormalizerInterface
{
    use SerializerAwareTrait;

    #[Override]
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        return $type === Endpoints::class && $format === ActivityStreamEncoder::FORMAT;
    }

    #[Override]
    public function denormalize($data, string $type, string $format = null, array $context = []): Endpoints
    {
        assert($data instanceof stdClass);
        assert($type === Endpoints::class);

        $endpoints = new Endpoints();
        foreach ( get_object_vars($data) as $key => $value) {
            $endpoints[$key] = $this->getSerializer()->denormalize($value, Uri::class, ActivityStreamEncoder::FORMAT);
        }

        return $endpoints;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Endpoints::class => true
        ];
    }
}
