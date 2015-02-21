<?php

namespace Application\Core\Lib;

Class Controller extends AppMethods {
    public function __construct() {
        $this->BeforeControllerHook();
    }

    public function Render($view, $title, array $params = array()) {
        return $this->getComponent(\Get::Config('templateHandler.component'))->Render($view, $title, $params);
    }

    public function ForwardTo($route, $urlQueryString = null) {
        return $this->getComponent(\Get::Config('routeHandler.component'))->ForwardTo($route, $urlQueryString);
    }

    public function ForwardToController($route, array $variables = array()) {
        return $this->getComponent(\Get::Config('routeHandler.component'))->ForwardToController($route, $variables);
    }

    public function UnderDevelopmentAction()
    {
        return $this->Render(':UnderDevelopment:SiteUnderDevelopment.html.php', 'Site Under Development');
    }

    public function __destruct() {
        $this->AfterControllerHook();
    }
}