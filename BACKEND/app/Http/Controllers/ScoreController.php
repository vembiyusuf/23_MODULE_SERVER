<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Score::query();
        if ($request->has('user_id')) $query->where('user_id', $request->user_id);
        if ($request->has('game_id')) $query->where('game_id', $request->game_id);
        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'game_id' => 'required | exists:games,id',
            'score' => 'required | numeric',
        ]);

        $score = Score::create([
            'user_id' => $request->user()->id,
            'game_id' => $request->game_id,
            'score' => $request->score,
        ]);

        return response()->json($score);
    }

    public function show($id)
    {
        return response()->json(Score::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $score = Score::findOrFail($id);
        $score->update($request->all());
        return response()->json($score);
    }

    public function destroy($id)
    {
        Score::destroy($id);
        return response()->json([
            'status' => true,
            'message' => 'Score deleted successfully'
        ]);
    }
}
