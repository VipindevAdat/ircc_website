<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ContactUs;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {

            $contactus = ContactUs::where(function($query) use($request){
              
            })->select('contact_us.*');
            
            return $datatables->eloquent($contactus)
            ->editColumn('created_at', function (ContactUs $ContactUs) {
                return Carbon::parse($ContactUs->created_at)->format('m-d-Y') . '<br>' . Carbon::parse($ContactUs->created_at)->format('H:i:s');
             })
             ->editColumn('message', function (ContactUs $ContactUs) {
                return wordwrap($ContactUs->message,50,'<br>');
            })
            ->editColumn('subject', function (ContactUs $ContactUs) {
                return wordwrap($ContactUs->subject,20,'<br>');
            })
             ->rawColumns(['created_at','subject','message'])
                ->make(true);
          }
   
              return view('admin.contact_us');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
