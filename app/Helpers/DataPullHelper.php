<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use App\SportCategory;
use App\SportArea;
use App\League;
use App\LeagueDivision;
use App\Team;
use App\Game;
use App\LeagueTeam;

class DataPullHelper
{
    protected $client = null;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'http://www.conectate.com.do/deportes/api/',
            'proxy' => getenv('HTTP_PROXY'),
        ]);
    }

    public function pullSportCategory()
    {
        $response = $this->client->request('GET', 'categories');
        $originals = json_decode($response->getBody());
        foreach ($originals as $original) {
            $sport = SportCategory::withTrashed()
                ->find($original->sport_category_id);

            if ($sport) {
                $sport->name = $original->sport_category_name;
                if (!$sport->name_es) {
                    $sport->name_es = $original->sport_category_name;
                }
                $sport->save();
            } else {
                SportCategory::create([
                    'id' => $original->sport_category_id,
                    'name' => $original->sport_category_name,
                    'name_es' => $original->sport_category_name,
                    'slug' => str_slug($original->sport_category_name),
                    'slug_es' => str_slug($original->sport_category_name),
                    'display_order' => SportCategory::getNextDisplayOrder(),
                ]);
            }
        }
    }

    public function pullSportArea()
    {
        $response = $this->client->request('GET', 'countries');
        $originals = json_decode($response->getBody());

        foreach ($originals as $original) {
            $sportArea = SportArea::withTrashed()
                ->find($original->sport_country_id);

            if ($sportArea) {
                $sportArea->name = $original->sport_country_name;
                if (!$sportArea->name_es) {
                    $sportArea->name_es = $original->sport_country_name;
                }
                $sportArea->save();
            } else {
                SportArea::create([
                    'id' => $original->sport_country_id,
                    'name' => $original->sport_country_name,
                    'name_es' => $original->sport_country_name,
                    'slug' => str_slug($original->sport_country_name),
                    'slug_es' => str_slug($original->sport_country_name),
                    'display_order' => SportArea::getNextDisplayOrder(),
                ]);
            }
        }
    }

    public function createLeagueDivision($league_division_data, $league, $parent_id, $position, $display_order)
    {
        $league_division = LeagueDivision::where([
            'league_id' => $league->id,
            'parent_id' => $parent_id,
            'position' => $position,
        ])->first();
        if (!$league_division) {
            $league_division = LeagueDivision::create([
                'parent_id' => $parent_id,
                'league_id' => $league->id,
                'name' => $league_division_data->name,
                'name_es' => $league_division_data->name,
                'position' => $position,
                'display_order' => $display_order,
            ]);
        } else {
            $league_division->name = $league_division_data->name;
            $league_division->name_es = $league_division_data->name;
            $league_division->display_order = $display_order;
            $league_division->save();
        }

        return $league_division;
    }

    public function createTeams($data, $league, $league_division_id)
    {
        if (isset($data->teams)) {
            foreach ($data->teams as $team_data) {
                if (empty($team_data->local_id)) {
                    continue;
                }
                $properties = ['logo', 'name', 'name_es', 'short_name', 'short_name_es'];
                $propertyValid = true;
                foreach ($properties as $property) {
                    if (!property_exists($team_data, $property)) {
                        $propertyValid = false;
                        break;
                    }
                }
                if (!$propertyValid) {
                    continue;
                }
                $team = Team::where('ref_id', $team_data->local_id)->first();
                if ($team) {
                    $team->logo = $team_data->logo;
                    $team->name = $team_data->name;
                    $team->name_es = $team_data->name_es;
                    $team->short_name = $team_data->short_name;
                    $team->short_name_es = $team_data->short_name_es;
                    $team->save();
                } else {
                    $team = Team::create([
                        'ref_id' => $team_data->local_id,
                        'logo' => $team_data->logo,
                        'sport_category_id' => $league->sport_category_id,
                        'sport_area_id' => $league->sport_area_id,
                        'name' => $team_data->name,
                        'name_es' => $team_data->name_es,
                        'short_name' => $team_data->short_name,
                        'short_name_es' => $team_data->short_name_es,
                        'slug' => str_slug($team_data->name),
                        'slug_es' => str_slug($team_data->name_es),
                    ]);
                }

                $league_team = LeagueTeam::where([
                    'team_id' => $team->id,
                    'league_id' => $league->id,
                ])->first();
                if (!$league_team) {
                    $league_team = LeagueTeam::create([
                        'team_id' => $team->id,
                        'league_id' => $league->id,
                        'league_division_id' => $league_division_id,
                    ]);
                } else {
                    LeagueTeam::where([
                        'team_id' => $team->id,
                        'league_id' => $league->id,
                    ])->forceDelete();
                    $league_team = LeagueTeam::create([
                        'team_id' => $team->id,
                        'league_id' => $league->id,
                        'league_division_id' => $league_division_id,
                    ]);
                }
            }
        }
    }

    public function pullLeagues()
    {
        $response = $this->client->request('GET', 'leagues');
        $originals = json_decode($response->getBody());

        foreach ($originals as $original) {
            if (empty($original->category) || empty($original->category->sport_category_id)) {
                continue;
            }
            $category = SportCategory::find($original->category->sport_category_id);
            if (!$category) {
                continue;
            }
            $league = League::withTrashed()
                ->find($original->sport_league_id);
            if ($league) {
                $league->sport_area_id = $original->country ? $original->country->sport_country_id : 0;
                $league->sport_category_id = $original->category ? $original->category->sport_category_id : 0;
                $league->logo = $original->sport_league_logo;
                $league->name = $original->sport_league_name;
                if (!$league->name_es) {
                    $league->name_es = $original->sport_league_name;
                }
                $league->save();
            } else {
                League::create([
                    'id' => $original->sport_league_id,
                    'sport_area_id' => $original->country ? $original->country->sport_country_id : 0,
                    'sport_category_id' => $original->category ? $original->category->sport_category_id : 0,
                    'logo' => $original->sport_league_logo,
                    'name' => $original->sport_league_name,
                    'name_es' => $original->sport_league_name,
                    'slug' => str_slug($original->sport_league_name),
                    'slug_es' => str_slug($original->sport_league_name),
                    'display_order' => League::getNextDisplayOrder(),
                ]);
            }
        }

        // $leagues = League::where('id', 13)->get();
        $leagues = League::all();
        $display_order = 1;
        foreach ($leagues as $league) {
            if (!$league->sport_category) {
                continue;
            }
            try {
                $response = $this->client->request('GET', "standings?league_id={$league->id}");
                $standings = json_decode($response->getBody());
                if (isset($standings->leagues)) {
                    $sub_position = 0;
                    foreach ($standings->leagues as $sub_league) {
                        $league_division_level_0 = $this->createLeagueDivision($sub_league, $league, 0, $sub_position, $display_order);
                        ++$display_order;

                        if (isset($sub_league->divisions)) {
                            $league_division_position = 0;
                            foreach ($sub_league->divisions as $league_division) {
                                $league_division_level_1 = $this->createLeagueDivision($league_division, $league, $league_division_level_0->id, $league_division_position, $display_order);
                                $this->createTeams($league_division, $league, $league_division_level_1->id);

                                ++$display_order;
                                ++$league_division_position;
                            }
                        } else {
                            $this->createTeams($sub_league, $league, $league_division_level_0->id);
                        }
                        ++$sub_position;
                    }
                } else {
                    $this->createTeams($standings, $league, 0);
                }
            } catch (\Exception $e) {
                Log::error($e);
            }
        }
    }

    public function pullGames($params)
    {
        //
    }

    public function games($params)
    {
        $response = $this->client->request('GET', 'games', [
            'query' => $params,
        ]);
        $ret = json_decode($response->getBody());
        return $ret;
    }

    public function importGames($league_id, $data)
    {
        $league = League::find($league_id);
        foreach ($data as $item) {
            $home_team = $this->importGameTeam($league, $item['home_team'], $item['between_players']);
            $away_team = $this->importGameTeam($league, $item['away_team'], $item['between_players']);
            $game = Game::withTrashed()->where([
                // 'ref_id' => $item['ref_id'],
                'league_id' => $league_id,
                'home_team_id' => $home_team->id,
                'away_team_id' => $away_team->id,
                'start_at' => $item['start_at'],
            ])->first();
            if (!$game) {
                $game = Game::where('ref_id', $item['ref_id'])->first();
            }

            $data = [
                'ref_id' => $item['ref_id'],
                'league_id' => $league_id,
                'home_team_id' => $home_team->id,
                'away_team_id' => $away_team->id,
                'start_at' => $item['start_at'],
                'home_team_score' => $item['home_team_score'],
                'away_team_score' => $item['away_team_score'],
                'game_info' => $item['game_info'],
                'status' => $item['status'],
                'setting_manually' => 0,
                'between_players' => $item['between_players']
            ];

            if ($item['status'] === Game::$STATUS_NOT_STARTED || $item['status'] === Game::$STATUS_POSTPONED) {
                $data['game_general_info'] = $item['game_general_info'];
            }

            if ($game) {
                $game->update($data);
                if ($game->trashed()) {
                    $game->restore();
                }
            } else {
                $game = Game::create($data);
            }
        }
    }

    public function importGameTeam($league, $data, $is_player)
    {
        $team_ref_id = $data['local_id'];
        $team_data = [
            'sport_category_id' => $league->sport_category_id,
            'sport_area_id' => $league->sport_area_id,
            'ref_id' => $team_ref_id,
            'logo' => $data['logo'],
            'name' => $data['name'],
            'name_es' => $data['name_es'],
            'short_name_es' => $data['short_name_es'],
            'short_name' => $data['short_name'],
            'slug' => str_slug($data['name']),
            'slug_es' => str_slug($data['name_es']),
            'is_player' => $is_player
        ];
        $team = Team::where(['ref_id' => $team_ref_id, 'is_player' => $is_player])->first();
        if ($team === null) {
            $team = Team::create($team_data);

            $data = ['league_id' => $league->id, 'team_id' => $team->id];
            $league_team = LeagueTeam::where($data)->first();
            if ($league_team === null) {
                $league_team = LeagueTeam::create($data);
            }
        }
        else {
            $team->update($team_data);
        }
        return $team;
    }
}
