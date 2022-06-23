<?php

namespace App\Enum;

use BenSampo\Enum\Enum;

final class UploadStatus extends Enum
{
    public const NO = 0; //First init
    public const INIT = 10;
    public const SUCCESS = 1;
    public const FAIL = -1;
    public const ERROR = -2;
}


