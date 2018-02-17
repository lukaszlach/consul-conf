<?php

namespace ConsulConf;

use ConsulConf\Service\User;

/**
 * @property \ConsulConf\Service\ConsulKv $consul_kv
 * @property \ConsulConf\Service\Shell $shell
 * @property \ConsulConf\Service\User $user
 * @property \ConsulConf\Service\Config $config
 * @property \ConsulConf\Service\Dashboard $dashboards
 * @property \ConsulConf\Service\Notify $notify
 */
class Application extends \Phalcon\Mvc\Micro
{

    public function bootstrap()
    {
        $this->setHeadersMiddleware();
        $this->setServices();
        $this->setRouter();
        $this->setEvents();
        $this->setAuthMiddleware();
    }

    private function setServices()
    {
        $app = $this;
        $this->setService('config', \ConsulConf\Service\Config::class, true);
        $this->setService('consul_kv', function () use ($app) {
            $consulKv = new \ConsulConf\Service\ConsulKv();
            $consulKv->setShell($app->shell);
            $consulKv->setNotify($app->notify);

            return $consulKv;
        }, true);
        $this->setService('dashboards', function () use ($app) {
            $consulKv = new \ConsulConf\Service\Dashboard();
            $consulKv->setConsulKv($app->consul_kv);

            return $consulKv;
        });
        $this->setService('user', function () use ($app) {
            static $user;
            if (null === $user) {
                $user   = new \ConsulConf\Service\User();
                $shell  = new \ConsulConf\Service\Shell();
                $shell->setUser($user);
                $notify = new \ConsulConf\Service\Notify();
                $notify->setUser($user);
                $notify->setShell($shell);
                $notify->setConfig($app->config);
                $user->setShell($shell);
                $user->setNotify($notify);
                $user->setConfig($app->config);
                $user->initialize();
            }

            return $user;
        }, true);
        $this->setService('shell', function () use ($app) {
            $shell = new \ConsulConf\Service\Shell();
            $shell->setUser($app->user);

            return $shell;
        }, true);
        $this->setService('notify', function () use ($app) {
            $notify = new \ConsulConf\Service\Notify();
            $notify->setShell($app->shell);
            $notify->setUser($app->user);
            $notify->setConfig($app->config);

            return $notify;
        }, true);
        $this->setService('view', function () {
            $view = new \Phalcon\Mvc\View();
            $view->setViewsDir(__DIR__ . '/../tpl/');
            $view->registerEngines([
                '.phtml' => \Phalcon\Mvc\View\Engine\Volt::class,
            ]);

            return $view;
        });
    }

    private function setRouter()
    {
        $this->router->setUriSource(\Phalcon\Mvc\Router::URI_SOURCE_SERVER_REQUEST_URI);
        $app = $this;
        /* Index */
        $this->get(
            '/',
            function () use ($app) {
                if (!$this->user->isLoggedIn()) {
                    $app->response->redirect('login')->send();
                } else {
                    $app->response->redirect('d')->send();
                }
            }
        );
        /* Log in */
        $this->get(
            '/login',
            function () use ($app) {
                if ($this->user->isLoggedIn()) {
                    $app->response->redirect('d')->send();

                    return;
                }
                $app->response->setContent($app->view
                    ->start()
                    ->render('login', 'index', [])
                    ->finish()
                    ->getContent()
                );
            }
        );
        $this->post(
            '/login',
            function () use ($app) {
                $username = $app->request->getPost('uid');
                $password = $app->request->getPost('password');
                $acl      = $this->request->getPost('acl');
                $loggedIn = $app->user->logIn($username, $password);
                if ($loggedIn && !empty($acl)) {
                    $this->user->setConsulAclToken($acl);
                }
                $app->response->setJsonContent($loggedIn);
            }
        );
        /* Log out */
        $this->get(
            '/logout',
            function () use ($app) {
                $app->user->logOut();
                $app->response->redirect('');
            }
        );
        /* Dashboards list */
        $this->get(
            '/d',
            function () use ($app) {
                $app->response->setContent($app->view
                    ->start()
                    ->render('dashboard', 'index', [
                        'dashboardCollection' => $app->dashboards->findDashboards()
                    ])
                    ->finish()
                    ->getContent()
                );
            }
        );
        /* Dashboard */
        $this->get(
            '/d/{path:.*}',
            function ($path) use ($app) {
                $dashboard = $app->dashboards->getDashboard($path);
                if (null === $dashboard || $dashboard->isHidden()) {
                    $app->response->redirect('d')->send();

                    return;
                }
                $app->response->setContent($app->view
                    ->start()
                    ->render('dashboard', 'show', [
                        'dashboardEntity' => $dashboard,
                        'fieldCollection' => $app->dashboards->getDashboardFields($dashboard),
                    ])
                    ->finish()
                    ->getContent()
                );
            }
        );
        $this->post(
            '/d/{path:.*}',
            function ($path) use ($app) {
                $fields = $app['request']->getPost();
                foreach ($fields as $key => $value) {
                    $app->consul_kv->set($key, $value, false);
                }
                $app->response->setJsonContent(true)->send();
            }
        );
    }

    private function setHeadersMiddleware()
    {
        $app = $this;
        $this->after(
            function () use ($app) {
                $app->response->setHeader(
                    'X-Powered-By',
                    sprintf('%s %s',
                        $app->config->getEnv('PROJECT_NAME'),
                        $app->config->getEnv('PROJECT_VERSION')
                    )
                );
                // regenerate session cookie
                $app->response->setHeader(
                    'Set-Cookie',
                    sprintf(
                        '%s=%s; path=/; HttpOnly',
                        User::COOKIE_SESSION_ID,
                        urlencode($app['user']->pack())
                    )
                );

                return true;
            }
        );
    }

    private function setEvents()
    {
        $app = $this;
        $this->notFound(
            function () use ($app) {
                $app->response->setStatusCode(404, 'Not Found');
                $app->response->setContent('');
                $app->response->send();
            }
        );
        $app->finish(
            function () use ($app) {
                !$app->response->isSent() && $app->response->send();
            }
        );
    }

    private function setAuthMiddleware()
    {
        $app = $this;
        $this->before(function () use ($app) {
            switch ($this->request->getURI()) {
                case '/':
                case '/login':
                    return true;
            }
            if (!$app->user->isLoggedIn()) {
                $this->response->redirect('login')->send();
                // @todo as return false does not work
                die;
            }

            return true;
        });
    }
}