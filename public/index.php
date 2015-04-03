<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Zalgo\Zalgo;
use Zalgo\Mood;
use Zalgo\Soul;
use Zalgo\Web\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Application();

// Services ---
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../resources/views',
));

$app['zalgo.calm'] = function ($app) {
    return new Zalgo(new Soul(), Mood::soothed());
};

$app['zalgo.enraged'] = function ($app) {
    return new Zalgo(new Soul(), Mood::enraged());
};

$app['zalgo.twitter'] = function ($app) {
    return new Zalgo(new Soul(), Mood::twitter());
};

// Routes ---
$app->get('/', function (Application $app) {
    $message = 'The magic quotes! They are taking me away.';

    $speaks = [
        'calm' => $app['zalgo.calm']->speaks($message),
        'enraged' => $app['zalgo.enraged']->speaks($message),
        'twitter' => $app['zalgo.twitter']->speaks($message)
    ];

    return $app->render('home.twig', compact('speaks'));
});

$app->post('/', function (Request $request, Application $app) {
    $message = $request->get('phrase');

    $speaks = [
        'calm' => $app['zalgo.calm']->speaks($message),
        'enraged' => $app['zalgo.enraged']->speaks($message),
        'twitter' => $app['zalgo.twitter']->speaks($message)
    ];

    return $app->render('home.twig', compact('speaks'));
});

// Error Handling ---
$app->error(function (\Exception $e, $code) {
    switch ($code) {
        case 404:
            $message = 'Requested page could not be found.';
            break;
        default:
            $message = 'Sorry about that! Something\'s going crazy down here.';
            break;
    }

    return new Response($message);
});

// Off we go!
$app->run();