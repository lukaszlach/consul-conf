<?php

namespace ConsulConf\Service;

use ConsulConf\Entity\Collection\DashboardCollection;
use ConsulConf\Entity\Collection\FieldCollection;
use ConsulConf\Entity\Dashboard as DashboardEntity;

class Dashboard
{
    const CONSUL_PATH_DASHBOARD        = '.dashboard';
    const CONSUL_PATH_DASHBOARD_CONFIG = '.dashboard/.config';

    /** @var ConsulKv */
    protected $consulKv;

    /**
     * @param string $basePath
     * @return DashboardCollection
     */
    public function findDashboards($basePath = '')
    {
        $dashboardPaths = $this->consulKv->findAllDashboardPaths();
        $result = [];
        foreach ($dashboardPaths as $dashboardPath) {
            $dashboard = $this->consulKv->get("$dashboardPath/".self::CONSUL_PATH_DASHBOARD_CONFIG);
            if (null === $dashboard || '' === $dashboard) {
                continue;
            }
            $dashboard = $this->consulKv->jsonDecode($dashboard);
            $dashboard['path'] = $dashboardPath;
            $result[] = $dashboard;
        }
        $dashboardCollection = new DashboardCollection($result);

        return $dashboardCollection;
    }

    /**
     * @param string $path
     * @return DashboardEntity|null
     */
    public function getDashboard($path)
    {
        $dashboardData = $this->consulKv->getJson("$path/".self::CONSUL_PATH_DASHBOARD_CONFIG);
        if (!is_array($dashboardData)) {

            return null;
        }
        $dashboardData['path'] = $path;
        $dashboardCollection   = new DashboardCollection([$dashboardData]);
        $dashboard             = $dashboardCollection->getOne();

        return $dashboard;
    }

    /**
     * @param DashboardEntity $dashboard
     * @return FieldCollection
     */
    public function getDashboardFields(DashboardEntity $dashboard)
    {
        $values = $this->consulKv->getAllValues($dashboard->getPath());
        $fieldCollection = new FieldCollection($values);
        $fieldCollection->setBasePath($dashboard->getPath());

        return $fieldCollection;
    }

    /**
     * @param ConsulKv $consulKv
     */
    public function setConsulKv(ConsulKv $consulKv)
    {
        $this->consulKv = $consulKv;
    }
}