<?php

namespace XenoLabs\XenoEngine\Constant\Crud;

enum SearchTypeEnum: string
{
    case TEXT = 'text';
    case SELECT = 'select';
    case DATE = 'date';
    case TIME = 'time';
    case NUMBER = 'number';
}
