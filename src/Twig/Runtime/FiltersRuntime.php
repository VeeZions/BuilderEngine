<?php

namespace XenoLab\XenoEngine\Twig\Runtime;

use XenoLab\XenoEngine\IntegrityChecks\Constants;
use Twig\Extension\RuntimeExtensionInterface;
use Symfony\Component\Uid\Uuid;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Request;

class FiltersRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }
}
