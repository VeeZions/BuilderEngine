<?php

namespace VeeZions\BuilderEngine\Enum;

enum ColumnEnum: string
{
    // Texts
    case TEXT = 'text';
    case RICH_TEXT = 'rich_text';
    // Dates
    case DATE = 'date';
    case DATE_TIME = 'date_time';
    case TIME = 'time';
    case DURATION = 'duration';
    // Media
    case IMAGE = 'image';
    case AVATAR = 'avatar';
    case FILE = 'file';
    // Numbers
    case INTEGER = 'integer';
    case DECIMAL = 'decimal';
    case BOOLEAN = 'boolean';
    case PERCENT = 'percent';
    // Money
    case PRICE = 'price';
    // Locale
    case COUNTRY = 'country';
}
