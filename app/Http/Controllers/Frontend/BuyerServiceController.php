<?php

namespace App\Http\Controllers\Frontend;

use App\Category;
use App\Http\Controllers\Controller;
use App\Service;
use Illuminate\Http\Request;

class BuyerServiceController extends Controller
{
    public function allServices(Request $request)
    {
        $services_query = Service::with('reviews', 'pendingOrder', 'completeOrder', 'cancelOrder');

        // search by service category
        if(!empty($request->category)){
            $services_query->where('category_id', $request->category);
        }

        // search by online offline service
        if (!empty($request->online_offline_status)) {
            if ($request->online_offline_status == 'offline') {
                $services_query->where('is_service_online', 0);
            } else if($request->online_offline_status == 'online') {
                $services_query->where('is_service_online', 1);
            }
        }

        // search by service amount
        if (!empty($request->service_price)) {
            //
            $service_id = Service::select('id', 'title')->where('price',  '<=',$request->service_price)->pluck('id')->toArray();
            $services_query->whereIn('id', $service_id);
        }

        // search by service title
        if (!empty($request->service_title)) {
            $service_id = Service::select('id', 'title')->where('title',  'LIKE', "%{$request->service_title}%")->pluck('id')->toArray();
            $services_query->whereIn('id', $service_id);
        }
        $services = $services_query->latest()->paginate(10);
        $categories=Category::where('status', 1)->get();

        return view('frontend.user.buyer.services.services', compact('services', 'categories'));
    }
}
