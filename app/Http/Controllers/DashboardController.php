<?php

namespace App\Http\Controllers;

use App\Models\AbstractDomain;
use App\Models\AbstractSubmission;
use App\Models\AbstractReviewer;
use App\Models\Category;
use App\Models\Participant;
use App\Models\Payment;
use App\Models\PaymentRefund;
use App\Models\Registration;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use App\Traits\GivebutterHelper;

class DashboardController extends Controller
{
    use GivebutterHelper;
    public function index()
    {
    
        if (!Auth::guard('customer')->check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access the dashboard.');
        }

        $user = Auth::guard('customer')->user();
        $participants = Participant::where('registration_id', $user->id)->where('type','<>',2)->get();
        $payments = Payment::where('registration_id', $user->id)->where('payment_status',3)->orderBy('id', 'DESC')->get();
        $emailVerificationMessage = null;
        if (request()->query('verified') == 1) {
            $emailVerificationMessage = 'Your email has been successfully verified!';
        }
        $categories = Category::where('status', 1)->orderBy('sort_order', 'ASC')->get();
        $settings = Setting::where('status', 1)->get();
        $categories=Category::where('status', 1)->orderBy('sort_order', 'ASC')->get();
        $minClass=$categories->min('grade_from');
        $maxClass=$categories->max('grade_to');
        $abstracts = AbstractSubmission::where('registration_id', $user->id)
            ->with('abstractReviewer')
            ->orderBy('id')
            ->get();
        $refundHistory = PaymentRefund::where('registration_id', $user->id)->get();
        $payment_summary=$this->user_payments();
        $paymentApprovePending= Payment::where('registration_id', $user->id)->where('payment_mode','Direct Bank Transfer')->where('payment_status',1)->first();
        $participant_delete = optional(Setting::where('code','participant_delete_status')->first())->value;
        return view('dashboard.index', [
            'type' => 'Register',
            'categories' => $categories,
            'settings' => $settings,
            'user' => $user,
            'participants' =>  Participant::where('registration_id', $user->id)->get(),
            'payments' =>  Payment::where('registration_id', $user->id)->orderBy('id', 'DESC')->get(),
            'emailVerificationMessage' => $emailVerificationMessage,
            'minClass'=>$minClass,
            'maxClass'=>$maxClass,
            'abstracts' => $abstracts,
            'payment_summary'=>$payment_summary,
            'refundHistory'=>$refundHistory,
            'paymentApprovePending'=>$paymentApprovePending,
            'participant_delete' => $participant_delete,
            'rise150_student_discount' => (float) str_replace(',', '', $payment_summary['rise150_student_discount'] ?? '0'),
        ]);
    }
   
    public function viewDashboard(){
        $user = Auth::guard('customer')->user();
        $participants = Participant::where('registration_id', $user->id)->where('type','<>',2)->get();
        $categories=Category::where('status', 1)->orderBy('sort_order', 'ASC')->get();
        $payments = Payment::where('registration_id', $user->id)->where('payment_status',3)->orderBy('id', 'DESC')->get();
        $participant_delete = optional(Setting::where('code','participant_delete_status')->first())->value;
            if (request()->ajax()) {
            if (request()->has('partial') && request('partial') === 'registration_details') {

                $user = Auth::guard('customer')->user();
                $participant = Participant::where('registration_id', $user->id);

                return datatables($participant)
                    ->addIndexColumn()
                    ->make(true);
            }
         $payment_summary=$this->user_payments();
         $refundHistory = PaymentRefund::where('registration_id', $user->id)->get();
         $abstracts = AbstractSubmission::where('registration_id', $user->id)
             ->with('abstractReviewer')
             ->orderBy('id')
             ->get();
        $paymentApprovePending= Payment::where('registration_id', $user->id)->where('payment_mode','Direct Bank Transfer')->where('payment_status',1)->first();
        $participant_delete = optional(Setting::where('code','participant_delete_status')->first())->value;
            return view('dashboard.registration_details', [
                'user' => $user,
                'participants' => Participant::where('registration_id', $user->id)->get(),
                'payments' => Payment::where('registration_id', $user->id)->orderBy('id', 'DESC')->get(),
                'payment_summary'=>$payment_summary,
                'refundHistory'=>$refundHistory,
                'abstracts' => $abstracts,
                'paymentApprovePending'=>$paymentApprovePending,
                'settings' => Setting::where('status', 1)->get(),
                'participant_delete' => $participant_delete,
                'rise150_student_discount' => (float) str_replace(',', '', $payment_summary['rise150_student_discount'] ?? '0'),
            ]);
        }
    }

   
    // public function user_payments(){
    //     $amountPerHead = optional(Setting::where('code', 'amount_per_head')->first())->value;
    //     $registration_fee = optional(Setting::where('code', 'registration_fee')->first())->value;
    //     $user = Auth::guard('customer')->user();
    //     $participants = Participant::where('registration_id', $user->id)->get();
    //     $participantsCount = $participants->count();
    //     $paidParticipantsCount = Payment::where('registration_id', $user->id)->sum('participants');
    //     $payments = Payment::where('registration_id', $user->id)->where('payment_status',3)->orderBy('id', 'DESC')->get();
    //     $paidAmount = $payments->isEmpty() ? 0 : $payments->sum('amount');
    //     $subTotal=$participantsCount* $amountPerHead;
    //     if($participantsCount==0)
    //     $grandTotal=0;
    //     else
    //     $grandTotal=($participantsCount* $amountPerHead)+$registration_fee;
    //     $totalAmountPayable=$grandTotal-$paidAmount;
    //     if($grandTotal>$paidAmount)
    //     $totalAmountPayable=$grandTotal-$paidAmount;
    //     else
    //     $totalAmountPayable=0;
    //     $refund=0;
    //     if($paidAmount>$grandTotal)
    //     $refund =$paidAmount-$grandTotal;
    //     return (['subTotal'=>$subTotal,'grandTotal'=>$grandTotal,'paidAmount'=>$paidAmount,'amountPayable'=>$totalAmountPayable,'refund'=>$refund]);
       
    // }

    public function updateAttendingStatus(Request $request)
    {
        $user = Auth::guard('customer')->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'is_attending' => 'required|boolean'
        ]);

        try {
            // Ensure we're working with a fresh user instance
            $user = Registration::find($user->id);
            
            $user->is_attending = $request->is_attending == 1 || $request->is_attending === true;
            $user->save();
            
            // Refresh the user model to ensure the change is persisted
            $user->refresh();
            
            // Clear any cached user data
            Auth::guard('customer')->setUser($user);

            return response()->json([
                'success' => true,
                'message' => 'Attending status updated successfully',
                'is_attending' => $user->is_attending,
                'is_attending_value' => (int)$user->is_attending
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to update attending status: ' . $e->getMessage()
            ], 500);
        }
    }

}
