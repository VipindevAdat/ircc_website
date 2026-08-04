<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramScheduleDay;
use App\Models\ProgramScheduleSetting;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProgramScheduleController extends Controller
{
    public function index(Request $request, DataTables $datatables)
    {
        if ($request->ajax()) {
            $days = ProgramScheduleDay::select('program_schedule_days.*');

            return $datatables->eloquent($days)
                ->addColumn('items_count', function (ProgramScheduleDay $day) {
                    return $day->items()->count();
                })
                ->editColumn('is_visible', function (ProgramScheduleDay $day) {
                    return $day->is_visible
                        ? '<span class="text-success">Visible</span>'
                        : '<span class="text-muted">Hidden</span>';
                })
                ->editColumn('is_virtual', function (ProgramScheduleDay $day) {
                    return $day->is_virtual
                        ? '<span class="text-info">Virtual</span>'
                        : '<span class="text-muted">In-person</span>';
                })
                ->editColumn('status', function (ProgramScheduleDay $day) {
                    return $day->status
                        ? '<span class="text-success">Enabled</span>'
                        : '<span class="text-danger">Disabled</span>';
                })
                ->addColumn('action', function (ProgramScheduleDay $day) {
                    return '<div class="d-flex gap-1">
                        <a href="' . route('admin.program-schedule-items.index', $day->id) . '" class="btn btn-sm btn-clean btn-icon" title="Manage items"><i class="fa fa-list" aria-hidden="true"></i></a>
                        <a href="' . route('admin.program-schedule-days.edit', $day->id) . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-href="' . route('admin.program-schedule-days.destroy', $day->id) . '" data-bs-toggle="modal" data-bs-target="#delete-day-modal" class="btn btn-sm btn-clean btn-icon delete-day" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    </div>';
                })
                ->rawColumns(['is_visible', 'is_virtual', 'status', 'action'])
                ->make(true);
        }

        return view('admin.program_schedule.index', [
            'pageSettings' => ProgramScheduleSetting::current(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'conference_title' => 'required|string|max:500',
            'venue' => 'nullable|string|max:500',
            'event_dates' => 'nullable|string|max:255',
            'disclaimer' => 'nullable|string',
        ]);

        $settings = ProgramScheduleSetting::first();
        if ($settings) {
            $settings->update($validated);
        } else {
            ProgramScheduleSetting::create($validated);
        }

        return redirect()
            ->route('admin.program-schedule.index')
            ->with(['success_message' => 'Program schedule page settings saved successfully.']);
    }
}
