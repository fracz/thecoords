<?php

namespace App\Infrastructure\Doctrine;

use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\DateTimeType;

/**
 * Saves datetime always as UTC.
 * http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/working-with-datetime.html
 */
class UTCDateTimeType extends DateTimeType
{
    private static $utc = null;

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value === null || is_string($value)) {
            return $value;
        }
        if (is_null(self::$utc)) {
            self::$utc = new DateTimeZone('UTC');
        }
        $value->setTimeZone(self::$utc);
        return $value->format($platform->getDateTimeFormatString());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }
        if (is_null(self::$utc)) {
            self::$utc = new DateTimeZone('UTC');
        }
        $val = DateTime::createFromFormat($platform->getDateTimeFormatString(), $value, self::$utc);
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
        return $val;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return 'utcdatetime';
    }
}
