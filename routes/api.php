<?php

$router->group(['prefix' => 'api'], function () use ($router) {
    // Authentication
    $router->post('login', ['as' => 'login', 'uses' => 'AuthController@login']);
    $router->post('register', ['as' => 'register', 'uses' => 'AuthController@register']);
});

// Admin
$router->group(['prefix' => 'api/admin', 'namespace' => 'Admin'], function () use ($router) {

    // Options
    $router->get('generals/options/{name}', ['as' => 'generals.options.index', 'uses' => 'GeneralOptionController@index']);
    $router->put('generals/options/{name}', ['as' => 'generals.options.update', 'uses' => 'GeneralOptionController@update']);

    // Menus
    $router->post('appearances/menus/reorder', ['as' => 'apperances.menus.reorder', 'uses' => 'AppearanceMenuController@reorder']);
    $router->get('appearances/menus/{menu_type}', ['as' => 'apperances.menus.index', 'uses' => 'AppearanceMenuController@index']);
    $router->post('appearances/menus/{menu_type}', ['as' => 'apperances.menus.store', 'uses' => 'AppearanceMenuController@store']);
    $router->put('appearances/menus/{menu_type}/{id}', ['as' => 'apperances.menus.update', 'uses' => 'AppearanceMenuController@update']);
    $router->delete('appearances/menus/{id}', ['as' => 'apperances.menus.destroy', 'uses' => 'AppearanceMenuController@destroy']);

    // Bet type
    $router->get('generals/bet-types', ['as' => 'generals.bet_types.index', 'uses' => 'GeneralBetTypeController@index']);
    $router->put('generals/bet-types', ['as' => 'generals.bet_types.update', 'uses' => 'GeneralBetTypeController@update']);

    // Users
    $router->get('generals/users', ['as' => 'generals.users.index', 'uses' => 'GeneralUserController@index']);
    $router->get('generals/users/{id}', ['as' => 'generals.users.show', 'uses' => 'GeneralUserController@show']);
    $router->post('generals/users', ['as' => 'generals.users.store', 'uses' => 'GeneralUserController@store']);
    $router->put('generals/users/{id}', ['as' => 'generals.users.update', 'uses' => 'GeneralUserController@update']);
    $router->put('generals/users/{id}/toggle-active', ['as' => 'generals.users.toggleactive', 'uses' => 'GeneralUserController@toggleActive']);
    $router->delete('generals/users/{id}', ['as' => 'generals.users.destroy', 'uses' => 'GeneralUserController@destroy']);
    $router->put('change-password', ['as' => 'change_password', 'uses' => 'GeneralUserController@change_password']);

    // Sport Bet types
    $router->get('sports/bet-types', ['as' => 'sports.bet_types.index', 'uses' => 'SportBetTypeController@index']);

    // Sport Categories
    $router->get('sports/categories', ['as' => 'sports.categories.index', 'uses' => 'SportCategoryController@index']);
    $router->get('sports/categories/pull', ['as' => 'sports.categories.pull', 'uses' => 'SportCategoryController@pull']);
    $router->post('sports/categories', ['as' => 'sports.categories.store', 'uses' => 'SportCategoryController@store']);
    $router->put('sports/categories/{id}/toggle-active', ['as' => 'sports.categories.toggleactive', 'uses' => 'SportCategoryController@toggleActive']);
    $router->put('sports/categories/{id}', ['as' => 'sports.categories.update', 'uses' => 'SportCategoryController@update']);
    $router->put('sports/categories/{current_order}/{desired_order}', ['as' => 'sports.categories.reorder', 'uses' => 'SportCategoryController@reorder']);
    $router->delete('sports/categories/{id}', ['as' => 'sports.categories.destroy', 'uses' => 'SportCategoryController@destroy']);

    // Sport Areas
    $router->get('sports/areas', ['as' => 'sports.areas.index', 'uses' => 'SportAreaController@index']);
    $router->get('sports/areas/pull', ['as' => 'sports.areas.pull', 'uses' => 'SportAreaController@pull']);
    $router->post('sports/areas', ['as' => 'sports.areas.store', 'uses' => 'SportAreaController@store']);
    $router->put('sports/areas/{id}/toggle-active', ['as' => 'sports.areas.toggleactive', 'uses' => 'SportAreaController@toggleActive']);
    $router->put('sports/areas/{id}', ['as' => 'sports.areas.update', 'uses' => 'SportAreaController@update']);
    $router->put('sports/areas/{current_order}/{desired_order}', ['as' => 'sports.areas.reorder', 'uses' => 'SportAreaController@reorder']);
    $router->delete('sports/areas/{id}', ['as' => 'sports.areas.destroy', 'uses' => 'SportAreaController@destroy']);

    // Sport League Teams
    $router->get('sports/leagues/{league_id}/teams', ['as' => 'sports.leagues.teams.index', 'uses' => 'SportLeagueTeamController@index']);
    $router->post('sports/leagues/{league_id}/teams', ['as' => 'sports.leagues.teams.store', 'uses' => 'SportLeagueTeamController@store']);
    $router->delete('sports/leagues/{league_id}/teams/{team_id}', ['as' => 'sports.leagues.teams.destroy', 'uses' => 'SportLeagueTeamController@destroy']);

    // Sport League Divisions
    $router->get('sports/leagues/{league_id}/divisions', ['as' => 'sports.leagues.divisions.index', 'uses' => 'SportLeagueDivisionController@index']);
    $router->get('sports/leagues/{league_id}/divisions/options', ['as' => 'sports.leagues.divisions.options', 'uses' => 'SportLeagueDivisionController@options']);

    // Sport Leagues
    $router->get('sports/leagues', ['as' => 'sports.leagues.index', 'uses' => 'SportLeagueController@index']);
    $router->get('sports/leagues/all', ['as' => 'sports.leagues.all', 'uses' => 'SportLeagueController@all']);
    $router->get('sports/leagues/pull', ['as' => 'sports.leagues.pull', 'uses' => 'SportLeagueController@pull']);
    $router->post('sports/leagues', ['as' => 'sports.leagues.store', 'uses' => 'SportLeagueController@store']);
    $router->put('sports/leagues/{id}/toggle-active', ['as' => 'sports.leagues.toggleactive', 'uses' => 'SportLeagueController@toggleActive']);
    $router->post('sports/leagues/{id}/attach-bet-types', ['as' => 'sports.leagues.attachbettypes', 'uses' => 'SportLeagueController@attachBetTypes']);
    $router->put('sports/leagues/{id}', ['as' => 'sports.leagues.update', 'uses' => 'SportLeagueController@update']);
    $router->put('sports/leagues/{sport_category_id}/{current_order}/{desired_order}', ['as' => 'sports.leagues.reorder', 'uses' => 'SportLeagueController@reorder']);
    $router->delete('sports/leagues/{id}', ['as' => 'sports.leagues.destroy', 'uses' => 'SportLeagueController@destroy']);

    // Sport Teams
    $router->get('sports/teams', ['as' => 'sports.teams.index', 'uses' => 'SportTeamController@index']);
    $router->get('sports/teams/all', ['as' => 'sports.teams.all', 'uses' => 'SportTeamController@all']);
    $router->get('sports/teams/search', ['as' => 'sports.teams.search', 'uses' => 'SportTeamController@search']);
    $router->get('sports/teams/pull', ['as' => 'sports.teams.pull', 'uses' => 'SportTeamController@pull']);
    $router->post('sports/teams', ['as' => 'sports.teams.store', 'uses' => 'SportTeamController@store']);
    $router->put('sports/teams/{id}/toggle-active', ['as' => 'sports.teams.toggleactive', 'uses' => 'SportTeamController@toggleActive']);
    $router->put('sports/teams/{id}', ['as' => 'sports.teams.update', 'uses' => 'SportTeamController@update']);
    $router->delete('sports/teams/{id}', ['as' => 'sports.teams.destroy', 'uses' => 'SportTeamController@destroy']);

    // Games
    $router->get('bets/games', ['as' => 'bets.games.index', 'uses' => 'BetGameController@index']);
    $router->post('bets/games', ['as' => 'bets.games.store', 'uses' => 'BetGameController@store']);
    $router->get('bets/games/import-list', ['as' => 'bets.games.import-list', 'uses' => 'BetGameController@importList']);
    $router->post('bets/games/import', ['as' => 'bets.games.import', 'uses' => 'BetGameController@import']);
    $router->put('bets/games/{id}/pull', ['as' => 'bets.games.pull', 'uses' => 'BetGameController@pull']);
    $router->put('bets/games/{id}/toggle-active', ['as' => 'bets.games.toggle-active', 'uses' => 'BetGameController@toggleActive']);
    $router->put('bets/games/toggle-active', ['as' => 'bets.games.toggle-active-games', 'uses' => 'BetGameController@toggleActiveGames']);
    $router->get('bets/games/{id}', ['as' => 'bets.games.show', 'uses' => 'BetGameController@show']);
    $router->put('bets/games/{id}', ['as' => 'bets.games.update', 'uses' => 'BetGameController@update']);
    $router->delete('bets/games', ['as' => 'bets.games.destroy-games', 'uses' => 'BetGameController@destroyGames']);
    $router->delete('bets/games/{id}', ['as' => 'bets.games.destroy', 'uses' => 'BetGameController@destroy']);

    // Game Bet Types
    $router->post('bets/games/{game_id}/bet-types', ['as' => 'bets.games.bet-types.store', 'uses' => 'BetGameBetTypeController@store']);
    $router->post('bets/games/{game_id}/bet-types/{id}', ['as' => 'bets.games.bet-types.update', 'uses' => 'BetGameBetTypeController@update']);

    // Leaderboard
    $router->put('bets/leaderboard/calculate-total', ['as' => 'bets.leaderboard.calculateTotal', 'uses' => 'BetLeaderboardController@calculateTotal']);

    // Blogs
    $router->get('blogs/posts', ['as' => 'blogs.posts.index', 'uses' => 'BlogPostController@index']);
    $router->get('blogs/posts/search', ['as' => 'blogs.posts.search', 'uses' => 'BlogPostController@search']);
    $router->post('blogs/posts', ['as' => 'blogs.posts.store', 'uses' => 'BlogPostController@store']);
    $router->get('blogs/posts/{id}', ['as' => 'blogs.posts.show', 'uses' => 'BlogPostController@show']);
    $router->put('blogs/posts/{id}', ['as' => 'blogs.posts.update', 'uses' => 'BlogPostController@update']);
    $router->put('blogs/posts/{id}/toggle-active', ['as' => 'blogs.posts.toggle-active', 'uses' => 'BlogPostController@toggleActive']);
    $router->delete('blogs/posts/{id}', ['as' => 'blogs.posts.destroy', 'uses' => 'BlogPostController@destroy']);

    /* Manual Urls */
    // Manual Categories
    $router->get('manual/categories', ['as' => 'manual.categories.index', 'uses' => 'ManualCategoryController@index']);
    $router->post('manual/categories', ['as' => 'manual.categories.store', 'uses' => 'ManualCategoryController@store']);
    $router->put('manual/categories/{id}/toggle-active', ['as' => 'manual.categories.toggleactive', 'uses' => 'ManualCategoryController@toggleActive']);
    $router->put('manual/categories/{id}', ['as' => 'manual.categories.update', 'uses' => 'ManualCategoryController@update']);
    $router->put('manual/categories/{current_order}/{desired_order}', ['as' => 'manual.categories.reorder', 'uses' => 'ManualCategoryController@reorder']);
    $router->delete('manual/categories/{id}', ['as' => 'manual.categories.destroy', 'uses' => 'ManualCategoryController@destroy']);

    // Manual Subcategories
    $router->get('manual/subcategories', ['as' => 'manual.subcategories.index', 'uses' => 'ManualSubcategoryController@index']);
    $router->get('manual/subcategories/all', ['as' => 'manual.subcategories.all', 'uses' => 'ManualSubcategoryController@all']);
    $router->post('manual/subcategories', ['as' => 'manual.subcategories.store', 'uses' => 'ManualSubcategoryController@store']);
    $router->put('manual/subcategories/{id}/toggle-active', ['as' => 'manual.subcategories.toggleactive', 'uses' => 'ManualSubcategoryController@toggleActive']);
    $router->put('manual/subcategories/{id}', ['as' => 'manual.subcategories.update', 'uses' => 'ManualSubcategoryController@update']);
    $router->put('manual/subcategories/{category_id}/{current_order}/{desired_order}', ['as' => 'manual.subcategories.reorder', 'uses' => 'ManualSubcategoryController@reorder']);
    $router->delete('manual/subcategories/{id}', ['as' => 'manual.subcategories.destroy', 'uses' => 'ManualSubcategoryController@destroy']);

    // Manual Countries
    $router->get('manual/countries', ['as' => 'manual.countries.index', 'uses' => 'ManualCountryController@index']);
    $router->post('manual/countries', ['as' => 'manual.countries.store', 'uses' => 'ManualCountryController@store']);
    $router->put('manual/countries/{id}/toggle-active', ['as' => 'manual.countries.toggleactive', 'uses' => 'ManualCountryController@toggleActive']);
    $router->put('manual/countries/{id}', ['as' => 'manual.countries.update', 'uses' => 'ManualCountryController@update']);
    $router->put('manual/countries/{current_order}/{desired_order}', ['as' => 'manual.countries.reorder', 'uses' => 'ManualCountryController@reorder']);
    $router->delete('manual/countries/{id}', ['as' => 'manual.countries.destroy', 'uses' => 'ManualCountryController@destroy']);

    // Manual Candidate Types
    $router->get('manual/candidate-types', ['as' => 'manual.candidate_types.index', 'uses' => 'ManualCandidateTypeController@index']);
    $router->post('manual/candidate-types', ['as' => 'manual.candidate_types.store', 'uses' => 'ManualCandidateTypeController@store']);
    $router->put('manual/candidate-types/{id}/toggle-active', ['as' => 'manual.candidate_types.toggleactive', 'uses' => 'ManualCandidateTypeController@toggleActive']);
    $router->put('manual/candidate-types/{id}', ['as' => 'manual.candidate_types.update', 'uses' => 'ManualCandidateTypeController@update']);
    $router->put('manual/candidate-types/{current_order}/{desired_order}', ['as' => 'manual.candidate_types.reorder', 'uses' => 'ManualCandidateTypeController@reorder']);
    $router->delete('manual/candidate-types/{id}', ['as' => 'manual.candidate_types.destroy', 'uses' => 'ManualCandidateTypeController@destroy']);

    // Manual Candidates
    $router->get('manual/candidates', ['as' => 'manual.candidates.index', 'uses' => 'ManualCandidateController@index']);
    $router->get('manual/candidates/all', ['as' => 'manual.candidates.all', 'uses' => 'ManualCandidateController@all']);
    $router->get('manual/candidates/search', ['as' => 'manual.candidates.search', 'uses' => 'ManualCandidateController@search']);
    $router->post('manual/candidates', ['as' => 'manual.candidates.store', 'uses' => 'ManualCandidateController@store']);
    $router->put('manual/candidates/{id}/toggle-active', ['as' => 'manual.candidates.toggleactive', 'uses' => 'ManualCandidateController@toggleActive']);
    $router->put('manual/candidates/{id}', ['as' => 'manual.candidates.update', 'uses' => 'ManualCandidateController@update']);
    $router->delete('manual/candidates/{id}', ['as' => 'manual.candidates.destroy', 'uses' => 'ManualCandidateController@destroy']);

    // Manual Poll Pages
    $router->get('manual/poll-pages', ['as' => 'manual.poll-pages.index', 'uses' => 'ManualPollPageController@index']);
    $router->get('manual/poll-pages/{id}/whole', ['as' => 'manual.poll-pages.whole', 'uses' => 'ManualPollPageController@showWhole']);
    $router->get('manual/poll-pages/replicates', ['as' => 'manual.poll-pages.replicates', 'uses' => 'ManualPollPageController@replicates']);
    $router->put('manual/poll-pages/{id}/replicate/{replicate_id}', ['as' => 'manual.poll-pages.replicate', 'uses' => 'ManualPollPageController@replicate']);
    $router->post('manual/poll-pages', ['as' => 'manual.poll-pages.store', 'uses' => 'ManualPollPageController@store']);
    $router->put('manual/poll-pages/{id}/toggle-active', ['as' => 'manual.poll-pages.toggle-active', 'uses' => 'ManualPollPageController@toggleActive']);
    $router->put('manual/poll-pages/toggle-active', ['as' => 'manual.poll-pages.toggle-active-poll-pages', 'uses' => 'ManualPollPageController@toggleActivePollPages']);
    $router->get('manual/poll-pages/{id}', ['as' => 'manual.poll-pages.show', 'uses' => 'ManualPollPageController@show']);
    $router->put('manual/poll-pages/{id}', ['as' => 'manual.poll-pages.update', 'uses' => 'ManualPollPageController@update']);
    $router->delete('manual/poll-pages', ['as' => 'manual.poll-pages.destroy-poll-pages', 'uses' => 'ManualPollPageController@destroyPollPages']);
    $router->delete('manual/poll-pages/{id}', ['as' => 'manual.poll-pages.destroy', 'uses' => 'ManualPollPageController@destroy']);

    // Manual Futures
    $router->get('manual/futures', ['as' => 'manual.futures.index', 'uses' => 'ManualFutureController@index']);
    $router->post('manual/futures', ['as' => 'manual.futures.store', 'uses' => 'ManualFutureController@store']);
    $router->put('manual/futures/{id}/toggle-active', ['as' => 'manual.futures.toggleactive', 'uses' => 'ManualFutureController@toggleActive']);
    $router->put('manual/futures/{id}', ['as' => 'manual.futures.update', 'uses' => 'ManualFutureController@update']);
    $router->put('manual/futures/{current_order}/{desired_order}', ['as' => 'manual.futures.reorder', 'uses' => 'ManualFutureController@reorder']);
    $router->delete('manual/futures/{id}', ['as' => 'manual.futures.destroy', 'uses' => 'ManualFutureController@destroy']);

    // Manual Future Answers
    $router->get('manual/future-answers', ['as' => 'manual.future-answers.index', 'uses' => 'ManualFutureAnswerController@index']);
    $router->post('manual/future-answers', ['as' => 'manual.future-answers.store', 'uses' => 'ManualFutureAnswerController@store']);
    $router->put('manual/future-answers/{id}/toggle-active', ['as' => 'manual.future-answers.toggleactive', 'uses' => 'ManualFutureAnswerController@toggleActive']);
    $router->put('manual/future-answers/{id}', ['as' => 'manual.future-answers.update', 'uses' => 'ManualFutureAnswerController@update']);
    $router->put('manual/future-answers/{current_order}/{desired_order}', ['as' => 'manual.future-answers.reorder', 'uses' => 'ManualFutureAnswerController@reorder']);
    $router->delete('manual/future-answers/{id}', ['as' => 'manual.future-answers.destroy', 'uses' => 'ManualFutureAnswerController@destroy']);
    
    // Manual Events
    $router->get('manual/events', ['as' => 'manual.events.index', 'uses' => 'ManualEventController@index']);
    $router->post('manual/events', ['as' => 'manual.events.store', 'uses' => 'ManualEventController@store']);
    $router->put('manual/events/{id}/toggle-active', ['as' => 'manual.events.toggleactive', 'uses' => 'ManualEventController@toggleActive']);
    $router->put('manual/events/{id}', ['as' => 'manual.events.update', 'uses' => 'ManualEventController@update']);
    $router->put('manual/events/{current_order}/{desired_order}', ['as' => 'manual.events.reorder', 'uses' => 'ManualEventController@reorder']);
    $router->delete('manual/events/{id}', ['as' => 'manual.events.destroy', 'uses' => 'ManualEventController@destroy']);
});
