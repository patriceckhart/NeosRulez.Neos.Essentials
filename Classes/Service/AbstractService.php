<?php
namespace NeosRulez\Neos\Essentials\Service;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;

abstract class AbstractService
{

    protected array $settings;

    public function injectSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    /**
     * @Flow\Inject
     * @var ObjectManagerInterface
     */
    protected $objectManager;

}
