<?php
namespace NeosRulez\Neos\Essentials\Service;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;

class LoginOperations extends Operations
{

    /**
     * @Flow\InjectConfiguration(package="NeosRulez.Neos.Essentials", path="login")
     * @var array
     */
    protected array $settings = [];

}
