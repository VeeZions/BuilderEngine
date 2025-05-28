<?php

namespace VeeZions\BuilderEngine\Constant;

class NavigationConstant
{
    public const SUP_HEADER_TYPE = 'sup_header';
    public const HEADER_TYPE = 'header';
    public const FOOTER_TYPE = 'footer';
    public const SUB_FOOTER_TYPE = 'sub_footer';
    
    public function getTypes(): array
    {
        return [
            self::SUP_HEADER_TYPE => 'form.navigation.type.sup_header',
            self::HEADER_TYPE => 'form.navigation.type.header',
            self::FOOTER_TYPE => 'form.navigation.type.footer',
            self::SUB_FOOTER_TYPE => 'form.navigation.type.sub_footer',
        ];
    }
}
