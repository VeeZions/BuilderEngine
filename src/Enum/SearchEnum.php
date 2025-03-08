<?php

namespace Vision\BuilderEngine\Enum;

enum SearchEnum: string
{
    case TEXT = 'text';
    case SELECT = 'select';
    case DATE = 'date';
    case TIME = 'time';
    case NUMBER = 'number';
}
