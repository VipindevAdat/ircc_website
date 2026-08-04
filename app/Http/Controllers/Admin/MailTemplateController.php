<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MailTemplate;
use Yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\Invitation;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionAbstract;
use App\Models\IrccLeads;
use App\Models\MailHistory;
use Monolog\Registry;
use App\Jobs\sendMailJob;
use Illuminate\Mail\Attachment;
use Plank\Mediable\Media;
use MediaUploader;
class MailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Datatables $datatables)
    {
        if ($request->ajax()) {

            $mailTemplate = MailTemplate::select('mail_templates.*');
            
            return $datatables->eloquent($mailTemplate)
            ->editColumn('created_at', function (MailTemplate $mailTemplate) {
                return Carbon::parse($mailTemplate->created_at)->format('m-d-Y') . '<br>' . Carbon::parse($mailTemplate->created_at)->format('H:i:s');
             })
             ->addColumn('action', function (MailTemplate $mailTemplate) use($request) {
               
                return '<div class="d-flex">
                        <div class="dropdown ms-auto">
                        <a href="' . route('admin.mailTemplate.edit', $mailTemplate->id) . '" class="btn btn-sm btn-clean btn-icon text-end" title="Edit" data-toggle="Edit"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                        <a data-id="'. $mailTemplate->id.'" data-bs-toggle="modal" data-bs-target="#delete-modal" class="btn btn-sm btn-clean btn-icon text-end delete-template" data-toggle="Delete" title="Delete"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
                        
                        </div>
                        </div>';
            })
        
            ->rawColumns([ 'action','created_at'])
            ->make(true);
            
          }
   
              return view('admin.mail_template.list');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mail_template.form')->with([
            'mailTemplate' => new MailTemplate(),
           
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'subject' => 'required',
            'mail_body' => 'required',
            ]);
        $mailTemplate = MailTemplate::create($request->all());
        if (!empty($request->file('file-upload'))) {
            foreach ($request->file('file-upload') as $file)
            {
                $time = time();
                $filename = 'IRCC_' . $time;
                $media = MediaUploader::fromSource($file)
                    ->useFilename($filename)
                    ->toDirectory('attachment/' . $mailTemplate->id)
                    ->toDisk('uploads')
                    ->upload();
        
                $mailTemplate->attachMedia($media, ['attachment']);
            }
 
        }
        return redirect()->route('admin.mailTemplate.index')->with(['success_message'=>'Mail template added succesfully']);
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
    public function edit(MailTemplate $mailTemplate)
    {
        return view('admin.mail_template.form')->with([
            'mailTemplate' => $mailTemplate,
           
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MailTemplate $mailTemplate)
    {
        $mailTemplate->update($request->all());
        if (!empty($request->file('file-upload'))) {
            $images =$mailTemplate->getMedia('attachment');
            if (!empty($images)) {
                foreach ($images as $attachment) {
                    $attachment->delete();
                }
            }
            foreach ($request->file('file-upload') as $file)
            {
                $time = time();
                $filename = 'IRCC_' . $time;
                $media = MediaUploader::fromSource($file)
                    ->useFilename($filename)
                    ->toDirectory('attachment/' . $mailTemplate->id)
                    ->toDisk('uploads')
                    ->upload();
        
                $mailTemplate->attachMedia($media, ['attachment']);
            }
 
        }
        return redirect()->route('admin.mailTemplate.index')->with(['success_message'=>'Mail template updated succesfully']);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function deleteTemplate(Request $request){
        MailTemplate::where('id',$request->deleteId)->delete();
        return redirect()->route('admin.mailTemplate.index')->with(['success_message'=>'Mail template deleted succesfully']);
   
    }
    public function sendMail(Request $request){
        $mailTemplates=MailTemplate::where('status',1)->get();
        return view('admin.mail_template.send_mail')->with([
            'mailTemplates' => $mailTemplates,
           
        ]);
   
    }
    public function getTemplateById(Request $request){
        $mailTemplate=MailTemplate::where('id',$request->templateId)->first();
        $html='';
        if($mailTemplate->getMedia('attachment')){
          
               $html.='<label class="form-label">Attachments</label>';           
            foreach($mailTemplate->getmedia('attachment') as $image){
                $html.='<div class="image-div col-md-3">';
                if($image->mime_type=='application/pdf')
                $html.='<embed src="'.$image->getUrl().'#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="150px" /></div>';
                else
                $html.='<img class="images image-input-wrapper" style="width: 150px;height:150px; object-fit:contain" src="'.$image->getUrl().'" title="'.$image->filename.'"/></div>';
                
            }
           
    }
        $mailTemplate->html=$html;
        return response()->json($mailTemplate);
   
    }
    public function sendMailSubmit(Request $request){
        //dd($request->all());
        if($request->select_tag){
            $this->validate($request, [
                'subject' => 'required',
                'mail_body' => 'required',
                'email_to_tag' => 'required',
                ]); 
        }
        else{
            $this->validate($request, [
                'subject' => 'required',
                'mail_body' => 'required',
                'email_to' => 'required',
                ]); 
        }

        $mailHistory=New MailHistory();
        $mailHistory->tag=$request->select_tag;
        $mailHistory->template=$request->select_template;
        $mailHistory->mail_to=$request->email_to;
        $mailHistory->subject=$request->subject;
        $mailHistory->mail_body=$request->mail_body;
        $mailHistory->status=0;
        $mailHistory->save();
        if($request->select_tag){
            
            $emails=$request->email_to_tag;
            // foreach($emails as $email){
                sendMailJob::dispatch($emails,'',$mailHistory->id);
               
        //    }
        }
        else{
                // Mail::to($request->email_to, 'IRCC24')->send(new Invitation($mailHistory));
                sendMailJob::dispatch([$request->email_to],'IRCC24',$mailHistory->id);
        }
        return redirect()->route('admin.sendMail')->with(['success_message'=>'Sent mails successfully']);
     
   
    }

    public function getTagEmails(Request $request){
        if($request->tag=='registration')
        $emails=CompetitionRegistration::where('register_tag','IRCC-23-24')->orWhere('register_tag','IRCC-2023')->orWhere('register_tag','IRCC-2024')->where('status',1)->get();
        elseif($request->tag=='leads')
        $emails=IrccLeads::where('tag','IRCC-23-24')->orderBy('id','DESC')->get();
        elseif($request->tag=='abstract')
        $emails=CompetitionAbstract::get();
        $option='';
        foreach($emails as $email){
            if($request->tag=='abstract')
            $option.='<option value="'.$email->correspondent_email.'" selected>'.$email->correspondent_email.'</option>';  
            else
            $option.='<option value="'.$email->email.'" selected>'.$email->email.'</option>';  
        }
       
        return response()->json(['option'=>$option]);

    }
    public function deleteAttachmentById(Request $request){
        //dd($request->all());
        $mailTemplateId=$request->mailTemplateId;
        $mailTemplate=MailTemplate::where('id',$mailTemplateId)->first();
        $status=$mailTemplate->getMedia('attachment')->where('id',$request->id)->first()->delete();
        return $status;

    }  
    
    
}
