<?php

namespace Application\Core\Lib;

/**
 * Author: Wahab Qureshi.
 */

abstract class Hooks extends Debugger{

    protected function BeforeApplicationHook(){}

    protected function AfterApplicationHook(){}

    protected function BeforeControllerHook(){}

    protected function AfterControllerHook(){}

    protected function BeforeModelHook(){}

    protected function AfterModelHook(){}

    protected function BeforeRepositoryHook(){}

    protected function AfterRepositoryHook(){}

    protected function BeforeEntityHook(){}

    protected function AfterEntityHook(){}
}