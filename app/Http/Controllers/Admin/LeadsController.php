<?php

namespace App\Http\Controllers\Admin;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IrccLeads;
use App\Models\MailHistory;
use App\Models\MailTemplate;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionAbstract;
use App\Mail\Invitation;
use Illuminate\Support\Facades\Mail;
class LeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {

            $irccLeads = IrccLeads::select('ircc_leads.*');
            
            return $datatables->eloquent($irccLeads)
            ->editColumn('created_at', function (IrccLeads $irccLeads) {
                return Carbon::parse($irccLeads->created_at)->format('m-d-Y') . '<br>' . Carbon::parse($irccLeads->created_at)->format('H:i:s');
             })
             ->addColumn('action', function (IrccLeads $irccLeads) use($request) {
               
                return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.leads.edit', $irccLeads->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="'. $irccLeads->id.'" data-href="' . route("admin.leads.destroy", $irccLeads->id) . '"  data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-leads" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        
                        </div>
                        </div>';
            })
        
            ->rawColumns([ 'action','created_at'])
            ->make(true);
            
          }
   
              return view('admin.leads.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
      
        return view('admin.leads.form')->with([
            'leads' => new IrccLeads]); 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            ]);
            $request->merge(['tag'=>'IRCC-23-24','added_date'=>date('Y-m-d')]);   
        $IrccLeads = IrccLeads::create($request->all());
        return redirect()->route('admin.leads.index')->with(['success_message'=>'Leads submitted succesfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
      
        return view('admin.leads.form')->with([
            'leads' => IrccLeads::whereId($id)->first(),
           
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
      
        $irccLeads=IrccLeads::whereId($id)->first();
        $irccLeads->update($request->all());
        return redirect()->route('admin.leads.index')->with(['success_message'=>'Leads updated succesfully']);;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $review= IrccLeads::where("id", $id)->first();
        IrccLeads::where("id", $id)->delete();
        return response()
            ->json(["success" => "Leads deleteted successfully"], 200);
    }
  
    public function sendInvi($start,$limit,$template,$table){
      if($table=='registration')
      $irccLeads=CompetitionRegistration::skip($start)->take($limit)->get();
      if($table=='abstract')
      $irccLeads=CompetitionAbstract::skip($start)->take($limit)->get();
      if($table=='leads')
      $irccLeads=IrccLeads::skip($start)->take($limit)->get();
      if(!empty($irccLeads)){
        $mailTemplate=MailTemplate::where('id',$template)->first();
        $mailHistory=New MailHistory();
        $mailHistory->tag='leads';
        $mailHistory->template=$template;
        $mailHistory->mail_to='';
        $mailHistory->subject=$mailTemplate->subject;
        $mailHistory->mail_body=$mailTemplate->mail_body;
        $mailHistory->save();
        $send=[];
        $email_array=[];
            foreach($irccLeads as $lead){
              if (!in_array($lead->email, $email_array))
                    {
                      array_push($email_array,$lead->email);
                Mail::to($lead->email, '')->send(new Invitation($mailHistory));
                $send['email'][]=$lead->email;
                    }
           }
       return response()->json($send);
      }
     else{
        return response()->json(['message'=>'no data','data'=>$irccLeads]);
     }
      
    }
   
}
