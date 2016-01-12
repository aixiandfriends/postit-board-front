<?php
require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = true;

$app
    ->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => array(
            __DIR__ . '/../app/Resources',
            __DIR__ . '/../src/Aixia/PostitBoardFront/Resources/views',
        )
    ))
;

$app->before(function () use ($app) {
    $app['twig']->addGlobal('layout', $app['twig']->loadTemplate('layout.html.twig'));
});

$app->get('/postits', function() use($app) {
    $client = new GuzzleHttp\Client();
    $res = [];
    try {
        $res = $client->request('GET', 'http://172.16.0.8:8080/app_dev.php/postits');
        $res = json_decode($res->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
    } catch (\Exception $ex) {
        var_export($ex->getMessage());
    }

//    $res = [
//        [ '_id' => [ '$id' => '1' ], 'message' => 'message 1'],
//        [ '_id' => [ '$id' => '2' ], 'message' => 'message 2'],
//    ];
    return $app['twig']->render('default.html.twig', [
        'postits' => $res
    ]);
})->bind('homepage');

$app->get('/', function() use($app) {
    return $app->redirect('/postits');
});

$app->match('/edit/{id}', function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
    $id = $request->get('id');

    if ($request->isMethod('POST')) {
        $message = $request->get('message');

        $client = new GuzzleHttp\Client();
        try {
            $request = new GuzzleHttp\Psr7\Request('PATCH', 'http://172.16.0.8:8080/app_dev.php/postits/'.$id,
                [
                    'Content-Type' => 'application/json;charset=UTF-8'
                ],
                json_encode([
                    'post_it' => [
                        'message' => utf8_encode($message)
                    ]
                ])
            );
            $client->send($request);
        } catch (\Exception $ex) {
            var_export($ex->getMessage());
        }
    }

    $res = [];
    try {
        $client = new GuzzleHttp\Client();
        $res = $client->request('GET', 'http://172.16.0.8:8080/app_dev.php/postits/'.$id);
        $res = json_decode($res->getBody()->getContents(), JSON_OBJECT_AS_ARRAY);
    } catch (\Exception $ex) {
        var_export($ex->getMessage());
    }

    return $app['twig']->render('edit.html.twig', [
        'postit' => $res
    ]);
})->bind('edit');

$app->match('/new', function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
    if ($request->isMethod('POST')) {
        $message = $request->get('message');

        $client = new GuzzleHttp\Client();
        $request = new GuzzleHttp\Psr7\Request('POST', 'http://172.16.0.8:8080/app_dev.php/postits',
            [
                'Content-Type' => 'application/json;charset=UTF-8'
            ],
            json_encode([
                'post_it' => [
                    'message' => utf8_encode($message)
                ]
            ])
        );
        $client->send($request);
        return $app->redirect('/postits');
    }

    return $app['twig']->render('new.html.twig');
})->bind('new');

$app->match('/delete/{id}', function(\Symfony\Component\HttpFoundation\Request $request) use($app) {
    $id = $request->get('id');
    try {
        $client = new GuzzleHttp\Client();
        $client->request('DELETE', 'http://172.16.0.8:8080/app_dev.php/postits/'.$id);
        return $app->redirect('/postits');
    } catch (\Exception $ex) {
        var_export($ex->getMessage());
    }
})->bind('delete');


$app->run();
