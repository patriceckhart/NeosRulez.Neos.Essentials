<?php
namespace NeosRulez\Neos\Essentials\Security;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Mvc\RequestInterface;
use Neos\Flow\Security\RequestPatternInterface;

class NeosRequestPattern implements RequestPatternInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * Expects options in the form array('matchFrontend' => TRUE/FALSE)
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $this->options = $options;
    }

    /**
     * Matches a \Neos\Flow\Mvc\RequestInterface against its set pattern rules
     *
     * @param RequestInterface $request The request that should be matched
     * @return boolean TRUE if the pattern matched, FALSE otherwise
     */
    public function matchRequest(RequestInterface $request)
    {
        if (!$request instanceof ActionRequest) {
            return false;
        }
        $shouldMatchFrontend = isset($this->options['matchFrontend']) && $this->options['matchFrontend'] === true;
        $requestPath = $request->getHttpRequest()->getUri()->getPath();
        $requestPathMatchesBackend = str_starts_with($requestPath, '/neos') || strpos($requestPath, '@') !== false;
        return $shouldMatchFrontend !== $requestPathMatchesBackend;
    }

}
