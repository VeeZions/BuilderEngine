<?php

namespace VeeZions\BuilderEngine\DataCollector;

use Symfony\Bundle\FrameworkBundle\DataCollector\AbstractDataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BuilderCollector extends AbstractDataCollector
{
    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $this->data = [
            'route' => $request->get('_route'),
            'pages' => [],
        ];
    }

    public function getName(): string
    {
        return 'veezions_collector';
    }

    public function getRoute(): string
    {
        return $this->data['route'];
    }

    public function getPages(): array
    {
        return $this->data['pages'];
    }
}
