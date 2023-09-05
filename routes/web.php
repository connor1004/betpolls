<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$router->get('/admin{any:.*}', function () {
    return view('admin.index');
});

$router->group(['namespace' => 'Front'], function () use ($router) {
    $router->get('es/entrada', ['as' => 'front.login.get', 'uses' => 'AuthController@loginGet']);
    $router->post('es/entrada', ['as' => 'front.login.post', 'uses' => 'AuthController@loginPost']);
    $router->get('es/registro', ['as' => 'front.register.get', 'uses' => 'AuthController@registerGet']);
    $router->post('es/registro', ['as' => 'front.register.post', 'uses' => 'AuthController@registerPost']);
    $router->get('es/olvido', ['as' => 'front.forgot.get', 'uses' => 'AuthController@forgotPasswordGet']);
    $router->post('es/olvido', ['as' => 'front.forgot.post', 'uses' => 'AuthController@forgotPasswordPost']);
    $router->get('es/reiniciar', ['as' => 'front.reset.get', 'uses' => 'AuthController@resetPasswordGet']);
    $router->post('es/reiniciar', ['as' => 'front.reset.post', 'uses' => 'AuthController@resetPasswordPost']);

    $router->get('es/perfil', [
        'as' => 'front.profile',
        'uses' => 'ProfileController@index',
        'middleware' => ['auth', 'redirectIfUnconfirmed']
    ]);
    $router->get('es/chat', [
        'as' => 'front.chat',
        'uses' => 'HomeController@chat',
    ]);
    $router->get('es/cerrar-session', [
        'as' => 'front.logout',
        'uses' => 'ProfileController@logout',
        'middleware' => ['auth']
    ]);
    $router->get('es/confirmacion-pendiente', [
        'as' => 'front.pending-confirmation',
        'uses' => 'ProfileController@pendingConfirmation',
        'middleware' => ['auth', 'redirectIfConfirmed']
    ]);
    $router->get('es/enviar-confirmacion', [
        'as' => 'front.send-confirmation',
        'uses' => 'ProfileController@sendConfirmation',
        'middleware' => ['auth', 'redirectIfConfirmed']
    ]);
    $router->get('es/confirmado', [
        'as' => 'front.confirm',
        'uses' => 'ProfileController@confirm',
        'middleware' => ['auth', 'redirectIfConfirmed']
    ]);

    $router->get('es/mejores-picks', [
        'as' => 'front.toppicks',
        'uses' => 'HomeController@toppick'
    ]);

    $router->get('es/tabla-de-posiciones', [
        'as' => 'front.leaderboard',
        'uses' => 'LeaderboardController@index'
    ]);

    $router->get('es/usuarios/{slug}', [
        'as' => 'front.users.get',
        'uses' => 'LeaderboardController@user'
    ]);

    $router->get('es/deportes/{category}/{league}', [
        'as' => 'front.leagues',
        'uses' => 'LeagueController@index'
    ]);

    $router->get('es/deportes/{category}', [
        'as' => 'front.sport-categories',
        'uses' => 'SportCategoryController@index'
    ]);

    $router->post('es/juego/{game}/voto', [
        'as' => 'front.games.vote',
        'uses' => 'GameController@vote'
    ]);

    $router->post('es/futuros/event/{event_id}/voto', [
        'as' => 'front.future.event.vote',
        'uses' => 'FutureController@event_vote'
    ]);

    $router->post('es/futuros/multi/{future_id}/voto', [
        'as' => 'front.future.multi.vote',
        'uses' => 'FutureController@future_vote'
    ]);

    $router->get('es/futuros/{mainSlug}/{subSlug}/{pageSlug}', [
        'as' => 'front.futuros.showPollPage',
        'uses' => 'FutureController@showFuturePollPage'
    ]);

    $router->get('es/futuros/{mainSlug}/{subOrPageSlug}', [
        'as' => 'front.futuros.showSubOrPage',
        'uses' => 'FutureController@showFutureSubOrPage'
    ]);

    $router->get('es/futuros/{mainSlug}', [
        'as' => 'front.future.showFutureMain',
        'uses' => 'FutureController@showFutureMain'
    ]);

    $router->get('es/futuros', [
        'as' => 'front.futuros.index',
        'uses' => 'FutureController@index'
    ]);

    $router->get('es/deporte/{mainSlug}/{subSlug}/{pageSlug}', [
        'as' => 'front.deporte.showSportPollPage',
        'uses' => 'FutureController@showSportPollPage'
    ]);

    $router->get('es/deporte/{mainSlug}/{subOrPageSlug}', [
        'as' => 'front.deporte.showSportOrPage',
        'uses' => 'FutureController@showSportSubOrPage'
    ]);

    $router->get('es/deporte/{mainSlug}', [
        'as' => 'front.deporte.showSportMain',
        'uses' => 'FutureController@showSportMain'
    ]);

    $router->get('es/deporte', function() {
        return redirect('/es');
    });

    $router->get('es/{slug}', [
        'as' => 'front.posts.show',
        'uses' => 'PostController@show'
    ]);

    $router->post('es/{slug}', [
        'as' => 'front.posts.show',
        'uses' => 'PostController@store'
    ]);
    
    $router->get('es', ['as' => 'front.index', 'uses' => 'HomeController@index']);


});

$router->group(['namespace' => 'Front'], function () use ($router) {
    $router->get('/test', ['as' => 'front.test.index', 'uses' => 'TestController@index']);
    $router->get('/sitemap', ['as' => 'front.sitemap.index', 'uses' => 'SitemapController@index']);
    $router->get('/login', ['as' => 'front.login.get', 'uses' => 'AuthController@loginGet']);
    $router->post('/login', ['as' => 'front.login.post', 'uses' => 'AuthController@loginPost']);
    $router->get('/register', ['as' => 'front.register.get', 'uses' => 'AuthController@registerGet']);
    $router->post('/register', ['as' => 'front.register.post', 'uses' => 'AuthController@registerPost']);
    $router->get('/forgot', ['as' => 'front.forgot.get', 'uses' => 'AuthController@forgotPasswordGet']);
    $router->post('/forgot', ['as' => 'front.forgot.post', 'uses' => 'AuthController@forgotPasswordPost']);
    $router->get('/reset', ['as' => 'front.reset.get', 'uses' => 'AuthController@resetPasswordGet']);
    $router->post('/reset', ['as' => 'front.reset.post', 'uses' => 'AuthController@resetPasswordPost']);

    $router->get('/profile', [
        'as' => 'front.profile',
        'uses' => 'ProfileController@index',
        'middleware' => ['auth', 'redirectIfUnconfirmed']
    ]);
    $router->get('/chat', [
        'as' => 'front.chat',
        'uses' => 'HomeController@chat',
    ]);
    $router->get('/logout', [
        'as' => 'front.logout',
        'uses' => 'ProfileController@logout',
        'middleware' => ['auth']
    ]);
    $router->get('/pending-confirmation', [
        'as' => 'front.pending-confirmation',
        'uses' => 'ProfileController@pendingConfirmation',
        'middleware' => ['auth', 'redirectIfConfirmed']
    ]);
    $router->get('/send-confirmation', [
        'as' => 'front.send-confirmation',
        'uses' => 'ProfileController@sendConfirmation',
        'middleware' => ['auth', 'redirectIfConfirmed']
    ]);
    $router->get('/confirm', [
        'as' => 'front.confirm',
        'uses' => 'ProfileController@confirm',
        'middleware' => ['auth', 'redirectIfConfirmed']
    ]);

    $router->get('/top-picks', [
        'as' => 'front.toppicks',
        'uses' => 'HomeController@toppick'
    ]);

    $router->get('/leaderboard', [
        'as' => 'front.leaderboard',
        'uses' => 'LeaderboardController@index'
    ]);

    $router->get('/users/{slug}', [
        'as' => 'front.users.get',
        'uses' => 'LeaderboardController@user'
    ]);

    $router->get('sports/{category}/{league}', [
        'as' => 'front.leagues',
        'uses' => 'LeagueController@index'
    ]);

    $router->get('sports/{category}', [
        'as' => 'front.sport-categories',
        'uses' => 'SportCategoryController@index'
    ]);

    $router->post('/games/{game}/vote', [
        'as' => 'front.games.vote',
        'uses' => 'GameController@vote'
    ]);

    $router->post('/futures/event/{event_id}/vote', [
        'as' => 'front.futures.event.vote',
        'uses' => 'FutureController@event_vote'
    ]);

    $router->post('/futures/multi/{future_id}/vote', [
        'as' => 'front.futures.multi.vote',
        'uses' => 'FutureController@future_vote'
    ]);

    $router->get('/futures/{mainSlug}/{subSlug}/{pageSlug}', [
        'as' => 'front.futures.showPollPage',
        'uses' => 'FutureController@showFuturePollPage'
    ]);

    $router->get('/futures/{mainSlug}/{subOrPageSlug}', [
        'as' => 'front.futures.showSubOrPage',
        'uses' => 'FutureController@showFutureSubOrPage'
    ]);

    $router->get('/futures/{mainSlug}', [
        'as' => 'front.futures.showFutureMain',
        'uses' => 'FutureController@showFutureMain'
    ]);

    $router->get('/futures', [
        'as' => 'front.futures.index',
        'uses' => 'FutureController@index'
    ]);

    $router->get('/sport/{mainSlug}/{subSlug}/{pageSlug}', [
        'as' => 'front.future.showSportPollPage',
        'uses' => 'FutureController@showSportPollPage'
    ]);

    $router->get('/sport/{mainSlug}/{subOrPageSlug}', [
        'as' => 'front.future.showSportOrPage',
        'uses' => 'FutureController@showSportSubOrPage'
    ]);

    $router->get('/sport/{mainSlug}', [
        'as' => 'front.future.showSportMain',
        'uses' => 'FutureController@showSportMain'
    ]);

    $router->get('/sport', function() {
        return redirect('/');
    });

    $router->get('/{slug}', [
        'as' => 'front.posts.show',
        'uses' => 'PostController@show'
    ]);

    $router->post('/{slug}', [
        'as' => 'front.posts.show',
        'uses' => 'PostController@store'
    ]);
    
    $router->get('/', ['as' => 'front.index', 'uses' => 'HomeController@index']);
});
