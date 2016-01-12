<?php
$loader = require_once __DIR__.'/../vendor/autoload.php';

define('ROOT_PATH', __DIR__ . '/..');
define('APP_PATH', ROOT_PATH . '/app');

$loader->add('Aixia', ROOT_PATH . '/src');

$app = new Silex\Application();
$app['debug'] = true;

$app
    ->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => array(
            __DIR__ . '/../app/Resources',
            __DIR__ . '/../src/Aixia/PostitBoardFront/Resources/views',
        ),
        'twig.cache' => array(
            'cache' => __DIR__ . '/../app/cache'
        )
    ))
;

$app->before(function () use ($app) {
    $app['twig']->addGlobal('layout', $app['twig']->loadTemplate('layout.html.twig'));
});


$app['rest.client'] = new \Aixia\PostitBoardFront\RestClient();

$app->get('/postits', function() use ($app) {
    return $app['twig']->render('default.html.twig', [
        'postits' => $app['rest.client']->get('postits')
    ]);
})->bind('homepage');

$app->get('/', function() use($app) {
    return $app->redirect('/postits');
});

$app->match('/edit/{id}', function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
    $id = $request->get('id');

    if ($request->isMethod('POST')) {
        $message = $request->get('message');

        $app['rest.client']->patch('postits', $id, [
            'post_it' => [
                'message' => utf8_encode($message)
            ]
        ]);
    }

    $res = $app['rest.client']->get('postits', $id);

    return $app['twig']->render('edit.html.twig', [
        'postit' => $res
    ]);
})->bind('edit');

$app->match('/new', function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
    if ($request->isMethod('POST')) {
        $message = $request->get('message');
        $app['rest.client']->post('postits',
            [
                'post_it' => [
                    'message' => utf8_encode($message)
                ]
            ]
        );

        return $app->redirect('/postits');
    }

    return $app['twig']->render('new.html.twig');
})->bind('new');

$app->match('/delete/{id}', function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
    $app['rest.client']->delete('postits', $request->get('id'));
    return $app->redirect('/postits');
})->bind('delete');


$app->run();
