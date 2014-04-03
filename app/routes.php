<?php
namespace App;

use Silex\Application;

/** @var Application $app */
$app->mount('/', new Controller\Cards());
