<?php
namespace NeosRulez\Neos\Essentials\Domain\Model;

/*
 * This file is part of the NeosRulez.Neos.Essentials package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Neos\Flow\Persistence\PersistenceManagerInterface;

abstract class AbstractModel
{

    use SoftDeleteableEntity;
    use TimestampableEntity;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->persistenceManager->getIdentifierByObject($this);
    }

    /**
     * @param bool|string $format
     * @return \DateTime|string
     */
    public function getCreated(bool|string $format = false): \DateTime|string
    {
        if($format !== false) {
            return $this->createdAt->format($format);
        }
        return $this->createdAt;
    }

}
