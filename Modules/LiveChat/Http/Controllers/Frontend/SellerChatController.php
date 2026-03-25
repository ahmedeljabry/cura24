<?php

namespace Modules\LiveChat\Http\Controllers\Frontend;

use App\Events\MessageSent;
use App\Order;
use App\User;
use Auth;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\LiveChat\Entities\LiveChatMessage;
use Illuminate\Support\Facades\Http;

class SellerChatController extends Controller
{
    public function liveChat()
    {
        $buyers = LiveChatMessage::select('buyer_id')
            ->with('buyerList')
            ->distinct('buyer_id')
            ->where('buyer_id','!=',NULL)
            ->where('seller_id', Auth::guard('web')->user()->id)
            ->get();
        return view('livechat::frontend.seller.livechat',compact('buyers'));
    }

    public function getLoadLatestMessages(Request $request)
    {
        if(!$request->user_id) {
            return;
        }
        $messages = LiveChatMessage::where(function($query) use ($request) {
            $query->where('from_user', \Illuminate\Support\Facades\Auth::user()->id)->where('to_user', $request->user_id);
        })->orWhere(function ($query) use ($request) {
            $query->where('from_user', $request->user_id)->where('to_user', Auth::user()->id);
        })->orderBy('created_at', 'DESC')->limit(20)->get();


        $return = [];
        foreach ($messages->reverse() as $message) {
            $return[] = view('livechat::frontend.seller.message-line')->with('message', $message)->render();
        }

        return response()->json(['state' => 1, 'messages' => $return]);
    }

    /**
     * postSendMessage
     *
     * @param Request $request
     */
    public function postSendMessage(Request $request)
    {
        if(!$request->to_user || !$request->message) {
            return;
        }

        $message = new LiveChatMessage();

        $message->from_user = Auth::user()->id;
        $message->to_user = $request->to_user;

        if($request->message != '' && $request->message != null && $request->message != 'null')  {
            $message->message = strip_tags($request->message);
        } else {
            if($request->hasFile("image")) {
                // Define custom error messages for validation
                $customMessages = [
                    'image.image' => __('The uploaded file must be an image.'),
                    'image.mimes' => __('The uploaded file must be a JPEG, PNG, JPG, WebP, or GIF.'),
                    'image.max' => __('The uploaded file may not be greater than 10MB in size.'),
                ];

                // Validate the request data
                $validator = Validator::make($request->all(), [
                    'image' => 'image|mimes:jpeg,png,jpg,webp,gif|max:10240', // Maximum file size: 10MB
                ], $customMessages);

                // Check if validation fails
                if ($validator->fails()) {
                    return response()->json(['error' => $validator->errors()->first()], 422);
                }

                $filename = $this->uploadImage($request);
                $message->image = $filename;
            }
        }
        $message->seller_id = Auth::user()->id;
        $message->buyer_id = $request->to_user;
        $message->save();

        $sender   = Auth::guard('web')->user();
        $receiver = User::find($message->to_user); // use to_user directly
        if ($receiver) {
            $pushTitle = __("New message from") . " " . $sender->name;
            $pushBody  = __($message->message);

            $extraData = [
                'sender_id'   => $sender->id,
                'sender_name' => $sender->name,
                'message'     => $message->message
            ];

            send_push_notification(
                $receiver,       // Notifiable
                $message->id,    // Identity
                'Chat',          // Type
                $pushBody,       // Body
                $pushTitle,      // Title
                $extraData,
                $message,
            );
        }


        $profile_image =  render_image_markup_by_attachment_id(optional($message->fromUser)->image);

        // prepare the message object along with the relations to send with the response
        $message = LiveChatMessage::with(['fromUser', 'toUser'])->find($message->id);

        // fire the event
        \event(new MessageSent($message));

        $all_array = $message->toArray() + ['profile_image'=>$profile_image];

        return response()->json(['state' => 1, 'message' => $all_array]);
    }

    public function markAsRead(Request $request)
    {
        $buyer_id = $request->buyer_id;
        $seller_id = Auth::guard('web')->user()->id;

        LiveChatMessage::where('buyer_id', $buyer_id)
            ->where('seller_id', $seller_id)
            ->where('from_user', $buyer_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['state' => 1]);
    }
    
    public function getUnreadCount(Request $request)
    {
        $user = Auth::guard('web')->user();
        $totalUnreadMessages = LiveChatMessage::where(
            $user->user_type == 0 ? 'seller_id' : 'buyer_id',
            $user->id
        )
            ->where('from_user', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'success',
            'totalUnreadMessages' => $totalUnreadMessages,
        ]);
    }

    /**
     * getOldMessages
     *
     * we will fetch the old messages using the last sent id from the request
     * by querying the created at date
     *
     * @param Request $request
     */
    public function getOldMessages(Request $request)
    {
        if(!$request->old_message_id || !$request->to_user)
            return;

        $message = LiveChatMessage::find($request->old_message_id);
        $previousMessages = $this->getPreviousMessages($request, $message);
        $return = [];

        $noMoreMessages = true;
        if($previousMessages->count() > 0) {
            foreach ($previousMessages as $message) {
                $return[] = view('livechat::frontend.seller.message-line')->with('message', $message)->render();
            }
            $noMoreMessages = !($this->getPreviousMessages($request, $previousMessages[$previousMessages->count() - 1])->count() > 0);
        }



        return response()->json(['state' => 1, 'messages' => $return, 'no_more_messages' => $noMoreMessages]);
    }

    /**
     * @param Request $request
     * @param $message
     * @return mixed
     */
    private function getPreviousMessages(Request $request, $message)
    {
        $previousMessages = LiveChatMessage::where(function ($query) use ($request, $message) {
            $query->where('from_user', Auth::user()->id)
                ->where('to_user', $request->to_user)
                ->where('created_at', '<', $message->created_at);
        })
            ->orWhere(function ($query) use ($request, $message) {
                $query->where('from_user', $request->to_user)
                    ->where('to_user', Auth::user()->id)
                    ->where('created_at', '<', $message->created_at);
            })
            ->orderBy('created_at', 'DESC')->limit(10)->get();

        return $previousMessages;
    }

    private function uploadImage($request)
    {
        $file = $request->file('image');
        $filename = md5(uniqid()) . "." . $file->getClientOriginalExtension();

        // file scan start
        $uploaded_file = $file;
        $file_extension = $uploaded_file->getClientOriginalExtension();
        if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            $processed_image = Image::make($uploaded_file);
            $image_default_width = $processed_image->width();
            $image_default_height = $processed_image->height();

            $processed_image->resize($image_default_width, $image_default_height, function ($constraint) {
                $constraint->aspectRatio();
            });
            $processed_image->save('assets/uploads/chat_image/' . $filename);
        }else{
            $file->move('assets/uploads/chat_image', $filename);
        } // file scan end

        return $filename;
    }

}
