<?php

namespace Application\Core\Interfaces;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

interface Template {

    public function Render($template, $pageTitle, array $params = array());

    public function RenderView($template, $params = array(), $extract = true);

    public function Render404Response();

    public function Render500Response();

    public function EndOutput();

    public function GetOutput();

    public function IncludeView($template, array $params = array());
}