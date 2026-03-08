<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Position;

class PositionController extends Controller
{
    public function index()
    {
        $positions = Position::orderBy('name')->get();
        return view('admin.positions.index', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:positions,name',
            'description' => 'nullable|string|max:255',
        ]);

        Position::create($request->only('name', 'description'));
        return back()->with('success', 'Position added.');
    }

    public function update(Request $request, Position $position)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:positions,name,' . $position->id,
            'description' => 'nullable|string|max:255',
        ]);

        $position->update($request->only('name', 'description'));
        return back()->with('success', 'Position updated.');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return back()->with('success', 'Position deleted.');
    }
}
