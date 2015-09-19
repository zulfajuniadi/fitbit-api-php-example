<?php
session_start();
require '../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv('../');
$dotenv->load();
$app = new Slim\Slim();
$app->config(array(
    'templates.path' => '../templates'
));

$client = new GuzzleHttp\Client();

$app->get('/', function() use ($app, $client) {
    if(!isset($_SESSION['access_token'])) {
        $app->render('login.php');
    } else {
        $result = $client->get('https://api.fitbit.com/1/user/' . $_SESSION['user_id'] . '/profile.json', [
            'headers' => [
                'Authorization' => 'Bearer ' . $_SESSION['access_token']
            ]
        ]);
        $response = json_decode($result->getBody()->__toString());
        $app->render('home.php', ['user' => $response->user]);
    }
});

$app->get('/login', function() use ($app) {
    $app->redirect('https://www.fitbit.com/oauth2/authorize?response_type=code&client_id=' . getenv('CLIENT_ID') . '&scope=heartrate%20profile');
});

$app->get('/callback', function() use ($client, $app) {
    $code = $_GET['code'];
    $response = $client->post('https://api.fitbit.com/oauth2/token', [
        'headers' => [
            'Authorization' => 'Basic ' . base64_encode('' . getenv('CLIENT_ID') . ':' . getenv('CLIENT_SECRET'))
        ],
        'form_params' => [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => getenv('CLIENT_ID'),
            'redirect_uri' => getenv('REDIRECT_URL')
        ]
    ]);
    $response = json_decode($response->getBody()->__toString());
    $_SESSION['access_token'] = $response->access_token;
    $_SESSION['user_id'] = $response->user_id;
    $app->redirect('/');
});

$app->get('/logout', function() use ($app) {
    unset($_SESSION['access_token']);
    unset($_SESSION['user_id']);
    $app->redirect('/');
});

$app->run();