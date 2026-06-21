<?php

namespace mmerlijn\LaravelSalt\Enums;

use mmerlijn\msgRepo\Enums\StringEnumTrait;

enum SendTypeEnum: string
{
    use StringEnumTrait;

    case MIRTH_TCP = 'MIRTH_TCP';
    case MIRTH_HTTP = 'MIRTH_HTTP';
    case HTTP = 'HTTP';
    case FILE = 'FILE';


}
