<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Holiday;

class HolidayController extends Controller
{
    public function index()
    {
        $holidays = Holiday::orderBy('date', 'desc')->get();
        return view('admin.holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'date' => 'required|date|unique:holidays,date',
            'type' => 'required|in:Regular,Special',
        ]);

        Holiday::create($request->only('name', 'date', 'type'));
        return back()->with('success', 'Holiday added.');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'date' => 'required|date|unique:holidays,date,' . $holiday->id,
            'type' => 'required|in:Regular,Special',
        ]);

        $holiday->update($request->only('name', 'date', 'type'));
        return back()->with('success', 'Holiday updated.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();
        return back()->with('success', 'Holiday deleted.');
    }
}
