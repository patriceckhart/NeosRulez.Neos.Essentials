<?php
namespace NeosRulez\Neos\Essentials\Service;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\RequestPatternInterface;
use Neos\Flow\Security\Policy\PolicyService;
use Neos\Flow\Security\Authentication\AuthenticationManagerInterface;
use Neos\Flow\Security\AccountFactory;
use Neos\Flow\Security\Cryptography\HashService;
use Neos\Flow\Security\AccountRepository;
use Neos\Flow\Security\Context;
use Neos\Flow\Security\Account;

/**
 * @Flow\Scope("singleton")
 */
class UserService extends AbstractService
{

    /**
     * @var AuthenticationManagerInterface
     * @Flow\Inject
     */
    protected $authenticationManager;

    /**
     * @var Context
     * @Flow\Inject
     */
    protected $securityContext;

    /**
     * @var AccountFactory
     * @Flow\Inject
     */
    protected $accountFactory;

    /**
     * @var HashService
     * @Flow\Inject
     */
    protected $hashService;

    /**
     * @var AccountRepository
     * @Flow\Inject
     */
    protected $accountRepository;

    /**
     * @Flow\InjectConfiguration(package="NeosRulez.Neos.Essentials", path="account")
     * @var array
     */
    protected $accountSettings;

    /**
     * @Flow\InjectConfiguration(package="NeosRulez.Neos.Essentials", path="user")
     * @var array
     */
    protected $userSettings;

    /**
     * @Flow\InjectConfiguration(package="Neos.Flow", path="security")
     * @var array
     */
    protected $securitySettings;

    #[Flow\Inject]
    protected Operations $operations;

    /**
     * @param int $length
     * @return string
     */
    public function generatePassword(int $length = 12): string
    {
        $chars = '23456789bcdfhkmnprstvzBCDFHJKLMNPRSTVZ';
        $shuffled = str_shuffle($chars);
        return mb_substr($shuffled, 0, $length);
    }

    /**
     * @param string $email
     * @param string|null $password
     * @param string|null $role
     * @param bool $cli
     * @return Account
     */
    public function createUser(string $email, string|null $password = null, string|null $role = null, bool $cli = false): Account
    {
        $authenticationProviderName = array_key_first($this->securitySettings['authentication']['providers']);

        if($role === null) {
            $role = $this->accountSettings['defaultRole'];
        }

        if($password === null) {
            $password = $this->generatePassword();
        }

        $account = $this->accountFactory->createAccountWithPassword($email, $password, [$role], $authenticationProviderName);
        $account->setCredentialsSource($this->hashService->hashPassword($password));
        $this->accountRepository->add($account);

        if(!$cli) {
            $this->operations->execute('afterCreateAccount', ['account' => $account, 'password' => $password], $this->accountSettings);
        }

        return $account;
    }

    /**
     * @return Account|null
     */
    public function getLoggedInAccount(): Account|null
    {
        if($this->securityContext->canBeInitialized()) {
            if ($this->authenticationManager->isAuthenticated() === true) {
                return $this->securityContext->getAccount();
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getLoggedInUser(): mixed
    {
        if($this->securityContext->canBeInitialized()) {
            if ($this->authenticationManager->isAuthenticated() === true) {
                if(!empty($this->userSettings)) {
                    if(array_key_exists('repository', $this->userSettings)) {
                        if(array_key_exists('class', $this->userSettings['repository'])) {
                            $userRepository = $this->objectManager->get($this->userSettings['repository']['class']);
                            return $userRepository->findByAccount($this->securityContext->getAccount()) ? $userRepository->findByAccount($this->securityContext->getAccount())->getFirst() : null;
                        }
                    }
                }
            }
        }
        return null;
    }

}
