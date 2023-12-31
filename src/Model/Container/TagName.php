<?php

namespace Dontdrinkandroot\ActivityPubCoreBundle\Model\Container;

class TagName
{
    public const CONTROLLER = 'controller.service_arguments';
    public const SERIALIZER_ENCODER = 'serializer.encoder';
    public const ROUTING_LOADER = 'routing.loader';
    public const SERIALIZER_NORMALIZER = 'serializer.normalizer';
    public const KERNEL_EVENT_LISTENER = 'kernel.event_listener';
    public const OBJECT_PROVIDER = 'ddr.activity_pub_core.object_provider';
    public const MONOLOG_LOGGER = 'monolog.logger';
}
