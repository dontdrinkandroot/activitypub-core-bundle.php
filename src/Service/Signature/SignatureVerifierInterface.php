<?php

namespace Dontdrinkandroot\ActivityPubCoreBundle\Service\Signature;

use Dontdrinkandroot\ActivityPubCoreBundle\Model\Type\Extended\Actor\Actor;
use Symfony\Component\HttpFoundation\Request;

interface SignatureVerifierInterface
{
    public function verifyRequest(Request $request): Actor;
}
