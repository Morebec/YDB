<?php 

namespace Morebec\YDB;

use Morebec\ValueObjects\Identity\UuidIdentifier;
use Morebec\YDB\Database\RecordIdInterface;

/**
 * RecordId
 */
class RecordId extends UuidIdentifier implements RecordIdInterface
{    
}