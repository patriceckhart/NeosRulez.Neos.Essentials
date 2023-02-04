<?php
namespace NeosRulez\Neos\Essentials\Domain\Model;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Security\Account;

/**
 * @Flow\Entity
 */
abstract class AbstractUser extends AbstractModel
{

    /**
     * @var Account
     * @ORM\OneToOne(cascade={"persist", "remove"})
     */
    protected $account;

    /**
     * @return Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param Account $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @var boolean
     */
    protected $active = true;

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return void
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

}
