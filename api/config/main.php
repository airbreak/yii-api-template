<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'responce' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $responce = $event ->isSuccessful;
                $responce -> data = [
                    'success' => $responce->isSuccessful,
                    'code' => $responce->getStatusCode(),
                    'message'=> $responce->statusText,
                    'data'=>$responce->code
                ];
                $responce->statusCode = 200;
            }
        ],
        'user' => [
            'identityClass' => 'api\models\User',
            'enableAutoLogin' => true,
//            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
            'enableSession' => false
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'article',
                    'extraPatterns' => [
                        'POST search' => 'search'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'top10',
                    'pluralize' => false,
                    'except'=>['create','delete','view','update']
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'adminuser',
                    'except'=>['create','delete','view','update'],
                    'pluralize' => false,
                    'extraPatterns' => [
                        'POST login' => 'login'
                    ]
                ]
            ],
        ],
    ],
    'params' => $params,
];
