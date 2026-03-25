<?php

namespace Modules\JobPost\Http\Controllers\Frontend;

use App\AdminNotification;
use App\Category;
use App\ChildCategory;
use App\Country;
use App\Helpers\FlashMsg;
use App\Mail\BasicMail;
use App\ServiceCity;
use App\Subcategory;
use Auth;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Mail;
use Modules\JobPost\Entities\BuyerJob;
use Modules\JobPost\Entities\JobRequest;
use Str;

class JobPostController extends Controller
{
    public function all_jobs(Request $request)
    {

        if(!empty($request->job_id || $request->job_date || $request->job_status || $request->job_type || $request->job_title || $request->job_budget )){

            $job_query = BuyerJob::where('buyer_id', Auth::guard('web')->user()->id);

            // search by ID
            if (!empty($request->job_id)){
                $job_query->where('id', $request->job_id);
            }
            // search by order create date
            if (!empty($request->job_date)){
                $start_date = \Str::of($request->job_date)->before('to');
                $end_date = \Str::of($request->job_date)->after('to');
                $job_query->whereBetween('created_at', [$start_date,$end_date]);
            }
            // search by  status
            if (!empty($request->job_status)){
                if ($request->job_status == 'active'){
                    $job_query->where('status', 1);
                }else{
                    $job_query->where('status', 0);
                }
            }

            // search by job type
            if (!empty($request->job_type)){
                if ($request->job_type == 'online'){
                    $job_query->where('is_job_online', 1);
                }else{
                    $job_query->where('is_job_online', 0);
                }
            }

            // search by job_budget
            if (!empty($request->job_budget)){
                $job_query->where('price', 'LIKE', "%{$request->job_budget}%");
            }

            // search by job title
            if (!empty($request->job_title)){
                 $job_query->where('title',  'LIKE', "%{$request->job_title}%");
            }

            $jobs = $job_query->orderByDesc('id')->paginate(6);

        }else{
            $jobs = BuyerJob::where('buyer_id', Auth::guard('web')->user()->id)->orderByDesc('id')->paginate(6);
        }

        return view('jobpost::frontend.buyer.all-jobs',compact('jobs'));
    }

    //get sub category while change category
    public function sub_category(Request $request)
    {
        $sub_categories = Subcategory::where('category_id', $request->category_id)->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'sub_categories' => $sub_categories,
        ]);
    }

    //get child category while change sub category
    public function child_category(Request $request)
    {
        $child_categories = ChildCategory::where('sub_category_id', $request->sub_cat_id)->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'child_category' => $child_categories,
        ]);
    }

    //get city while change country
    public function city(Request $request)
    {
        $cities = ServiceCity::where('country_id', $request->country_id)->where('status', 1)->get();
        return response()->json([
            'status' => 'success',
            'cities' => $cities,
        ]);
    }

    //add new job post
    public function add_job(Request $request)
    {

        if($request->isMethod('post')){

            if($request->is_job_online == 1){
                $request->validate([
                    'category' => 'required',
                    'subcategory' => 'required',
                    'title' => 'required|max:191',
                    'description' => 'required|min:150',
                    'price' => 'required|numeric',
                    'dead_line' => 'required',
                    'image' => 'required'
                ]);
                $country_id = 0;
                $city_id = 0;
            }else{
                $request->validate([
                    'category' => 'required',
                    'subcategory' => 'required',
                    'country_id' => 'required',
                    'city_id' => 'required',
                    'title' => 'required|max:191',
                    'description' => 'required|min:150',
                    'price' => 'required|numeric',
                    'dead_line' => 'required',
                ]);
                $country_id = $request->country_id;
                $city_id = $request->city_id;
            }

                if(get_static_option('job_create_settings') == 'active'){
                    $job_status = 1;
                }else{
                    $job_status = 0;
                }

    $created_job =  BuyerJob::create([
                'category_id'=>$request->category,
                'subcategory_id'=>$request->subcategory,
                'child_category_id'=>$request->child_category,
                'buyer_id'=>Auth::guard('web')->user()->id,
                'country_id'=>$country_id,
                'city_id'=>$city_id,
                'title'=>$request->title,
                'slug' => create_slug($request->slug, 'BuyerJob', true, 'JobPost', 'slug'),
                'description'=>$request->description,
                'image'=>$request->image,
                'is_job_online'=>$request->is_job_online,
                'price'=>$request->price,
                'dead_line'=>$request->dead_line,
                'status'=>$job_status,
            ]);

            // admin notification add
            AdminNotification::create(['job_post_id' => $created_job->id]);

            try {
                $message = get_static_option('job_create_message') ?? '';
                $message = str_replace(["@job_post_id"],[$created_job->id],$message);
                Mail::to(get_static_option('site_global_email'))->send(new BasicMail([
                    'subject' => get_static_option('job_create_subject') ?? __('New Job Post Created'),
                    'message' => $message
                ]));
            } catch (\Exception $e) {
                FlashMsg::item_new($e->getMessage());
            }

            toastr_success(__('Job Post Added Success'));
            return redirect()->route('buyer.all.jobs');
        }
        $categories = Category::where('status',1)->get();
        $countries = Country::where('status',1)->whereHas('cities')->get();
        return view('jobpost::frontend.buyer.add-job',compact('categories','countries'));
    }

    //edit job post
    public function edit_job(Request $request,$id=null)
    {
        if($request->isMethod('post')){
            if($request->is_job_online == 1){
                $request->validate([
                    'category' => 'required',
                    'subcategory' => 'required',
                    'title' => 'required|max:191',
                    'description' => 'required|min:150',
                    'price' => 'required|numeric',
                    'dead_line' => 'required',
                ]);
                $country_id = 0;
                $city_id = 0;
            }else{
                $request->validate([
                    'category' => 'required',
                    'subcategory' => 'required',
                    'country_id' => 'required',
                    'city_id' => 'required',
                    'title' => 'required|max:191',
                    'description' => 'required|min:150',
                    'price' => 'required|numeric',
                    'dead_line' => 'required',
                ]);
                $country_id = $request->country_id;
                $city_id = $request->city_id;
            }

            if(get_static_option('job_create_settings') == 'active'){
                $job_status = 1;
            }else{
                $job_status = 0;
            }

            BuyerJob::where('id',$id)->update([
                'category_id'=>$request->category,
                'subcategory_id'=>$request->subcategory,
                'child_category_id'=>$request->child_category,
                'buyer_id'=>Auth::guard('web')->user()->id,
                'country_id'=>$country_id,
                'city_id'=>$city_id,
                'title'=>$request->title,
                'slug' => create_slug($request->slug, 'BuyerJob', true, 'JobPost', 'slug'),
                'description'=>$request->description,
                'image'=>$request->image,
                'is_job_online'=>$request->is_job_online,
                'price'=>$request->price,
                'dead_line'=>$request->dead_line,
                'status'=>$job_status,
            ]);
            toastr_success(__('Job Post Updated Success'));
            return redirect()->route('buyer.all.jobs');
        }
        $job = BuyerJob::find($id);

        $categories = Category::where('status',1)->get();
        $countries = Country::where('status',1)->whereHas('cities')->get();
        return view('jobpost::frontend.buyer.edit-job',compact('categories','countries','job'));
    }

    //Job post on off
    public function job_on_off(Request $request)
    {
        $is_job_on = BuyerJob::select('id','is_job_on')->where('id', $request->job_post_id)->first();
        $is_job_on->is_job_on === 1 ? $is_job_on = 0 : $is_job_on = 1;
        BuyerJob::where('id', $request->job_post_id)->update(['is_job_on' => $is_job_on]);
        return response()->json([
            'status' => 'success',
        ]);
    }

    //job delete
    public function job_delete($id = null)
    {
        JobRequest::where('job_post_id',$id)->delete();
        BuyerJob::find($id)->delete();
        toastr_error(__('Job Post Delete Success'));
        return back();
    }
}
