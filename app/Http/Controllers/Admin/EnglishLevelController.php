<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Academic\EnglishLevel;
use Illuminate\Http\Request;

class EnglishLevelController extends Controller
{
    public function index()
    {
        $levels = EnglishLevel::withCount('teachers')
            ->ordered()
            ->get();

        return view('admin.english-levels.index', compact('levels'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_name' => 'required|string|max:255|unique:english_level,level_name',
            'level_rank' => 'required|integer|min:1|unique:english_level,level_rank',
        ]);

        EnglishLevel::create([
            'level_name' => $request->level_name,
            'level_rank' => $request->level_rank,
        ]);

        return back()->with('success', 'Level created successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'level_name' => 'required|string|max:255|unique:english_level,level_name,'.$id.',english_level_id',
            'level_rank' => 'required|integer|min:1|unique:english_level,level_rank,'.$id.',english_level_id',
        ]);

        EnglishLevel::findOrFail($id)->update([
            'level_name' => $request->level_name,
            'level_rank' => $request->level_rank,
        ]);

        return back()->with('success', 'Level updated successfully.');
    }

    public function destroy($id)
    {
        $level = EnglishLevel::withCount('teachers')->findOrFail($id);

        if ($level->teachers_count > 0) {
            return back()->with('error', 'Cannot delete — this level is assigned to ' . $level->teachers_count . ' teacher(s).');
        }

        $level->delete();
        return back()->with('success', 'Level deleted successfully.');
    }
}