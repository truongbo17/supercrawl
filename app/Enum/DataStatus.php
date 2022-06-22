<?php

namespace App\Enum;

use BenSampo\Enum\Enum;

final class DataStatus extends Enum
{
    public const INIT = 0;
    public const HAS_DATA = 1;
    public const NO_DATA = -1;
    public const GET_DATA_ERROR = -2;
}
