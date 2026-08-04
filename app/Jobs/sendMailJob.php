<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\Invitation;
use App\Models\MailHistory;
use App\Models\MailTemplate;
use Illuminate\Support\Facades\Log;

class sendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 3000;
    protected $emails;
    protected $name;
    protected $mailHistory;
  
    /**
     * Create a new job instance.
     */
    public function __construct($emails,$name,$mailHistory)
    {
        $this->emails = $emails;
        $this->name = $name;
        $this->mailHistory = $mailHistory;
      
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mailHistory=MailHistory::whereId($this->mailHistory)->first();
        $template=MailTemplate::where('id',$mailHistory->template)->first();
        $attachmentsFile=[];
        if(!empty($template)){
           if($template->getMedia('attachment')->first()){
            foreach($template->getMedia('attachment') as $key=>$attachMent){
                if(!empty($attachMent->first()))
                $attachmentsFile[$key]['path']=$attachMent->first()->getUrl();
                $attachmentsFile[$key]['as']=$attachMent->first()->filename;
                $attachmentsFile[$key]['extension']=$attachMent->first()->extension;
                $attachmentsFile[$key]['mime']=$attachMent->first()->mime_type;
            }
           }
        }
        $emailsArray=[];
        foreach($this->emails as $email){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            $emailsArray[]=['email'=>$email,'name'=>''];
           }
           
        }
        $chunks = array_chunk($emailsArray,50);
        foreach($chunks as $emailto){
            Log::error($emailto);
            Log::error(json_encode($mailHistory));
            Log::error(json_encode($attachmentsFile));
            Mail::to($emailto)->send(new Invitation($mailHistory,$attachmentsFile));
        }
       
    }
}
