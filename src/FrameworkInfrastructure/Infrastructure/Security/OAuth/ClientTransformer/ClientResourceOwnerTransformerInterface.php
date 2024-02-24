<?php

namespace App\FrameworkInfrastructure\Infrastructure\Security\OAuth\ClientTransformer;

use App\FrameworkInfrastructure\Infrastructure\Security\OAuth\DTO\ResourceOwnerDTO;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;

interface ClientResourceOwnerTransformerInterface
{
    public function transform(ResourceOwnerInterface $resourceOwner): ResourceOwnerDTO;
}
