<?php
namespace NeosRulez\Neos\Essentials\Service;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\PersistenceManagerInterface;

class Operations extends AbstractService
{

    /**
     * @var array
     */
    protected array $settings = [];

    /**
     * @param string $operation
     * @param array $arguments
     * @param array $settings
     * @return void
     */
    public function execute(string $operation, array $arguments = [], array $settings = []): void
    {
        if(empty($settings)) {
            $settings = $this->settings;
        }
        if(array_key_exists($operation, $settings)) {
            if(array_key_exists('class', $settings[$operation])) {
                $class = $this->objectManager->get($settings[$operation]['class']);
                if(empty($arguments)) {
                    $class->execute();
                } else {
                    $class->execute($arguments);
                }
            }
        }
    }

    /**
     * @param string $operation
     * @param array $settings
     * @return bool|string
     */
    public function redirect(string $operation, array $settings = []): bool|string
    {
        if(empty($settings)) {
            $settings = $this->settings;
        }
        if(array_key_exists($operation, $settings)) {
            if(array_key_exists('redirectToUri',$settings[$operation])) {
                return $settings[$operation]['redirectToUri'];
            }
        }
        return false;
    }

}
