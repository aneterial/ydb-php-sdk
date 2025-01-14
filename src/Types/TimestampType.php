<?php

namespace YdbPlatform\Ydb\Types;

use DateTime;
use Exception;

class TimestampType extends DatetimeType
{
    /**
     * @var string
     */
    protected static $datetime_format = 'Y-m-d\TH:i:s.u\Z';

    protected $ydb_key_name = "uint64_value";

    protected $ydb_type = "TIMESTAMP";
    /**
     * @inherit
     */
    protected function getYqlString()
    {
        return 'Timestamp(' . $this->quoteString($this->value) . ')';
    }

    /**
     * @inherit
     */
    protected function getYdbValue()
    {
        $value = new DateTime($this->value);
        return $value->format("U.u") * 1000000;
    }
}
