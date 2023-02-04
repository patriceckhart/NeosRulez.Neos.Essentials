<?php
namespace NeosRulez\Neos\Essentials\Controller;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Error\Messages\Error;
use Neos\Error\Messages\Message;
use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Authentication\Controller\AbstractAuthenticationController;
use Neos\Flow\Security\Cryptography\HashService;
use Neos\Flow\Security\Exception\AuthenticationRequiredException;
use Neos\Fusion\View\FusionView;
use Neos\Flow\Security\Authentication\AuthenticationManagerInterface;
use NeosRulez\Neos\Essentials\Service\LoginOperations;

class LoginController extends AbstractAuthenticationController
{

    protected $defaultViewObjectName = FusionView::class;

    #[Flow\Inject]
    protected LoginOperations $loginOperations;

    /**
     * @return void
     */
    public function indexAction(): void
    {
        if($this->authenticationManager->isAuthenticated() !== false) {
            $this->loginOperations->execute('ifAuthenticated');
            $redirectUri = $this->loginOperations->redirect('ifAuthenticated');
            if($redirectUri) {
                $this->redirectToUri($redirectUri);
            }
        }
    }

    /**
     * Logs all active tokens out and redirects the user to the login form
     *
     * @return void
     */
    public function logoutAction(): void
    {
        parent::logoutAction();
        $this->loginOperations->execute('afterLogout');
        $redirectUri = $this->loginOperations->redirect('afterLogout');
        if($redirectUri) {
            $this->redirectToUri($redirectUri);
        }
    }

    /**
     * Is called if authentication was successful. If there has been an
     * intercepted request due to security restrictions, you might want to use
     * something like the following code to restart the originally intercepted
     * request:
     *
     * if ($originalRequest !== NULL) {
     *     $this->redirectToRequest($originalRequest);
     * }
     * $this->redirect('someDefaultActionAfterLogin');
     *
     * @param ActionRequest $originalRequest
     * @return void
     */
    protected function onAuthenticationSuccess(ActionRequest $originalRequest = null): void
    {

        if ($originalRequest !== NULL) {
            $this->redirectToRequest($originalRequest);
        }

        $this->loginOperations->execute('onAuthenticationSuccess');
        $redirectUri = $this->loginOperations->redirect('onAuthenticationSuccess');
        if($redirectUri) {
            $this->redirectToUri($redirectUri);
        }

    }

    /**
     * Is called if authentication failed.
     *
     * Override this method in your login controller to take any
     * custom action for this event. Most likely you would want
     * to redirect to some action showing the login form again.
     *
     * @param \Neos\Flow\Security\Exception\AuthenticationRequiredException $exception The exception thrown while the authentication process
     * @return void
     */
    protected function onAuthenticationFailure(\Neos\Flow\Security\Exception\AuthenticationRequiredException $exception = null): void
    {
        $this->loginOperations->execute('onAuthenticationFailure');
        $redirectUri = $this->loginOperations->redirect('onAuthenticationFailure');
        if($redirectUri) {
            $this->redirectToUri($redirectUri);
        }
    }

}
