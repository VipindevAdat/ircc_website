<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgramScheduleDay;
use App\Models\ProgramScheduleItem;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ProgramScheduleItemController extends Controller
{
    public function index(Request $request, ProgramScheduleDay $program_schedule_day, DataTables $datatables)
    {
        if ($request->ajax()) {
            $items = ProgramScheduleItem::where('program_schedule_day_id', $program_schedule_day->id)
                ->select('program_schedule_items.*');

            return $datatables->eloquent($items)
                ->editColumn('status', function (ProgramScheduleItem $item) {
                    return $item->status
                        ? '<span class="text-success">Enabled</span>'
                        : '<span class="text-danger">Disabled</span>';
                })
                ->addColumn('action', function (ProgramScheduleItem $item) use ($program_schedule_day) {
                    return '<div class="d-flex gap-1">
                        <a href="' . route('admin.program-schedule-items.edit', [$program_schedule_day->id, $item->id]) . '" class="btn btn-sm btn-clean btn-icon" title="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-href="' . route('admin.program-schedule-items.destroy', [$program_schedule_day->id, $item->id]) . '" data-bs-toggle="modal" data-bs-target="#delete-item-modal" class="btn btn-sm btn-clean btn-icon delete-item" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                    </div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.program_schedule_item.list', [
            'day' => $program_schedule_day,
        ]);
    }

    public function create(ProgramScheduleDay $program_schedule_day)
    {
        return view('admin.program_schedule_item.form', [
            'day' => $program_schedule_day,
            'item' => new ProgramScheduleItem([
                'program_schedule_day_id' => $program_schedule_day->id,
                'sort_order' => ($program_schedule_day->items()->max('sort_order') ?? 0) + 1,
                'status' => true,
            ]),
        ]);
    }

    public function store(Request $request, ProgramScheduleDay $program_schedule_day)
    {
        $validated = $this->validateItem($request);
        $validated['program_schedule_day_id'] = $program_schedule_day->id;
        ProgramScheduleItem::create($validated);

        return redirect()
            ->route('admin.program-schedule-items.index', $program_schedule_day->id)
            ->with(['success_message' => 'Schedule item added successfully.']);
    }

    public function edit(ProgramScheduleDay $program_schedule_day, ProgramScheduleItem $program_schedule_item)
    {
        $this->ensureItemBelongsToDay($program_schedule_day, $program_schedule_item);

        return view('admin.program_schedule_item.form', [
            'day' => $program_schedule_day,
            'item' => $program_schedule_item,
        ]);
    }

    public function update(Request $request, ProgramScheduleDay $program_schedule_day, ProgramScheduleItem $program_schedule_item)
    {
        $this->ensureItemBelongsToDay($program_schedule_day, $program_schedule_item);
        $program_schedule_item->update($this->validateItem($request));

        return redirect()
            ->route('admin.program-schedule-items.index', $program_schedule_day->id)
            ->with(['success_message' => 'Schedule item updated successfully.']);
    }

    public function destroy(ProgramScheduleDay $program_schedule_day, ProgramScheduleItem $program_schedule_item)
    {
        $this->ensureItemBelongsToDay($program_schedule_day, $program_schedule_item);
        $program_schedule_item->delete();

        return response()->json(['success' => 'Schedule item deleted successfully'], 200);
    }

    private function validateItem(Request $request): array
    {
        return $request->validate([
            'time_slot' => 'required|string|max:100',
            'title' => 'required|string|max:500',
            'details' => 'nullable|string',
            'venue' => 'nullable|string|max:500',
            'sort_order' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);
    }

    private function ensureItemBelongsToDay(ProgramScheduleDay $day, ProgramScheduleItem $item): void
    {
        if ($item->program_schedule_day_id !== $day->id) {
            abort(404);
        }
    }
}
