<?php

namespace App\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\SmallIntType;

class TinyintType extends SmallIntType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform)
    {
        return 'TINYINT' . (!empty($fieldDeclaration['unsigned']) ? ' UNSIGNED' : '');
    }


    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return 'tinyint';
    }
}
