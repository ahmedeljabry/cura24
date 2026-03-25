<?php

namespace Modules\JobPost\Http\Controllers\Backend;

use App\Helpers\FlashMsg;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Modules\JobPost\Entities\BuyerJob;
use Modules\JobPost\Entities\JobRequest;
use Modules\JobPost\Entities\JobRequestConversation;

class JobsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('permission:job-list|job-status|job-delete',['only' => ['jobs']]);
        $this->middleware('permission:job-status',['only' => ['change_status']]);
        $this->middleware('permission:job-delete',['only' => ['delete']]);
    }

    public function jobs()
    {
        $current_date = date('Y-m-d h:i:s');
        $all_jobs = BuyerJob::orderByDesc('id')->get();
        return view('jobpost::backend.jobs',compact('all_jobs'));
    }

    public function change_status($id)
    {
        $job = BuyerJob::with('buyer')->findOrFail($id);

        // Capture old status before toggling
        $old_status = $job->status == 1 ? 'Active' : 'Inactive';

        // Toggle status
        $status = $job->status == 1 ? 0 : 1;

        // Update in DB
        BuyerJob::where('id', $id)->update(['status' => $status]);

        // New status label
        $new_status = $status == 1 ? 'Active' : 'Inactive';

        if ($status === 1 && $job->buyer) {
            $buyer = $job->buyer;

            $pushTitle = __('Job Post Approved');
            $pushBody = __('Your job :title has been approved and is now live!', [
                'title' => $job->title ?? 'Job'
            ]);

            $extraData = [
                'job_id'     => $job->id,
                'job_title'  => $job->title ?? 'Untitled Job',
                'buyer_id'   => $buyer->id,
                'buyer_name' => $buyer->name ?? 'Buyer',
                'old_status' => $old_status,
                'new_status' => $new_status,
            ];

            send_push_notification(
                $buyer,             // Notifiable (buyer model)
                $job->id,           // Identity
                'JobStatus',              // Type
                $pushBody,          // Body
                $pushTitle,         // Title
                $extraData,
                $job,
                true
            );
        }
        return redirect()->back()->with(FlashMsg::item_new('Status Changed Success'));
    }

    public function delete($id){
        BuyerJob::find($id)->delete();
        return redirect()->back()->with(FlashMsg::item_new('Job Deleted Success'));
    }

    public function all_request($id)
    {
        $all_request = JobRequest::with('job')->where('job_post_id',$id)->orderByDesc('id')->get();
        return view('jobpost::backend.all-request',compact('all_request'));
    }

    public function conversation_details($id)
    {
        $request_details = JobRequest::with('job')
            ->where('id',$id)
            ->first();
        $all_messages = JobRequestConversation::where(['job_request_id'=>$id])->get();
        $q = $request->q ?? '';
        return view('jobpost::backend.view-conversation', compact('request_details','all_messages','q'));
    }

    public function jobSettings()
    {
        return view('jobpost::backend.job-settings');
    }
    public function jobCreateSettingsUpdate(Request $request)
    {
        update_static_option('job_create_settings',$request->job_create_settings);
        update_static_option('job_otp_verification_required', $request->job_otp_verification_required);
        update_static_option('job_overview_title',$request->job_overview_title);
        update_static_option('job_starting_at_price_title',$request->job_starting_at_price_title);
        update_static_option('job_hire_modal_title',$request->job_hire_modal_title);
        return redirect()->back()->with(FlashMsg::item_new('Update Success'));

    }

}
