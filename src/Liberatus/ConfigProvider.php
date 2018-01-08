<?php

namespace Liberatus;

use Liberatus\Action\CheckAction;

class ConfigProvider
{
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'factories'  => [
                CheckAction::class => Factory\CheckActionFactory::class,
            ]
        ];
    }
}
