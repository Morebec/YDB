<?php 

namespace Morebec\YDB\Entity\Identity;

use Morebec\ValueObjects\Identity\UuidIdentifier;
use Morebec\YDB\Contract\RecordIdInterface;

/**
 * RecordId
 */
class RecordId extends UuidIdentifier implements RecordIdInterface
{
}
