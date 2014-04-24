<?php

namespace SymBB\Core\SystemBundle\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * My custom datatype.
 */
class TimestampType extends Type
{

    const TIMESTAMP = 'timestamp'; // modify to match your type name
    const FORMAT = 'Y-m-d H:i:s';
    
    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TIMESTAMP';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $timestamp = 0;

        if ($value !== '0000-00-00 00:00:00') {
            $date = \DateTime::createFromFormat(self::FORMAT, $value);
            $timestamp = $date->getTimestamp();
        }

        return $timestamp;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $date = \date(self::FORMAT, $value);
        return $date;
    }

    public function getName()
    {
        return self::TIMESTAMP;
    }
}