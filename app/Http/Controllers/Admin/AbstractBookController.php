<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbstractBook;
use Illuminate\Http\Request;
use Plank\Mediable\Media;
use MediaUploader;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;

class AbstractBookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {

            $abstractBook  = AbstractBook::select('abstract_books.*');
            
            return $datatables->eloquent($abstractBook )
            ->editColumn('created_at', function (AbstractBook $abstractBook ) {
                return Carbon::parse($abstractBook ->created_at)->format('m-d-Y') . '<br>' . Carbon::parse($abstractBook ->created_at)->format('H:i:s');
             })
             ->editColumn('status', function (AbstractBook $abstractBook) {
                return  $abstractBook->status==1?'<span class="text-success">Enable</span>':'<span class="text-danger">Disable</span>';
                //return ($team->status == 0 ? '<span class="label label-lg font-weight-bold label-light-danger label-inline">Disabled</span>' : '<span class="label label-lg font-weight-bold label-light-success label-inline">Enabled</span>');

            })
             ->addColumn('action', function (AbstractBook $abstractBook ) use($request) {
               
                return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.abstract-book.edit', $abstractBook ->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="'. $abstractBook ->id.'" data-bs-toggle="modal" data-href="' . route("admin.abstract-book.destroy", $abstractBook->id) . '" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-book" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        
                        </div>
                        </div>';
            })
        
            ->rawColumns(['status', 'action','created_at'])
            ->make(true);
            
          }
   
              return view('admin.abstract_book.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.abstract_book.form')->with([
            'abstractBook' => new AbstractBook(),
           
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'description' => 'required'
            ]);
        $date_time = explode(" - ", $request->date_time);
        $date = [Carbon::createFromFormat("d/m/Y", $date_time[0])->format("Y-m-d"), Carbon::createFromFormat("d/m/Y", $date_time[1])->format("Y-m-d")];
        $request->merge(["date_from" => $date[0], "date_to" => $date[1]]);
        $abstractBook = AbstractBook::create($request->all());
        if (!empty($request->file('file-upload'))) {
            foreach ($request->file('file-upload') as $file)
            {
                $time = time();
                $filename = 'IRCC_' . $time;
                $media = MediaUploader::fromSource($file)
                    ->useFilename($filename)
                    ->toDirectory('abstractBook/' . $abstractBook->id)
                    ->toDisk('uploads')
                    ->upload();
        
                $abstractBook->attachMedia($media, ['abstractBook']);
            }
 
        }
        return redirect()->route('admin.abstract-book.index')->with(['success_message'=>'Abstract Book added succesfully']);
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
        $abstractBook = AbstractBook::whereId($id)->first();
        $abstractBook->date_time = Carbon::parse($abstractBook->date_from)->format("d/m/Y") . " - " . Carbon::parse($abstractBook->date_to)->format("d/m/Y");
        return view("admin..abstract_book.form")->with(["abstractBook" => $abstractBook]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,AbstractBook $abstractBook)
    {
        $abstractBook->update($request->all());
        if (!empty($request->file('file-upload'))) {
            $images = $abstractBook->getMedia('abstractBook');
            if (!empty($images)) {
                foreach ($images as $attachment) {
                    $attachment->delete();
                }
            }
            foreach ($request->file('file-upload') as $file) {
                $time = time();
                $filename = 'IRCC_' . $time;
                $media = MediaUploader::fromSource($file)->useFilename($filename)->toDirectory('abstractBook/' . $abstractBook->id)->upload();
                $abstractBook->attachMedia($media, ['abstractBook']);
            }
        }
        return redirect()->route('admin.abstract-book.index')->with(['success_message' => 'Abstract Book updated succesfully']);
    }
    public function deleteAbstractById(Request $request){
        //dd($request->all());
        $abstractBookId=$request->abstractBookId;
        $abstractBook=AbstractBook::where('id',$abstractBookId)->first();
        $status=$abstractBook->getMedia('abstractBook')->where('id',$request->id)->first()->delete();
        return $status;

    } 
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        AbstractBook::where("id", $id)->delete();
        return response()->json(["success" => "Abstract book deleteted successfully"], 200);
    }
}
