<?php

namespace App\Http\Controllers;

use App\Models\GameVersion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GameVersionController extends Controller
{
    public function index()
    {
        $gameVersions = GameVersion::all();

        if (!$gameVersions) {
            return response()->json([
                'status'  => false,
                'message' => 'No game version found',
            ]);
        }

        return response()->json([
            'status'  => true,
            'gameVersions' => $gameVersions,
        ]);
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request->all(), [
            'game_id' => 'required | integer',
            'version' => 'required | string',
            'file' => 'required | file | mimes:zip',
        ]);

        $path = $request->file('file')->store('game_versions');

        $version = GameVersion::create([
            'game_id' => $request->game_id,
            'version' => $request->version,
            'file_path' => $path,
        ]);

        return response()->json([
            'status' => true,
            'gameVersion' => $version,
        ]);
    }

    public function show($id)
    {
        return response()->json(GameVersion::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $version = GameVersion::findOrFail($id);

        $version->update($request->all());
        return response()->json([
            'status' => true,
            'gameVersion' => $version,
        ]);
    }

    public function destroy($id)
    {
        $version = GameVersion::findOrFail($id);
        Storage::delete($version->file_path);
        $version->delete();
        return response()->json([
            'status' => true,
            'message' => 'Game version deleted successfully',
        ]);
    }
}
