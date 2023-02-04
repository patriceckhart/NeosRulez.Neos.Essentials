<?php
namespace NeosRulez\Neos\Essentials\Domain\Repository;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Repository;

#[Flow\Scope("singleton")]
abstract class AbstractRepository extends Repository
{

    protected $defaultOrderings = array(
        'createdAt' => \Neos\Flow\Persistence\QueryInterface::ORDER_DESCENDING,
    );

}
