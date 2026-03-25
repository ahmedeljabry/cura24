<?php

namespace Modules\WhatsAppBookingSystem\Http\Services;

use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $phoneNumberId;
    protected $accessToken;
    public function __construct()
    {
        $this->phoneNumberId = get_whatsapp_option('whatsapp_phone_number_id') ?? env('WHATSAPP_PHONE_NUMBER_ID');
        $this->accessToken = get_whatsapp_option('whatsapp_permanent_token') ?? env('WHATSAPP_ACCESS_TOKEN');
    }


    public function sendText($to, $message)
    {
        return $this->sendMessage([
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'text',
            'text' => [ 'body' => $message ]
        ]);
    }

    public function sendServiceList($to, $services, $afterId = null)
    {
        // Filter services starting after $afterId
        if ($afterId !== null) {
            $services = array_filter($services, fn($s) => $s['id'] > $afterId);
        }

        // Take the next 9
        $chunk = array_slice(array_values($services), 0, 9);

        // Build rows
        $rows = array_map(function ($service) {
            return [
                'id' => "service_{$service['id']}",
                'title' => mb_strimwidth($service['title'], 0, 24, '…'),
                'description' => "Price: {$service['price']}"
            ];
        }, $chunk);

        // Add "View More" if more are left
        if (count($services) > count($chunk))
        {
            $lastId = end($chunk)['id'];
            $rows[] = [
                'id' => "view_more_after_{$lastId}",
                'title' => 'View More',
                'description' => 'Load more services'
            ];
        }

        $text=get_whatsapp_option('whatsapp_message_ask_service_select', 'Please choose a service:');
        $text=__($text);

        $button= get_whatsapp_option('whatsapp_button_text_select_service', 'Select Service');
        $button=__($button);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $text],
                'action' => [
                    'button' => $button,
                    'sections' => [
                        [
                            'title' => 'Available Services',
                            'rows' => $rows
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }

    public function sendServiceDetails($phone, $service)
    {
        $text = "Service Details\n\n";
        $text .= "Title: {$service['title']}\n";
        $text .= "Category: {$service['category']}\n";
        $text .= "Price: {$service['price']}\n";
        $text .= "Service Type: {$service['service_type']}\n";

        $included_excluded=get_whatsapp_option('whatsapp_button_text_included_excluded', 'Included-Excluded');
        $included_excluded=__($included_excluded);

       if($service['service_type'] === 'Online')
       {
           $show_faqs=get_whatsapp_option('whatsapp_button_text_show_faqs-benefits', 'Show FAQs-Benefits');
           $show_faqs=__($show_faqs);
       }else
       {
           $show_faqs=get_whatsapp_option('whatsapp_button_text_show_faqs', 'Show Benefits');
           $show_faqs=__($show_faqs);
       }

        $order_now=get_whatsapp_option('whatsapp_button_text_order_now', 'Order Now');
        $order_now=__($order_now);

        // WhatsApp interactive message with Order button
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "service_include_{$service['id']}",
                                'title' => $included_excluded,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "service_faqs_{$service['id']}",
                                'title' => $show_faqs,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "order_service_{$service['id']}",
                                'title' => $order_now,
                            ]
                        ],
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }

    public function sendServiceIncludes($phone, $service)
    {
        $text = "";
        if (!empty($service['included'])) {
            $text .= "Included:\n";
            foreach ($service['included'] as $item) {
                $text .= "- $item\n";
            }
            $text .= "\n";
        }
        if (!empty($service['excluded'])) {
            $text .= "Excluded:\n";
            foreach ($service['excluded'] as $item) {
                $text .= "- $item\n";
            }
            $text .= "\n";
        }

        if (empty($text)) {
            $text = "No included/excluded items found for this service.";
            $text=__($text);
        }

        $included_excluded=get_whatsapp_option('whatsapp_button_text_included_excluded', 'Included-Excluded');
        $included_excluded=__($included_excluded);

       if($service['service_type'] === 'Online')
       {
            $show_faqs=get_whatsapp_option('whatsapp_button_text_show_faqs-benefits', 'Show FAQs-Benefits');
            $show_faqs=__($show_faqs);
        }else
        {
            $show_faqs=get_whatsapp_option('whatsapp_button_text_show_faqs', 'Show Benefits');
            $show_faqs=__($show_faqs);
       }

        $order_now=get_whatsapp_option('whatsapp_button_text_order_now', 'Order Now');
        $order_now=__($order_now);

        // WhatsApp interactive message with Order button
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "service_include_{$service['service_id']}",
                                'title' => $included_excluded,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "service_faqs_{$service['service_id']}",
                                'title' => $show_faqs,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "order_service_{$service['service_id']}",
                                'title' => $order_now,
                            ]
                        ],
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }


    public function sendServiceFaqs($phone, $service)
    {
        $text = "";
        if (isset($service['faqs']) && !empty($service['faqs'])) {
            $text .= "Faqs:\n";
            foreach ($service['faqs'] as $item) {
                $title = mb_strimwidth($item['title'], 0, 30, '…');
                $desc = mb_strimwidth($item['description'], 0, 100, '…');
                $text .= "- $title: $desc\n";
            }
            $text .= "\n";
        }

        if(isset($service['benifits']) && !empty($service['benifits'])) {
            $text .= "Benefits:\n";
            foreach ($service['benifits'] as $item) {
                $title = mb_strimwidth($item['title'], 0, 30, '…');
                $text .= "- $title\n";
            }
            $text .= "\n";
        }

        if (empty($text)) {
            $text = "No FAQs found for this service.";
            $text=__($text);
        }

        $included_excluded=get_whatsapp_option('whatsapp_button_text_included_excluded', 'Included-Excluded');
        $included_excluded=__($included_excluded);

        if($service['service_type'] === 'Online') {
            $show_faqs = get_whatsapp_option('whatsapp_button_text_show_faqs-benefits', 'Show FAQs-Benefits');
            $show_faqs = __($show_faqs);
            }
        else
        {
            $show_faqs=get_whatsapp_option('whatsapp_button_text_show_faqs', 'Show Benefits');
            $show_faqs=__($show_faqs);
        }

        $order_now=get_whatsapp_option('whatsapp_button_text_order_now', 'Order Now');
        $order_now=__($order_now);

        // WhatsApp interactive message with Order button
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "service_include_{$service['service_id']}",
                                'title' => $included_excluded,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "service_faqs_{$service['service_id']}",
                                'title' => $show_faqs,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => "order_service_{$service['service_id']}",
                                'title' => $order_now,
                            ]
                        ],
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }




    public function sendServiceAddonList($to,$addons,$serviceId)
    {
        $rows = array_map(function ($addons) {
            return [
                'id' => "addon_{$addons['id']}",
                'title' => mb_strimwidth($addons['additional_service_title'], 0, 24, '…'),
                'description' => "Price: {$addons['additional_service_price']}"
            ];
        }, $addons);

        $rows[] = [
            'id' => "continue_{$serviceId}",
            'title' => 'Continue to Next step',
            'description' => 'Skip extra selection'
        ];

        $text=get_whatsapp_option('whatsapp_message_ask_addon_select', 'Please choose a extras:');
        $text=__($text);

        $button= get_whatsapp_option('whatsapp_button_text_select_addons', 'Select Extras');
        $button=__($button);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $text],
                'action' => [
                    'button' => $button,
                    'sections' => [
                        [
                            'title' => 'Available Extras',
                            'rows' => $rows
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }

    public function sendServiceIncludeFirstList($to,$includes,$serviceId)
    {
        $rows = array_map(function ($includes) {
            return [
                'id' => "include_{$includes['id']}",
                'title' => mb_strimwidth($includes['include_service_title'], 0, 24, '…'),
                'description' => "Price: {$includes['include_service_price']}"
            ];
        },$includes);


        $text=get_whatsapp_option('whatsapp_message_ask_include_select', 'Please choose a include service:');
        $text=__($text);

        $button= get_whatsapp_option('whatsapp_button_text_select_includes', 'Select Includes');
        $button=__($button);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $text],
                'action' => [
                    'button' => $button,
                    'sections' => [
                        [
                            'title' => 'Available Includes',
                            'rows' => $rows
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }
    public function sendServiceIncludeList($to,$includes,$serviceId)
    {
        $rows = array_map(function ($includes) {
            return [
                'id' => "include_{$includes['id']}",
                'title' => mb_strimwidth($includes['include_service_title'], 0, 24, '…'),
                'description' => "Price: {$includes['include_service_price']}"
            ];
        },$includes);

        $rows[] = [
            'id' => "continue_include_{$serviceId}",
            'title' => 'Continue to Next step',
            'description' => 'Skip include selection'
        ];

        $text=get_whatsapp_option('whatsapp_message_ask_include_select', 'Please choose a include service:');
        $text=__($text);

        $button= get_whatsapp_option('whatsapp_button_text_select_includes', 'Select Includes');
        $button=__($button);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $text],
                'action' => [
                    'button' => $button,
                    'sections' => [
                        [
                            'title' => 'Available Includes',
                            'rows' => $rows
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }

    public function sendServiceStaffList($to,$staffs,$serviceId)
    {
        $rows = array_map(function ($staff) {
            $fullname= $staff['first_name'] . ' ' . $staff['last_name'];
            return [
                'id' => "staff_{$staff['id']}",
                'title' => mb_strimwidth($fullname, 0, 24, '…'),
                'description' => $staff['email'] . ' | ' . $staff['phone'], // optional
            ];
        }, $staffs);


        $rows[] = [
            'id' => "skip_staff_{$serviceId}",
            'title' => 'Continue to Next step',
            'description' => 'Skip staff selection'
        ];
        $text=get_whatsapp_option('whatsapp_message_ask_select_staff', 'Please choose a staff:');
        $text=__($text);

        $button= get_whatsapp_option('whatsapp_button_text_select_staff', 'Select Staff');
        $button=__($button);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $text],
                'action' => [
                    'button' => $button,
                    'sections' => [
                        [
                            'title' => 'Available Staffs',
                            'rows' => $rows
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }

    public function sendAvailableSlots($phone,$slots)
    {

        if (empty($slots)) {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'body' => 'No slots available at the moment. Please try again later.'
                ]
            ];
        } else {
            $rows = array_map(function ($slot, $index) {
                return [
                    'id' => "slot_$index",
                    'title' => $slot,
                    'description' => 'Select this time slot'
                ];
            }, $slots, array_keys($slots));

            $text=get_whatsapp_option('whatsapp_message_ask_select_slot', 'Please choose a slot:');
            $text=__($text);

            $button= get_whatsapp_option('whatsapp_button_text_select_slot', 'Select Slot');
            $button=__($button);

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $phone,
                'type' => 'interactive',
                'interactive' => [
                    'type' => 'list',
                    'body' => ['text' => $text],
                    'action' => [
                        'button' => $button,
                        'sections' => [
                            [
                                'title' => 'Available Slots',
                                'rows' => $rows
                            ]
                        ]
                    ]
                ]
            ];
        }


        return $this->sendMessage($payload);
    }

//    public function sendLocationList($to,$locations,)
//    {
//        $rows = array_map(function ($location) {
//            return [
//                'id' => "location_{$location['id']}",
//                'title' => $location['title'],
//                'address' => $location['address'],
//
//            ];
//        }, $locations);
//
//        $payload = [
//            'to' => $to,
//            'type' => 'interactive',
//            'interactive' => [
//                'type' => 'list',
//                'body' => ['text' => 'Please choose a location:'],
//                'action' => [
//                    'button' => 'Select Address',
//                    'sections' => [
//                        [
//                            'title' => 'Available Addresses',
//                            'rows' => $rows
//                        ]
//                    ]
//                ]
//            ]
//        ];
//
//        return $this->sendMessage($payload);
//


    public function sendAddonQuantity($to, $addon)
    {
        $rows = [];

        // Generate quantity options from 1 to 10
        for ($i = 1; $i <= 10; $i++) {
            $rows[] = [
                'id' => "addon_qty_{$addon['id']}_$i",
                'title' => "Quantity-$i",
                'description' => "Select $i for {$addon['title']}"
            ];
        }

        $text=$addon['title'];
        $text .="\n\n";
        $text .= get_whatsapp_option('whatsapp_message_ask_select_addon_quantity', 'Please select quantity:');
        $text=__($text);

        $button=get_whatsapp_option('whatsapp_button_text_select_quantity', 'Select Quantity');
        $button=__($button);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $text],
                'action' => [
                    'button' => $button,
                    'sections' => [
                        [
                            'title' => 'Choose Quantity (1–10)',
                            'rows' => $rows
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }
    public function sendIncludeQuantity($to, $include)
    {
        $rows = [];

        // Generate quantity options from 1 to 10
        for ($i = 1; $i <= 10; $i++) {
            $rows[] = [
                'id' => "include_qty_{$include['id']}_$i",
                'title' => "Quantity-$i",
                'description' => "Select $i for {$include['title']}"
            ];
        }

        $text=$include['title'];
        $text .="\n\n";
        $text .= get_whatsapp_option('whatsapp_message_ask_select_include_quantity', 'Please select quantity:');
        $text=__($text);

        $button=get_whatsapp_option('whatsapp_button_text_select_include_quantity', 'Select Quantity');
        $button=__($button);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'list',
                'body' => ['text' => $text],
                'action' => [
                    'button' => $button,
                    'sections' => [
                        [
                            'title' => 'Choose Quantity (1–10)',
                            'rows' => $rows
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }



    public function sendWelcomeTemplate($phone)
    {
        $text= "Welcome! What would you like to do?";
        $text=__($text);

        $search_service=get_whatsapp_option('whatsapp_button_text_service_search', 'Search Service');
        $search_service=__($search_service);
        $order_details=get_whatsapp_option('whatsapp_button_text_order_details', 'View Recent Orders');
        $order_details=__($order_details);
        $talk_to_support=get_whatsapp_option('whatsapp_button_text_talk_to_support', 'Talk to Support');
        $talk_to_support=__($talk_to_support);


        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' =>$text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'search_service',
                                'title' => $search_service,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'order_details',
                                'title' => $order_details,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'talk_to_support',
                                'title' => $talk_to_support,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }


    public function sendConversationText($phone)
    {
        $text = get_whatsapp_option("whatsapp_message_help_message","How can we help you?");
        $text=__($text);

        $search_service = get_whatsapp_option('whatsapp_button_text_service_search', 'Search Service');
        $search_service = __($search_service);
        $order_details = get_whatsapp_option('whatsapp_button_text_view_recent_orders', 'View Recent Orders');
        $order_details = __($order_details);
        $talk_to_support =get_whatsapp_option('whatsapp_button_text_talk_to_support', 'Talk to Support');
        $talk_to_support = __($talk_to_support);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'search_service',
                                'title' => $search_service,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'order_details',
                                'title' => $order_details,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'talk_to_support',
                                'title' => $talk_to_support,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }


    public function sendCancelOrder($phone)
    {
        $text = get_whatsapp_option("whatsapp_message_cancel_confirmation","Do you want to cancel your order?");
        $text=__($text);
        $agrred_to_cancel =get_whatsapp_option('whatsapp_button_text_agree_to_cancel_order', 'Yes, Cancel Order');
        $agrred_to_cancel = __($agrred_to_cancel);
        $disagree_to_cancel = get_whatsapp_option('whatsapp_button_text_disagree_to_cancel_order', 'Do not cancel order');
        $disagree_to_cancel = __($disagree_to_cancel);
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'cancel_order_yes',
                                'title' => $agrred_to_cancel,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'cancel_order_no',
                                'title' => $disagree_to_cancel,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }
    public function sendOutsideWindowTemplate($phone)
    {
        $payload=[
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'template',
            'template' => [
                'name' => 'welcome__template',
                'language' => [
                    'code' => 'en',
                ],
            ],
        ];
        return $this->sendMessage($payload);

    }


    public function sendInvoice($phone,$order_id)
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'document',
            'document' => [
                'link' => asset("api/v1/client/order/invoice-details/{$order_id}"),
                'caption' => "Invoice for Order #{$order_id}",
                'filename' => "invoice_{$order_id}.pdf"
            ]
        ];

        return $this->sendMessage($payload);
    }

    public function sendConfirm($phone)
    {
        $text = get_whatsapp_option("whatsapp_message_confirm_order","Do you want to confirm your order?");
        $text=__($text);
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'confirm_order_yes',
                                'title' => 'Confirm Order',
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'confirm_order_no',
                                'title' => 'Cancel Order'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }


    public function sendOrderDetails($phone, $order_service_details)
    {
        $text = "Your Order Summary\n\n";
        $text .="Order Service Details :\n\n";
        $text .= "Service: {$order_service_details['service_title']}\n";
        foreach ($order_service_details['includes'] as $include) {
            $text .= "Include: {$include['title']} (Qty: {$include['quantity']}) (price: {$include['total']})\n";
        }
        $text=__($text);

        $order_addon_details = get_whatsapp_option('whatsapp_button_text_order_addon_details', 'Extras Details');
        $order_addon_details = __($order_addon_details);
        $order_other_details = get_whatsapp_option('whatsapp_button_text_order_other_details', 'Other Details');
        $order_other_details = __($order_other_details);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'order_addon_details',
                                'title' => $order_addon_details,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'order_other_details',
                                'title' => $order_other_details,
                            ]
                        ],

                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }
    public function sendOrderAddonDetails($phone,$result)
    {
       $text="Order Service Extras Details :\n\n";
        $text .= "Service: {$result['service_title']}\n";
        foreach ($result['addons'] as $addon) {
            $text .= "Extras: {$addon['title']} (Qty: {$addon['quantity']}) (price: {$addon['total']})\n";
        }

        $order_other_details =get_whatsapp_option('whatsapp_button_text_order_other_details', 'Other Details');
        $order_other_details = __($order_other_details);
        $confirm_order_yes = get_whatsapp_option('whatsapp_button_text_confirm_order', 'Confirm Order');
        $confirm_order_yes = __($confirm_order_yes);
        $confirm_order_no = get_whatsapp_option('whatsapp_button_text_cancel_order', 'Cancel Order');
        $confirm_order_no = __($confirm_order_no);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'order_other_details',
                                'title' => $order_other_details,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'confirm_order_yes',
                                'title' => $confirm_order_yes,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'confirm_order_no',
                                'title' => $confirm_order_no,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }
    public function sendOrderOtherDetails($phone,$result)
    {
        $text="Order Other Details :\n\n";
        if($result['service_type'] === 'Online')
        {
            $text .= "Extras Total: {$result['addon_total']}\n";
            $text .= "Sub Total: {$result['sub_total']}\n";
            $text .= "Tax: {$result['tax']}\n";
            $text .= "Total: {$result['total']}\n";
        }
        else
        {
            $text .= "Date: {$result['date']}\n";
            $text .= "Schedule: {$result['schedule']}\n";
            $text .= "Location: {$result['location']}\n";
            $text .= "Include Total: {$result['include_total']}\n";
            $text .= "Extras Total: {$result['addon_total']}\n";
            $text .= "Sub Total: {$result['sub_total']}\n";
            $text .= "Tax: {$result['tax']}\n";
            $text .= "Total: {$result['total']}\n";
        }

        $order_addon_details = get_whatsapp_option('whatsapp_button_text_order_addon_details', 'Extra Details');
        $order_addon_details = __($order_addon_details);
        $confirm_order_yes = get_whatsapp_option('whatsapp_button_text_confirm_order', 'Confirm Order');
        $confirm_order_yes = __($confirm_order_yes);
        $confirm_order_no = get_whatsapp_option('whatsapp_button_text_cancel_order', 'Cancel Order');
        $confirm_order_no = __($confirm_order_no);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'button',
                'body' => [
                    'text' => $text,
                ],
                'action' => [
                    'buttons' => [
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'order_addon_details',
                                'title' => $order_addon_details,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'confirm_order_yes',
                                'title' => $confirm_order_yes,
                            ]
                        ],
                        [
                            'type' => 'reply',
                            'reply' => [
                                'id' => 'confirm_order_no',
                                'title' => $confirm_order_no,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }


    public function sendAddressRequest($phone,$msg)
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $phone,
            'type' => 'interactive',
            'interactive' => [
                'type' => 'location_request_message',
                'body' => [
                    'text' => $msg,
                ],
                'action' => [
                    'name' => 'send_location',
                ]
            ]
        ];

        return $this->sendMessage($payload);
    }

    public function sendTemplate(string $to, string $templateName, array $components = [])
    {
        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => 'en_US',
                ],
                // Add components if variables are needed
                'components' => $components
            ]
        ];

        return $this->sendMessage($payload);
    }


    protected function sendMessage($data)
    {
        return Http::withToken($this->accessToken)
            ->post("https://graph.facebook.com/v19.0/{$this->phoneNumberId}/messages", $data)
            ->json();
    }

}
