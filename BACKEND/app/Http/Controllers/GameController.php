<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Game;
use App\Models\GameVersion;
use App\Models\Score;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int)$request->query('page', 0), 0);
        $size = max((int)$request->query('size', 10), 1);
        $sortBy = in_array($request->query('sortBy'), ['title', 'popular', 'uploaddate']) ? $request->query('sortBy') : 'title';
        $sortDir = $request->query('sortDir') === 'desc' ? 'desc' : 'asc';

        $games = Game::with(['latestVersion'])
            ->whereHas('latestVersion')
            ->withCount('scores as scoreCount')
            ->get()
            ->sortBy(function ($game) use ($sortBy) {
                if ($sortBy === 'popular') {
                    return -$game->scoreCount;
                } elseif ($sortBy === 'uploaddate') {
                    return optional($game->latestVersion)->created_at;
                }
                return $game->title;
            }, SORT_REGULAR, $sortDir === 'desc')
            ->values();

        $total = $games->count();
        $sliced = $games->slice($page * $size, $size)->values();

        return response()->json([
            'page' => $page,
            'size' => $sliced->count(),
            'totalElements' => $total,
            'content' => $sliced->map(function ($game) {
                return [
                    'slug' => $game->slug,
                    'title' => $game->title,
                    'description' => $game->description,
                    'thumbnail' => optional($game->latestVersion)->thumbnail ?? null,
                    'uploadTimestamp' => optional($game->latestVersion)->created_at?->toISOString(),
                    'author' => $game->author->username,
                    'scoreCount' => $game->scoreCount,
                ];
            })
        ]);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required|min:3|max:60',
            'description' => 'nullable|max:200'
        ])->validate();

        $slug = Str::slug($validated['title']);

        if (Game::where('slug', $slug)->exists()) {
            return response()->json([
                'status' => 'invalid',
                'slug' => 'Game title already exists'
            ], 400);
        }

        $game = Game::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'] ?? '',
            'created_by' => Auth::id()
        ]);

        return response()->json([
            'status' => 'success',
            'slug' => $game->slug
        ], 201);
    }

    public function show($slug)
    {
        $game = Game::with('latestVersion')->where('slug', $slug)->firstOrFail();
        return response()->json([
            'slug' => $game->slug,
            'title' => $game->title,
            'description' => $game->description,
            'thumbnail' => optional($game->latestVersion)->thumbnail,
            'uploadTimestamp' => optional($game->latestVersion)->created_at?->toISOString(),
            'author' => $game->author->username,
            'scoreCount' => $game->scores()->count(),
            'gamePath' => "/games/{$game->slug}/" . optional($game->latestVersion)->version . '/'
        ]);
    }

    public function update(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        if ($game->author_id !== Auth::id()) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author'
            ], 403);
        }

        $validated = Validator::make($request->all(), [
            'title' => 'required|min:3|max:60',
            'description' => 'nullable|max:200'
        ])->validate();

        $game->update($validated);

        return response()->json(['status' => 'success']);
    }

    public function destroy($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        if ($game->created_by !== Auth::id()) {
            return response()->json([
                'status' => 'forbidden',
                'message' => 'You are not the game author'
            ], 403);
        }

        $game->delete();

        return response()->noContent();
    }

    public function scores($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        $allScores = $game->scores()->with('user')->orderByDesc('score')->get();

        $filteredScores = $allScores->unique('user_id');

        return response()->json([
            'scores' => $filteredScores->map(function ($s) {
                return [
                    'username' => $s->user->username,
                    'score' => $s->score,
                    'timestamp' => $s->created_at->toISOString(),
                ];
            })->values()
        ]);
    }


    public function addScore(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $version = $game->latestVersion;

        $validated = Validator::make($request->all(), [
            'score' => 'required|integer|min:0'
        ])->validate();

        Score::create([
            'user_id' => Auth::id(),
            'game_version_id' => $version->id,
            'score' => $validated['score'],
        ]);

        return response()->json(['status' => 'success'], 201);
    }

    public function upload(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();

        if ($game->author_id !== Auth::id()) {
            return response('You are not the game author', 403);
        }

        if (!$request->hasFile('zipfile')) {
            return response('Missing zipfile', 400);
        }

        $file = $request->file('zipfile');

        if ($file->getClientOriginalExtension() !== 'zip') {
            return response('Only .zip files are allowed', 400);
        }

        $version = $game->versions()->max('version') + 1;
        $path = "games/{$game->slug}/{$version}";
        $zipPath = $file->storeAs("zips/{$game->slug}", "v{$version}.zip");

        $zip = new \ZipArchive;
        $fullPath = public_path($path);
        if ($zip->open(storage_path("app/{$zipPath}")) === true) {
            $zip->extractTo($fullPath);
            $zip->close();
        } else {
            return response('Failed to extract zip', 500);
        }

        $thumbnail = file_exists("{$fullPath}/thumbnail.png") ? "/games/{$game->slug}/{$version}/thumbnail.png" : null;

        GameVersion::create([
            'game_id' => $game->id,
            'version' => $version,
            'path' => "/games/{$game->slug}/{$version}/",
            'thumbnail' => $thumbnail
        ]);

        return response('Upload success', 201);
    }
}
