<?php

namespace VeeZions\BuilderEngine\Enum;

enum LibraryEnum: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';
    case DOCUMENT = 'document';
    case UNKNOWN = 'unknown';
}
