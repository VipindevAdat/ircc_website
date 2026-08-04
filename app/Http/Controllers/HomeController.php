<?php

namespace App\Http\Controllers;

use App\Mail\AccompanyingRegister;
use App\Models\CompetitionRegistration;
use App\Models\CompetitionAbstract;
use App\Models\CompetitionRegistrationTeam;
use App\Models\Category;
use App\Models\ContactUs;
use App\Models\Subscriber;
use App\Models\AccompanyPerson;
use App\Models\AccompanyPaymentDetail;
use App\Models\Faq;
use App\Models\AbstractSubmission;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\Register;
use App\Mail\AdminRegister;
use App\Mail\UserAbstract;
use App\Mail\AccompanyingVerify;
use App\Mail\AdminAbstract;
use App\Models\AbstractBook;
use App\Models\AwardGraphic;
use App\Models\Gallery;
use App\Models\Review;
use App\Models\ProgramScheduleDay;
use App\Models\ProgramScheduleSetting;
use App\Models\Setting;
use App\Models\Team;
use App\Models\TeamCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;
// PDF;
class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categoryCommittee=optional(TeamCategory::where('parent_id',1)->where('status',1)->get())->pluck('id')->toArray();
        $patronCat=optional(TeamCategory::where('parent_id',1)->where('name','Patron')->first())->id;
        $patrons=Team::where('team_category_id',$patronCat)->where('status',1)->orderBy('sort_order','ASC')->get();
        $committees=Team::with('category')->whereIn('team_category_id',$categoryCommittee)->where('team_category_id','<>',$patronCat)->where('is_home',1)->where('status',1)->orderBy('sort_order','ASC')->get()->take(4);
        
        // Get speakers for homepage carousel
        $speakers = collect();
        try {
            // Find parent "Speakers" category
            $speakerParent = TeamCategory::whereNull('parent_id')
                ->where('status', 1)
                ->where(function($query) {
                    $query->where('name', 'LIKE', '%Speaker%')
                          ->orWhere('name', 'LIKE', '%speaker%')
                          ->orWhere('id', 2); // Fallback to ID 2 if it exists
                })
                ->first();
            
            if ($speakerParent) {
                // Get all sub-categories under Speakers
                $speakerCategoryIds = TeamCategory::where('parent_id', $speakerParent->id)
                    ->where('status', 1)
                    ->pluck('id')
                    ->toArray();
                
                if (!empty($speakerCategoryIds)) {
                    $speakers = Team::with('category')
                        ->whereIn('team_category_id', $speakerCategoryIds)
                        ->where('status', 1)
                        ->orderBy('sort_order', 'ASC')
                        ->get();
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Error loading speakers for homepage: ' . $e->getMessage());
        }
    
        return view('home')->with([
            'settings'=>Setting::where('status',1)->get(),
            'reviews'=>Review::where('is_home',1)->where('status',1)->orderBy('sort_order','ASC')->get()->take(3),
            'patrons'=>$patrons,
            'committee'=>$committees,
            'speakers'=>$speakers
        ]);
    }
    public function registerSuccess(){
        return view('auth.registraion-successs');
    }
    public function registerFailure(){
        return view('auth.registration-failure');
    }
    public function isRegister(Request $request)
    {
        $registered_id=$request->registered_id;
        $registration=CompetitionRegistration::with('teams')->where('registration_number',$registered_id)->first();
        $type="abstract";
        if($registration){
            $abstract=CompetitionAbstract::where('registration_number',$registered_id)->first();
            
            if($abstract){
                $div = view('components.registration_details', compact('registration','type'))->render();
                $data['error']=1;
                $data['message']="You have been already submitted abstract";
                $data['html']= $div;
            }
            else{
                $div = view('components.registration_details', compact(['registration','type']))->render();
                $abstract = view('components.abstract-form', compact(['registration','type']))->render();
                $data['error']=0;
                $data['data']=$registration;
                $data['abstract']=$abstract;
                $data['html']= $div;

            }
            
            
        }
        else{
            $data['error']=1;
            $data['message']="Entered Wrong register number";
        }
        return response()->json($data);
        
    }    
    public function ajaxLoad(Request $request)
    {
    $div='';
    $option='';
    $status=1;
    $s=1;
    $n=12;
    $category=Category::whereId($request->category)->first();
    if($category->type==2){
        $s=$category->grade_from;
        $n=$category->grade_to;
    }
      else{
        $s=0;
        $n=0;
      }
           
            $div = view('components.students-details', compact(['request','option','s','n']))->render();
            $data['status']=$status;
            $data['error']=0;
            $data['html']=$div;
            return response()->json($data);

    }
    public function ajaxAccompany(Request $request)
    {
        $div='';
        $number=$request->number;
        $div = view('components.accompany', compact(['number']))->render();
        $data['error']=0;
        $data['html']=$div;
        return response()->json($data);

        

    }
    public function abstractSubmit(Request $request){
        $cript= Crypt::encryptString($request->register_number);
        $register_number=Crypt::decryptString($request->register_number);
      
        $registration=CompetitionRegistration::where('id',$register_number)->first();
      
        if(empty($registration)){
            $data['error']=1;
            $data['message']="Something went wrong";
            return response()->json($data); 
        }
      
        $abstract=CompetitionAbstract::where('registration_number',$registration->registration_number)->first();
        if(empty($abstract)){
      
        $lastid=CompetitionAbstract::latest()->first()?CompetitionAbstract::latest()->first()->id:0;
        $lastid = (int)$lastid+(int)1;
        $abstract_num = 'PID'.$lastid;
        $request->merge(['abstract_num'=>$abstract_num]);
        $created_at = date('Y-m-d H:i:s');
       
        $title=$Str = str_replace("'", '',$request->title);
        $affiliation=str_replace("'", '',$request->affiliation);
        $reference=str_replace("'", '',$request->reference);
        $abstract=str_replace("'", '',$request->abstract);
        $abstr_tag=optional(Setting::where('code','abstract_tag')->first())->value;
        $abstract=CompetitionAbstract::create(['title'=>$title,'abstract_number'=>$abstract_num,
        'registration_id'=>$registration->id,
        'registration_number'=>$registration->registration_number,'category'=>$registration->category,
        'authors'=>$request->author_name,'correspondent_name'=>$request->correspondent_name,
        'correspondent_email'=>$request->correspondent_email,'affiliation'=>$affiliation,
        'abstract'=>$abstract,'reference'=>$reference,'tag'=>$abstr_tag]);
            
            if(!$abstract){
                $data['error']=1;
                $data['message']=" Something went wrong";
                return response()->json($data);
            }
            else
            {
                $result=CompetitionAbstract::where('id',$abstract->id)->first();
                $category=Category::where('name',$result->category)->first();
                $categoryPrefix=$category?$category->prefix:substr($result->category, 0, 1);
                if(!empty($lastid['abstract_index'])){
                    $index = $lastid['abstract_index']+1; 
                }
                else{
                    $index=$category?$category->last_number:1;
                }
               
                $abstract_index=$categoryPrefix.'-'.sprintf('%03d', $index);
                $lastid=CompetitionAbstract::latest()->first()->id;
                $pdf = Pdf::loadView('mail.abstract-pdf', ['result' => $result,'id'=>$abstract_index]);
                $pdf->setPaper('A4', 'landscape'); 
                $pdf->render(); 
                $output = $pdf->output();
                Storage::disk('uploads')->put('abstract/'.$lastid.$abstract_index.'_proposal.pdf', $output);    
                $abstract->update(['abstract_file'=>$lastid.$abstract_index.'_proposal.pdf']);
                // Mail::to($abstract->correspondent_email, $abstract->correspondent_name)->send(new UserAbstract($abstract));
                // Mail::to('ignite@ircc.world', 'ignite@ircc.world')->cc(['contactustoircc@gmail.com'])->send(new AdminAbstract($abstract));
                // $data['error']=0;
                // $data['author']=implode(',',$request->author_name);
                $data['message']="Abstract sent successfully";
                return response()->json($data);
        
            }
        }
        else{
            $data['error']=1;
            $data['message']="You have been already submitted abstract";
            return response()->json($data);
        }
        
    }

    public function projectSubmit(Request $request){
  
        $email=$request->email;
        $type="register";
        $registration=CompetitionRegistration::where('email',$email)->first();
        //$stud = $this->db->query("SELECT *  FROM competition_registration where email = '$email'");
        if(!$registration){
            $_POST['created_at'] = date('Y-m-d H:i:s');
            $lastid =CompetitionRegistration::latest()->first()?CompetitionRegistration::latest()->first()->id:0;
                $lastid = (int)$lastid+(int)1;
            $_POST['register_num'] = 'IRCC'.$lastid;
            $_POST['status'] =1;
            $reg_tag=optional(Setting::where('code','registration_tag')->first())->value;
           // $reg_tag='IRCC-23-24';
            $request->merge(['name'=>$request->teacher_name,'created_at'=>date('Y-m-d H:i:s'),'register_num'=>'IRCC'.$lastid,
            'status'=>1,'mark'=>1,'message'=>'test','country'=>'test']);

            $registration=CompetitionRegistration::create(['school_name'=>$request->school_name?$request->school_name:'-',
            'teacher_name'=>$request->teacher_name,'designation'=>$request->teacher,'first_name'=>$request->first_name,
            'last_name'=>$request->last_name,'phone'=>$request->phone, 'email'=>$request->email, 'category'=>$request->category,'category_id'=>$request->category_id,'participation'=>$request->participation,
            'registration_number'=>'IRCC'.$lastid, 'members'=>$request->members,'no_accompany'=>$request->no_accompany, 'status'=>1,'register_tag'=>$reg_tag, 'mark'=>0, 
            'reason'=>$request->reason, 'other'=>$request->other]);
        
            $last_id = CompetitionRegistration::latest()->first()->id;; 
            
            // print_r($registration);exit;
            foreach($request->stud_first_name as $key=>$value){

                CompetitionRegistrationTeam::create(['registration_id'=>$last_id,'first_name'=>$request->stud_first_name[$key],'last_name'=>$request->stud_last_name[$key],
            'class'=>isset($request->stud_class[$key])?$request->stud_class[$key]:'']);
                
            }
            $payment_amount=0;
            $registration=CompetitionRegistration::with(['teams','accompany','accompany_payment'])->where('id',$registration->id)->first();
            if($registration){
                if($request->accompanyAdd==1){
                    foreach($request->accompany_name as $key=>$value){
                        AccompanyPerson::create(['registration_id'=>$registration->id,'name'=>$request->accompany_name[$key],'email'=>$request->accompany_email[$key]]);
                       // Mail::to($request->accompany_email[$key],$request->accompany_name[$key])->send(new AccompanyingRegister($registration));
                    }
                    $payment_date='';
                    if($request->payment_method=='bank'){
                       $payment_date=date('Y-m-d',strtotime($request->payment_date));
                    }
                    else{
                        $payment_date=date('Y-m-d',strtotime($request->payment_date_upi)); 
                    }
                    
                    $reference=$request->payment_method=='upi'?$request->payment_reference_upi:$request->payment_reference_bank;
               
                    AccompanyPaymentDetail::create(['registration_id'=>$registration->id,'payment_method'=>$request->payment_method,'payment_reference'=>$reference,'payment_date'=>$payment_date,'payment_amount'=>$payment_amount]);
                }

                // $send= $this->register_mail_abstract($request,'contactus');
                $registration=CompetitionRegistration::with(['teams','accompany','accompany_payment'])->where('id',$registration->id)->first();
                // Mail::to($request->email,$request->teacher_name)->send(new Register($registration));
                // Mail::to('ignite@ircc.world', 'ignite@ircc.world')->cc(['contactustoircc@gmail.com'])->send(new AdminRegister($registration));
                // if($request->accompanyAdd==1){
                // Mail::to($request->email,$request->teacher_name)->send(new AccompanyingRegister($registration));
                // }
                if( $registration){ 
                    $div = view('components.registration_details', compact(['registration','type']))->render();
                    $data['error']=0;
                    $data['message']="Your registration completed successfully";
                    $data['data']=$registration;
                    $data['html']=$div;
                } 
                echo json_encode($data);
                // return response()->json($data);
            } 
        }
        else{
            
            $data['error']=1;
            $data['message']="User with email already exist please check your mail";
            //return response()->json($data);
            echo json_encode($data);
        }
        
    }
public function abstractNow(Request $request){
    //dd($request->all());
    // return view('abstract')->with(['type'=>'Abstract','register'=>CompetitionRegistration::where('id',$request->reg_id)->first()]);
    return redirect('register/abstract')->with(['type'=>'Abstract','register'=>CompetitionRegistration::where('id',$request->reg_id)->first()]);
}
public function about()
{
    return view('about')->with([
        'settings' => Setting::where('status', 1)->get(),
        'convenerPortraitUrl' => Team::committeePortraitUrl('Devi Gireesh', 'front/images/committee/chair.png'),
        'coChairPortraitUrl' => Team::committeePortraitUrl('Padmapriya Palottu'),
    ]);
}
public function committee(){
    // Get committee categories - find parent "Committee" category and get its children
    $categoryCommitteeIds = [];
    
    // Approach 1: Find parent category by ID 4 (Committee)
    $committeeParent = TeamCategory::where('id', 4)
        ->whereNull('parent_id')
        ->where('status', 1)
        ->first();
    
    // Approach 2: If not found by ID, find by name containing "Committee"
    if (!$committeeParent) {
        $committeeParent = TeamCategory::whereNull('parent_id')
            ->where('status', 1)
            ->where(function($query) {
                $query->where('name', 'LIKE', '%Committee%')
                      ->orWhere('name', 'LIKE', '%committee%');
            })
            ->first();
    }
    
    // Approach 3: Fallback - find categories with parent_id = 4
    if (!$committeeParent) {
        $directCategories = TeamCategory::where('parent_id', 4)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();
        $categoryCommitteeIds = $directCategories;
    } else {
        // Get all sub-categories under the parent (child categories only, not parent)
        $subCategories = TeamCategory::where('parent_id', $committeeParent->id)
            ->where('status', 1)
            ->pluck('id')
            ->toArray();
        $categoryCommitteeIds = $subCategories;
    }
    
    // Get Patron category (should be under Committee parent)
    $patronCat = null;
    if ($committeeParent) {
        $patronCat = TeamCategory::where('parent_id', $committeeParent->id)
            ->where('name', 'LIKE', '%Patron%')
            ->where('status', 1)
            ->first();
    }
    $patronCatId = $patronCat ? $patronCat->id : null;
    
    // Get Patrons
    $patrons = collect();
    if ($patronCatId) {
        $patrons = Team::with('media')
            ->where('team_category_id', $patronCatId)
            ->where('status', 1)
            ->orderBy('sort_order', 'ASC')
            ->get();
    }
    
    // Get Committees (exclude Patrons and exclude Speaker categories)
    $committees = collect();
    if (!empty($categoryCommitteeIds)) {
        // Also exclude speaker categories (IDs 2, 3) to prevent speakers from appearing
        $speakerCategoryIds = [2, 3]; // Keynote Speakers, Distinguished Keynote Speaker
        
        $committees = Team::with(['category', 'media'])
            ->whereIn('team_category_id', $categoryCommitteeIds)
            ->whereNotIn('team_category_id', $speakerCategoryIds)
            ->where('status', 1);
        
        if ($patronCatId) {
            $committees = $committees->where('team_category_id', '<>', $patronCatId);
        }
        
        $committees = $committees->orderBy('sort_order', 'ASC')
            ->get()
            ->groupBy('team_category_id');

        // Same roles are already rendered above via $committeeLeadershipBlocks (Convener, Co-Chairs as presidents, etc.).
        // Hide DB categories that would duplicate those sections on /committee.
        $committeePageDuplicateCategoryNames = ['conference chairs', 'convener'];
        $committees = $committees->filter(function ($teams) use ($committeePageDuplicateCategoryNames) {
            $categoryName = mb_strtolower(trim((string) optional($teams->first()->category)->name));

            return ! in_array($categoryName, $committeePageDuplicateCategoryNames, true);
        });

        // After hard-coded leadership: Student Ambassador stays between that block and steering
        // committees (category name contains "student ambassador"), regardless of sort_order on other categories.
        $committees = $committees->sortBy(function ($teams) {
            $cat = optional($teams->first()->category);
            $name = mb_strtolower(trim((string) $cat->name));
            $order = (int) (optional($cat)->sort_order ?? 9999);
            if (str_contains($name, 'student ambassador')) {
                return $order;
            }

            return 100000 + $order;
        });
    }

    $portrait = static fn (string $name, ?string $static = null, ?string $upload = null) => Team::committeePortraitUrl($name, $static, $upload);

    $committeeLeadershipBlocks = [
        [
            'title' => 'Convener',
            'members' => [
                ['name' => 'Devi Gireesh', 'designation' => 'Convener', 'organization' => 'IRCC', 'img' => $portrait('Devi Gireesh', 'front/images/committee/chair.png')],
            ],
        ],
        [
            'title' => 'Co-Chair',
            'members' => [
                ['name' => 'Padmapriya Palottu', 'designation' => 'Co-Chair', 'organization' => '', 'img' => $portrait('Padmapriya Palottu')],
                ['name' => 'Dr. Irimpan Mathews', 'designation' => 'Co-Chair', 'organization' => 'Global Young Researchers’ Academy (GYRA), Oregon, USA', 'img' => $portrait('Dr. Irimpan Mathews', 'front/images/committee/drmathews.png')],
                ['name' => 'Preethi Pinto', 'designation' => 'Co-Chair', 'organization' => 'STEM4Girls', 'img' => $portrait('Preethi Pinto')],
            ],
        ],
        [
            'title' => 'Scientific Committee Chair',
            'members' => [
                ['name' => 'Dr. Finosh Thankam', 'designation' => 'Scientific Committee Chair', 'organization' => 'GYRA, USA', 'img' => $portrait('Dr. Finosh Thankam', null, 'uploads/teams/19/GYRA_1773885376.jpg')],
            ],
        ],
    ];

    return view('committee')->with([
        'settings' => Setting::where('status', 1)->get(),
        'patrons' => $patrons,
        'committees' => $committees,
        'committeeLeadershipBlocks' => $committeeLeadershipBlocks,
    ]);
}
public function speakers(){
    try {
        // Get speaker categories - find parent "Speakers" category and get its children
        $categorySpeakersIds = [];
        
        // Approach 1: Find parent category by ID 1 (Speakers)
        $speakerParent = TeamCategory::where('id', 1)
            ->whereNull('parent_id')
            ->where('status', 1)
            ->first();
        
        // Approach 2: If not found by ID, find by name containing "Speaker"
        if (!$speakerParent) {
            $speakerParent = TeamCategory::whereNull('parent_id')
                ->where('status', 1)
                ->where(function($query) {
                    $query->where('name', 'LIKE', '%Speaker%')
                          ->orWhere('name', 'LIKE', '%speaker%');
                })
                ->first();
        }
        
        // Approach 3: Fallback - find categories with parent_id = 1
        if (!$speakerParent) {
            $directCategories = TeamCategory::where('parent_id', 1)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();
            $categorySpeakersIds = $directCategories;
        } else {
            // Get all sub-categories under the parent (child categories only, not parent)
            $subCategories = TeamCategory::where('parent_id', $speakerParent->id)
                ->where('status', 1)
                ->pluck('id')
                ->toArray();
            $categorySpeakersIds = $subCategories;
        }
        
        // Remove duplicates
        $categorySpeakersIds = array_unique($categorySpeakersIds);
        
        // Separate Distinguished and Keynote speakers
        $distinguishedSpeakers = collect();
        $keynoteSpeakers = collect();
        
        if (!empty($categorySpeakersIds)) {
            // Find Distinguished Keynote Speaker category
            $distinguishedCategoryId = TeamCategory::where('parent_id', $speakerParent ? $speakerParent->id : 1)
                ->where('name', 'LIKE', '%Distinguished%')
                ->where('status', 1)
                ->value('id');
            
            // Find Keynote Speaker category (not Distinguished)
            $keynoteCategoryId = TeamCategory::where('parent_id', $speakerParent ? $speakerParent->id : 1)
                ->where('name', 'LIKE', '%Keynote%')
                ->where('name', 'NOT LIKE', '%Distinguished%')
                ->where('status', 1)
                ->value('id');
            
            // Get all speakers
            $allSpeakers = Team::with('category')
                ->whereIn('team_category_id', $categorySpeakersIds)
                ->where('status', 1)
                ->orderBy('sort_order', 'ASC')
                ->get();
            
            // Separate into Distinguished and Keynote
            foreach ($allSpeakers as $speaker) {
                if ($speaker->category) {
                    $categoryName = strtolower($speaker->category->name);
                    if (stripos($categoryName, 'distinguished') !== false) {
                        $distinguishedSpeakers->push($speaker);
                    } elseif (stripos($categoryName, 'keynote') !== false || $speaker->team_category_id == $keynoteCategoryId) {
                        $keynoteSpeakers->push($speaker);
                    } else {
                        // If category doesn't match, check by category ID
                        if ($distinguishedCategoryId && $speaker->team_category_id == $distinguishedCategoryId) {
                            $distinguishedSpeakers->push($speaker);
                        } elseif ($keynoteCategoryId && $speaker->team_category_id == $keynoteCategoryId) {
                            $keynoteSpeakers->push($speaker);
                        } else {
                            // Default to keynote if not distinguished
                            $keynoteSpeakers->push($speaker);
                        }
                    }
                } else {
                    // If no category, default to keynote
                    $keynoteSpeakers->push($speaker);
                }
            }
        } else {
            \Log::warning('No speaker categories found. Please ensure a parent category "Speakers" (id=1 or name containing "Speaker") exists in Admin Panel > Team Categories, and sub-categories are assigned to it.');
        }
        
        return view('speakers')->with([
            'settings' => Setting::where('status', 1)->get(),
            'distinguishedSpeakers' => $distinguishedSpeakers,
            'keynoteSpeakers' => $keynoteSpeakers
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error loading speakers page', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Return empty collections on error
        return view('speakers')->with([
            'settings' => Setting::where('status', 1)->get(),
            'distinguishedSpeakers' => collect(),
            'keynoteSpeakers' => collect()
        ]);
    }
}

public function programSchedule(){
    $settings = Setting::where('status', 1)->get();
    $pageSettings = ProgramScheduleSetting::current();
    $days = ProgramScheduleDay::where('status', true)
        ->where('is_visible', true)
        ->orderBy('sort_order')
        ->with(['activeItems'])
        ->get();

    return view('program-schedule')->with([
        'settings' => $settings,
        'pageSettings' => $pageSettings,
        'days' => $days,
    ]);
}

public function donation(){
    return view('donation')->with(['settings'=>Setting::where('status',1)->get()]);
}

public function awards()
{
    $settings = Setting::where('status', 1)->get();
    $awardGraphics = AwardGraphic::query()
        ->with('media')
        ->where('status', 1)
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get();

    return view('awards')->with([
        'settings' => $settings,
        'awardGraphics' => $awardGraphics,
    ]);
}
public function contact_us(){
    return view('contact-us')->with(['settings'=>Setting::where('status',1)->get()]);
}
    public function guidelines()
    {
        $user = Auth::guard('customer')->user();
        if($user){
            $abstract=AbstractSubmission::where('registration_id', $user->id)->first();
        }else {
            $abstract=null;
        }

        return view('guidelines')->with([
            'settings'=>Setting::where('status',1)->get(),
             'user'=>$user,
             'abstract' => $abstract
            ]);
    }
public function policy(){
    return view('policy')->with(['settings'=>Setting::where('status',1)->get()]);
}
// public function term_condition(){
//     return view('terms-conditions');
// }
// public function faq(){
//     return view('faq');
// }
public function register(){
    return view('register-first')->with(['type'=>'Register','settings'=>Setting::where('status',1)->get()]);
}
public function registration(){

    return view('registration')->with(['type'=>'Register','categories'=>Category::where('status',1)->orderBy('sort_order','ASC')->get(),'settings'=>Setting::where('status',1)->get()]);
}
public function spotVerify(Request $request){
    if($request->verification_code=='Ircc@2324'){
        return view('registration-spot')->with(['type'=>'Register','categories'=>Category::where('status',1)->orderBy('sort_order','ASC')->get(),'settings'=>Setting::where('status',1)->get()]);
    }
    else{
        return view('registration-spot')->with(['type'=>'Verify','settings'=>Setting::where('status',1)->get(),'error_message'=>'invalid verification code']); 
    }
}
public function spotRegistration(){

    return view('registration-spot')->with(['type'=>'Verify','settings'=>Setting::where('status',1)->get()]);
}
public function reviews(){

    return view('reviews')->with(['reviews'=>Review::where('status',1)->orderBy('sort_order','ASC')->get(),'settings'=>Setting::where('status',1)->get()]);
}
public function abstractBook(){

    return view('abstract-book')->with(['settings'=>Setting::where('status',1)->get(),'abstractBooks'=>AbstractBook::where('status',1)->orderBy('sort_order','ASC')->get()]);
}
public function gallery(){

    return view('gallery')->with(['galleries'=>Gallery::where('status',1)->orderBy('sort_order','ASC')->get(),'settings'=>Setting::where('status',1)->get()]);
}
public function abstract_submission(Request $request){
   // dd($request->all());
   $register='';
   if($request->reg_id){
     $register=CompetitionRegistration::where('id',$request->reg_id)->first();
     $abstract=CompetitionAbstract::where('registration_id',$request->reg_id)->first();
     if(empty($abstract))
      return view('abstract')->with(['type'=>'Abstract','register'=>$register,'settings'=>Setting::where('status',1)->get()]);
    else
     return view('abstract')->with(['type'=>'Abstract','settings'=>Setting::where('status',1)->get()]);
   }
   return view('abstract')->with(['type'=>'Abstract','settings'=>Setting::where('status',1)->get()]);
}
public function contact_submit(Request $request){
 $contactUs=ContactUs::create(['name'=>$request->name,'email'=>$request->email,
                        'phone'=>$request->phone,'subject'=>$request->subject,
                        'message'=>$request->message]);
    if($contactUs){
        $data['error']=0;
        $data['message']="Contact us submit successfully";
    }   
    else{
        $data['error']=1;
        $data['message']="Something went wrong";
    }                   
        echo json_encode($data);
}
public function subscribe_submit(Request $request){
    $subscriber=Subscriber::where('email',$request->email)->first();
    
       if(!$subscriber){
           $subscriber=Subscriber::create(['email'=>$request->email]);
           $data['error']=0;
           $data['message']="Submit successfully";
       }   
       else{
           $data['error']=1;
           $data['message']="You are already subscribed";
       }                   
           echo json_encode($data);
   }
public function faq_submit(Request $request){
     $contactUs=Faq::create(['name'=>$request->name,'email'=>$request->email,
                           'phone'=>$request->phone,'message'=>$request->message]);
       if($contactUs){
           $data['error']=0;
           $data['message']="Faq submit successfully";
       }   
       else{
           $data['error']=1;
           $data['message']="Something went wrong";
       }                   
           echo json_encode($data);
   }
public function fileDownload(Request $request, $path){
    $filePath = public_path("images/$path");
    return response()->download($filePath);
}

public function mailTest(){
    $registration=CompetitionRegistration::with(['teams','accompany','accompany_payment'])->where('id',1)->first();
  $abstract=CompetitionAbstract::where('id',1)->first();
  //  Mail::to($abstract->correspondent_email, $abstract->correspondent_name)->send(new UserAbstract($abstract));
 // Mail::to('divya7071@gmail.com','divya')->send(new AdminAbstract($abstract));
  $registration=CompetitionRegistration::with(['accompany_payment','accompany'])->where('id', 1)->first(); 
//Mail::to($registration->email,$registration->teacher_name)->send(new AccompanyingVerify($registration));
//return view('mail.accompany-verify')->with(['data'=>$registration]);
 //return view('mail.abstract-submit-admin')->with(['data'=>$abstract]);
 return view('mail.admin-mail')->with(['data'=>$registration]);  
}

public function sendOtp(){

    $rand_no = rand(1000, 9999); 
     return response()
        ->json(["error"=>0,"success" => $rand_no." is OTP for your testing purpose",'otp'=>$rand_no]);
    
}

} 
