<?php

namespace Application\Core\Interfaces;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

interface TemplateInterface {

    public function Render($template, $pageTitle, array $params = array());
}