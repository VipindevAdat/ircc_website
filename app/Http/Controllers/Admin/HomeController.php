<?php

namespace App\Http\Controllers\Admin;


use App\Models\AbstractSubmission;
use App\Models\CompetitionAbstract;
use App\Models\Registration;
use App\Models\Participant;
use App\Models\Setting;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserAbstract;
use App\Mail\AdminAbstract;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $adultFee = (float) (optional(Setting::where('code', 'adult_fee')->first())->value ?? 50);

        // Count registrations with completed payment
        $paymentCompletedCount = Registration::whereRaw("
            COALESCE((SELECT SUM(COALESCE(p.paid_amount, p.amount, 0)) 
                      FROM payments p 
                      WHERE p.registration_id = registrations.id 
                      AND p.payment_status = 3), 0) > 0
            AND COALESCE((SELECT SUM(COALESCE(p.paid_amount, p.amount, 0)) 
                          FROM payments p 
                          WHERE p.registration_id = registrations.id 
                          AND p.payment_status = 3), 0) >= 
                COALESCE((SELECT SUM(CASE WHEN par.role = 'adult' THEN ? ELSE 150 END) 
                          FROM participants par 
                          WHERE par.registration_id = registrations.id 
                          AND par.type <> 2), 0)
        ", [$adultFee])->count();

        // Count abstracts (status=1) where payment is completed
        $abstractCompletedCount = AbstractSubmission::where('status', 1)
            ->whereHas('registration', function ($q) use ($adultFee) {
                $q->whereRaw("
                    COALESCE((SELECT SUM(COALESCE(p.paid_amount, p.amount, 0)) 
                              FROM payments p 
                              WHERE p.registration_id = registrations.id 
                              AND p.payment_status = 3), 0) > 0
                    AND COALESCE((SELECT SUM(COALESCE(p.paid_amount, p.amount, 0)) 
                                  FROM payments p 
                                  WHERE p.registration_id = registrations.id 
                                  AND p.payment_status = 3), 0) >= 
                        COALESCE((SELECT SUM(CASE WHEN par.role = 'adult' THEN ? ELSE 150 END) 
                                  FROM participants par 
                                  WHERE par.registration_id = registrations.id 
                                  AND par.type <> 2), 0)
                ", [$adultFee]);
            })->count();

        return view('admin.dashboard')->with([
            'registerCount' => Registration::count(),
            'participantsCount' => Participant::where('type', '<>', 2)->count(),
            'abstractCount' => AbstractSubmission::where('status', 1)->count(),
            'paymentCompletedCount' => $paymentCompletedCount,
            'abstractCompletedCount' => $abstractCompletedCount,
        ]);
    }

    public function registrationReport(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {
            $registrations = Registration::query()
                ->with(['abstract'])
                ->orderBy('id', 'DESC')
                ->select('registrations.*');

            return $datatables->eloquent($registrations)
                ->addColumn('abstract_status', function (Registration $registration) {
                    if ($registration->abstract) {
                        return '<a><i class="bi bi-check-circle-fill" style="color:#05b171"></i></a><br>' .
                            $registration->abstract->abstract_number;
                    }
                    return '-';
                })
                ->editColumn('created_at', function ($query) {
                    return date('Y-m-d', strtotime($query->created_at)) . '<br>' . date('H:i:s', strtotime($query->created_at));
                })
                ->editColumn('email', function (Registration $registration) {
                    return wordwrap($registration->email, 10, '<br>');
                })
                ->addColumn('action', function (Registration $registration) {
                    return '<a data-id="' . $registration->id . '" data-toggle="modal" data-target="#detailModal" id="registerDetail" style="cursor: pointer;">
                        <i class="bi bi-eye-fill"></i></a>';
                })
                ->rawColumns(['action', 'abstract_status', 'email', 'created_at'])
                ->make(true);
        }

        return view('admin.registration.registration_report');
    }
public function irccPdfAll(){

    $abstracts=AbstractSubmission::whereNull('abstract_file')->get();
    foreach($abstracts as $abstract){

    $category=Category::where('name',$abstract->category)->first();
    $categoryPrefix=$category?$category->prefix:substr($abstract->category, 0, 1);

    $abstract_index=$categoryPrefix.'-'.sprintf('%03d', $abstract->id);
    $lastid=$abstract->id;
    $pdf = Pdf::loadView('mail.abstract-pdf', ['result' => $abstract,'id'=>$abstract_index]);
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();
    $output = $pdf->output();
    Storage::disk('uploads')->put('abstract/'.$lastid.$abstract_index.'_proposal.pdf', $output);
    $abstract->update(['abstract_file'=>$lastid.$abstract_index.'_proposal.pdf']);
//    Mail::to($abstract->correspondent_email, $abstract->correspondent_name)->send(new UserAbstract($abstract));
//    Mail::to('ignite@ircc.world', 'ignite@ircc.world')->cc(['contactustoircc@gmail.com'])->send(new AdminAbstract($abstract));
    $file['reg'][]=$abstract->registration_id;
    $file['file'][]=$lastid.$abstract_index.'_proposal.pdf';

    }
    $data['error']=0;
    $data['file']=$file;

    // $data['author']=implode(',',$request->author_name);
    $data['message']="Abstract sent successfully";
    return response()->json($data);
}
public function irccPdf($id){

    $abstract=AbstractSubmission::where('registration_id',$id)->first();

    $category=Category::where('name',$abstract->category)->first();
    $categoryPrefix=$category?$category->prefix:substr($abstract->category, 0, 1);

    $abstract_index=$categoryPrefix.'-'.sprintf('%03d', $abstract->id);
    $lastid=$abstract->id;
    $pdf = Pdf::loadView('mail.abstract-pdf', ['result' => $abstract,'id'=>$abstract_index]);
    $pdf->setPaper('A4', 'landscape');
    $pdf->render();
    $output = $pdf->output();
    Storage::disk('uploads')->put('abstract/'.$lastid.$abstract_index.'_proposal.pdf', $output);
    $abstract->update(['abstract_file'=>$lastid.$abstract_index.'_proposal.pdf']);
//    Mail::to($abstract->correspondent_email, $abstract->correspondent_name)->send(new UserAbstract($abstract));
//    Mail::to('ignite@ircc.world', 'ignite@ircc.world')->cc(['contactustoircc@gmail.com'])->send(new AdminAbstract($abstract));
    $file['reg'][]=$abstract->registration_id;
    $file['file'][]=$lastid.$abstract_index.'_proposal.pdf';

    $data['error']=0;
    $data['file']=$file;

    // $data['author']=implode(',',$request->author_name);
    $data['message']="Abstract sent successfully";
    return response()->json($data);
}
}
