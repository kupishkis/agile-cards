<?php
/**
 * app.php
 *
 * @filesource
 * @created 2014-04-03
 */

namespace App;

use Silex\Application;
use Silex\Provider;

require_once __DIR__ . '/../vendor/autoload.php';

chdir(__DIR__);

$app          = new Application();
$app['debug'] = true;

$app->register(
    new Provider\TwigServiceProvider(),
    ['twig.path' => __DIR__ . '/views']
);
$app->register(new Provider\ServiceControllerServiceProvider());
$app->register(new Provider\FormServiceProvider());
$app->register(new Provider\ValidatorServiceProvider());
$app->register(new Provider\TranslationServiceProvider());
$app->register(new Provider\UrlGeneratorServiceProvider());

require 'routes.php';
