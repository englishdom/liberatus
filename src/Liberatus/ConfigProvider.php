<?php

namespace Liberatus;

use Liberatus\Action\RefreshAction;

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
//                RefreshAction::class => Factory\CheckActionFactory::class,
            ]
        ];
    }
}
