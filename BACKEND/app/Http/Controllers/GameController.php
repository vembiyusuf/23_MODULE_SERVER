<?php

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\{Game, GameVersion, Score, User};

class GameController extends Controller
{
    public function index(Request $request)
    {
        $page = max((int) $request->query('page', 0), 0);
        $size = max((int) $request->query('size', 10), 1);
        $sortBy = in_array($request->query('sortBy'), ['title', 'popular', 'uploaddate']) ? $request->query('sortBy') : 'title';
        $sortDir = $request->query('sortDir') === 'desc' ? 'desc' : 'asc';

        $games = Game::with('latestVersion')
            ->whereHas('latestVersion')
            ->withCount('scores as scoreCount')
            ->get()
            ->sortBy(fn($g) => $sortBy === 'popular' ? -$g->scoreCount : ($sortBy === 'uploaddate' ? optional($g->latestVersion)->created_at : $g->title), SORT_REGULAR, $sortDir === 'desc')
            ->values();

        return response()->json([
            'page' => $page,
            'size' => $games->slice($page * $size, $size)->count(),
            'totalElements' => $games->count(),
            'content' => $games->slice($page * $size, $size)->map(fn($g) => [
                'slug' => $g->slug,
                'title' => $g->title,
                'description' => $g->description,
                'thumbnail' => optional($g->latestVersion)->thumbnail,
                'uploadTimestamp' => optional($g->latestVersion)->created_at?->toISOString(),
                'author' => $g->author->username,
                'scoreCount' => $g->scoreCount,
            ])->values()
        ]);
    }

    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'title' => 'required|min:3|max:60',
            'description' => 'nullable|max:200'
        ])->validate();

        $slug = Str::slug($data['title']);
        if (Game::where('slug', $slug)->exists()) {
            return response()->json(['status' => 'invalid', 'slug' => 'Game title already exists'], 400);
        }

        $game = Game::create([
            'title' => $data['title'],
            'slug' => $slug,
            'description' => $data['description'] ?? '',
            'created_by' => Auth::id()
        ]);

        return response()->json(['status' => 'success', 'slug' => $game->slug], 201);
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
            return response()->json(['status' => 'forbidden', 'message' => 'You are not the game author'], 403);
        }

        $data = Validator::make($request->all(), [
            'title' => 'required|min:3|max:60',
            'description' => 'nullable|max:200'
        ])->validate();

        $game->update($data);
        return response()->json(['status' => 'success']);
    }

    public function destroy($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        if ($game->created_by !== Auth::id()) {
            return response()->json(['status' => 'forbidden', 'message' => 'You are not the game author'], 403);
        }

        $game->delete();
        return response()->noContent();
    }

    public function scores($slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $scores = $game->scores()->with('user')->orderByDesc('score')->get()->unique('user_id');

        return response()->json([
            'scores' => $scores->map(fn($s) => [
                'username' => $s->user->username,
                'score' => $s->score,
                'timestamp' => $s->created_at->toISOString(),
            ])->values()
        ]);
    }

    public function addScore(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        $version = $game->latestVersion;

        $data = Validator::make($request->all(), [
            'score' => 'required|integer|min:0'
        ])->validate();

        Score::create([
            'user_id' => Auth::id(),
            'game_version_id' => $version->id,
            'score' => $data['score'],
        ]);

        return response()->json(['status' => 'success'], 201);
    }

    public function upload(Request $request, $slug)
    {
        $game = Game::where('slug', $slug)->firstOrFail();
        if ($game->author_id !== Auth::id()) {
            return response('You are not the game author', 403);
        }

        if (!$request->hasFile('zipfile') || $request->file('zipfile')->getClientOriginalExtension() !== 'zip') {
            return response('Invalid or missing zipfile', 400);
        }

        $version = $game->versions()->max('version') + 1;
        $path = "games/{$game->slug}/{$version}";
        $zipPath = $request->file('zipfile')->storeAs("zips/{$game->slug}", "v{$version}.zip");

        $zip = new ZipArchive;
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

    public function shows($username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $isSelf = Auth::check() && Auth::user()->username === $username;

        $games = $user->authoredGames()->with('versions')->get()->filter(function ($game) use ($isSelf) {
            return $isSelf || $game->versions->isNotEmpty();
        })->map(fn($g) => [
            'slug' => $g->slug,
            'title' => $g->title,
            'description' => $g->description,
        ])->values();

        $scores = $user->scores()
            ->with('gameVersion.game')
            ->orderByDesc('score')
            ->get()
            ->unique(fn($s) => $s->gameVersion->game_id)
            ->map(fn($s) => [
                'game' => [
                    'slug' => $s->gameVersion->game->slug,
                    'title' => $s->gameVersion->game->title,
                    'description' => $s->gameVersion->game->description,
                ],
                'score' => $s->score,
                'timestamp' => $s->created_at->toISOString(),
            ])->values();

        return response()->json([
            'username' => $user->username,
            'registeredTimestamp' => $user->created_at->toISOString(),
            'authoredGames' => $games,
            'highscores' => $scores,
        ]);
    }
}
