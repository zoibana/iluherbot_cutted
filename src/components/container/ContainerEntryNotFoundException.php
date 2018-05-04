<?php

namespace app\components\container;


use Psr\Container\NotFoundExceptionInterface;

class ContainerEntryNotFoundException  extends \Exception implements NotFoundExceptionInterface {

}