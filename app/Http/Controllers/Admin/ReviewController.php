<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class ReviewController extends Controller
{
    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {

            $review = Review::select('reviews.*');
            if ($request->filled('year')) {
                $review->where('year', $request->year);
            }
            return $datatables->eloquent($review)
            ->editColumn('created_at', function (Review $review) {
                return Carbon::parse($review->created_at)->format('m-d-Y') . '<br>' . Carbon::parse($review->created_at)->format('H:i:s');
             })
             ->editColumn('is_home', function (Review $review) {
                return  $review->is_home==1?'<span class="text-success">Home</span>':'<span class="text-danger">--</span>';
                
            })
             ->editColumn('status', function (Review $review) {
                return  $review->status==1?'<span class="text-success">Enable</span>':'<span class="text-danger">Disable</span>';
                
            })
             ->addColumn('action', function (Review $review) use($request) {
               
                return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.review.edit', $review->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="'. $review->id.'" data-href="' . route("admin.review.destroy", $review->id) . '"   data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-review" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        
                        </div>
                        </div>';
            })
        
            ->rawColumns(['status','is_home','action','created_at'])
            ->make(true);
            
          }
   
              return view('admin.reviews.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(\Illuminate\Http\Request $request)
    {
        return view('admin.reviews.form')->with([
            'review' => new Review(),
           
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'author' => 'required',
            'message' => 'required',
            ]);
        $review = Review::create($request->all());
        return redirect()->route('admin.review.index')->with(['success_message'=>'Review added succesfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        return view('admin.reviews.form')->with([
            'review' => $review,
           
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $review->update($request->all());
        return redirect()->route('admin.review.index')->with(['success_message'=>'Review updated succesfully']);
    }
   
    public function destroy(string $id)
    {
        $review= Review::where("id", $id)->first();
        Review::where("id", $id)->delete();
        return response()
            ->json(["success" => "Review deleteted successfully"], 200);
    }
}
