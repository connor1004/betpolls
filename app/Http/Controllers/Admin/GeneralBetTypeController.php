<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\BetType;

class GeneralBetTypeController extends AdminController
{
    public function index()
    {
        $bet_types = BetType::all();
        return $bet_types;
    }

    public function update(Request $request)
    {
        $bet_types_data = $request->input('bet_types', []);
        foreach ($bet_types_data as $data) {
            $bet_type = BetType::find($data['id']);
            if ($bet_type) {
                $bet_type->update([
                    'win_score' => $data['win_score'],
                    'loss_score' => $data['loss_score'],
                    'tie_win_score' => $data['tie_win_score'],
                    'tie_loss_score' => $data['tie_loss_score'],
                ]);
            }
        }
        $bet_types = BetType::all();
        return $bet_types;
    }
}
