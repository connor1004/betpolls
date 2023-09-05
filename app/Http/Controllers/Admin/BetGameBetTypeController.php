<?php

namespace App\Http\Controllers\Admin;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

use App\Http\Controllers\Admin\AdminController;
use App\Game;
use App\GameBetType;
use DB;

class BetGameBetTypeController extends AdminController
{
    public function store(Request $request, $game_id)
    {
        $game = Game::with('league.bet_types')->find($game_id);
        $game->createBetTypes();
        return GameBetType::with(['bet_type'])->where('game_id', $game_id)->get();
    }

    public function update(Request $request, $game_id, $id)
    {
        $game_bet_type = GameBetType::find($id);
        $this->validate($request, [
            'weight_1' => 'required|numeric',
        ]);
        $data = $request->only(['weight_1', 'weight_2', 'weight_3', 'weight_4', 'weight_5']);
        $game_bet_type->fill($data);
        $game_bet_type->save();
        return $game_bet_type;
    }
}
