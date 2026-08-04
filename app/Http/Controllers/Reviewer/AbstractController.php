<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\AbstractReviewer;
use App\Models\AbstractSubmission;
use App\Models\ReviewStatus;
use App\Models\Reviewer;
use App\Models\Setting;
use App\Models\Registration;
use App\Models\AbstractDomain;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Auth;

class AbstractController extends Controller
{
    public function index(Request $request, Datatables $datatables)
    {
        $user = Auth::guard('reviewer')->user(); // Get the currently logged-in reviewer

        if ($request->ajax()) {
            $abstract_reviewers = AbstractReviewer::with('abstract', 'reviewer') // Eager load abstract and reviewer relationships
                ->where('abstract_reviewer.reviewer_id', $user->id) // Filter by the logged-in reviewer's ID
                ->orderBy('abstract_reviewer.id', 'DESC');

            return $datatables->eloquent($abstract_reviewers)
                ->addColumn('title', function ($row) {
                    return mb_substr(wordwrap($row->abstract->abstract_title ?? 'No Title Found',25,'<br>'),0,100,'UTF-8').'...';
                })
                ->addColumn('view_abstract', function ($row) {
                    return '<a href="' . route('admin.abstract.show', $row->abstract_id) . '" class="btn btn-sm btn-primary">View Abstract</a>';
                })
                ->addColumn('clarity', function ($row) {
                    return $row->clarity ?? '0.00';
                })
                ->addColumn('relevance', function ($row) {
                    return $row->relevance ?? '0.00';
                })
                ->addColumn('originality', function ($row) {
                    return $row->originality ?? '0.00';
                })
                ->addColumn('score', function ($row) {
                    return $row->score ?? 'Not Rated';
                })
                
                ->addColumn('status', function ($row) {
                    // Get the status label from the review_statuses table
                    $status = ReviewStatus::find($row->review_status);
                    return $status ? ($status->status=='Approved'?'<span class="badge bg-success">'.$status->status.'</span>':($status->status=='Rejected'?'<span class="badge bg-danger">'.$status->status.'</span>':'<span class="badge bg-warning">'.$status->status.'</span>' )): 'Unknown';
                })
                ->addColumn('comments', function ($row) {
                    // return nl2br($row->comments ?? 'No comments');
                    return mb_substr(wordwrap($row->comments ?? 'No Title Found',25,'<br>'),0,100,'UTF-8'). '...';
                 
                })
                ->rawColumns(['view_abstract', 'comments','title','status']) // Make sure to render HTML for buttons and comments
                ->make(true);
        }

        return view('reviewer.abstract.list');
    }


    public function addScore(Request $request)
    {
        $abstractId = $request->input('id');
        $abstractReviewer = AbstractReviewer::where('abstract_id', $abstractId)->firstOrFail();
        $reviewStatuses = ReviewStatus::all();
        $minScore = Setting::where('code', 'min_score')->value('value');
        return view('reviewer.abstract.form', compact('abstractReviewer', 'reviewStatuses', 'minScore'));
    }
    
    

    public function storeScore(Request $request, $id)
    {
        // Validate the input data
        $validated = $request->validate([
            'clarity' => 'required|numeric|min:0|max:5',
            'relevance' => 'required|numeric|min:0|max:5',
            'originality' => 'required|numeric|min:0|max:5',
            'comments' => 'nullable|string',
            'review_status' => 'required|exists:review_status,id',
        ]);

        $abstractReviewer = AbstractReviewer::findOrFail($id);

        $averageScore = ($validated['clarity'] + $validated['relevance'] + $validated['originality']) / 3;

        $abstractReviewer->update([
            'clarity' => $validated['clarity'],
            'relevance' => $validated['relevance'],
            'originality' => $validated['originality'],
            'score' => $averageScore,
            'comments' => $validated['comments'],
            'review_status' => $validated['review_status'],
        ]);

        $abstract = AbstractSubmission::findOrFail($abstractReviewer->abstract_id);  
        $abstract->update([
            'score' => $averageScore,
            'comments' => $validated['comments'],
        ]);

        return redirect()->route('reviewer.abstracts')->with('success_message', 'Score added successfully!');
    }


    public function abstractView(Request $request) {
        $abstract = AbstractSubmission::find($request->id);

        if (!$abstract) {
            return response()->json(['error' => 'Abstract not found'], 404);
        }

        $student_authors = $this->decodeJsonField($abstract->student_authors);
        $additional_authors = $this->splitCommaSeparatedValues($abstract->additional_authors);
        $registration = Registration::find($abstract->registration_id);
        $category = $abstract->category->name;
        $abstractDomain = AbstractDomain::find($abstract->abstract_domain);

        $reviews = $abstract->abstractReviewer()->with('reviewer')->first();   

        $data = [
            'title' => $abstract->abstract_title ?? 'Untitled',
            'content' => $abstract->abstract_content ?? 'No content available.',
            'student_authors' => $student_authors,
            'additional_authors' => $additional_authors,
            'category' => $category ?? 'NA',
            'abstractDomain' => $abstractDomain,
            'institute_name' => $registration->institution_name ?? 'Unknown Institution',
            'correspondent_author' => $abstract->correspondent_author ?? 'No corresponding author specified.',
            'reviews' => $reviews,
        ];

        return view('admin.components.viewAbstract', $data);
    }

    private function decodeJsonField($field) {
        if (is_string($field)) {
            return json_decode($field, true) ?? [];
        }

        return is_array($field) ? $field : [];
    }

    private function splitCommaSeparatedValues($field) {
        if (is_string($field) && !empty($field)) {
            return array_map('trim', explode(',', $field));
        }

        return [];
    }

}
