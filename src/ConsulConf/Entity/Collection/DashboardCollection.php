<?php

namespace ConsulConf\Entity\Collection;

use ConsulConf\Entity\Dashboard;

class DashboardCollection extends Base
{
    protected function initialize()
    {
        $this->setClassMap(Dashboard::class);
    }
}