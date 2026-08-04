<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramScheduleDay;
use Illuminate\Http\Request;

class ProgramScheduleDayController extends Controller
{
    public function create()
    {
        return view('admin.program_schedule_day.form', [
            'day' => new ProgramScheduleDay(['sort_order' => 1, 'is_visible' => true, 'status' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateDay($request);
        ProgramScheduleDay::create($validated);

        return redirect()
            ->route('admin.program-schedule.index')
            ->with(['success_message' => 'Schedule day added successfully.']);
    }

    public function edit(ProgramScheduleDay $program_schedule_day)
    {
        return view('admin.program_schedule_day.form', [
            'day' => $program_schedule_day,
        ]);
    }

    public function update(Request $request, ProgramScheduleDay $program_schedule_day)
    {
        $program_schedule_day->update($this->validateDay($request));

        return redirect()
            ->route('admin.program-schedule.index')
            ->with(['success_message' => 'Schedule day updated successfully.']);
    }

    public function destroy(ProgramScheduleDay $program_schedule_day)
    {
        $program_schedule_day->delete();

        return response()->json(['success' => 'Schedule day deleted successfully'], 200);
    }

    private function validateDay(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_visible' => 'nullable|boolean',
            'is_virtual' => 'nullable|boolean',
            'status' => 'required|boolean',
        ]);

        $validated['is_visible'] = $request->boolean('is_visible');
        $validated['is_virtual'] = $request->boolean('is_virtual');

        return $validated;
    }
}
