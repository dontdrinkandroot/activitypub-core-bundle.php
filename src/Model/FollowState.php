<?php

namespace Dontdrinkandroot\ActivityPubCoreBundle\Model;

enum FollowState: int
{
    case PENDING = 0;
    case ACCEPTED = 1;
}
