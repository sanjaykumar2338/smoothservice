<?php
namespace App\Http\Controllers\MainClient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
USE App\Models\Order;
use App\Models\Service;
use App\Models\Country;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientWelcome;
use App\Models\ClientStatus;
use Illuminate\Support\Str;
use App\Mail\ClientPasswordChanged;
use Illuminate\Support\Facades\Auth;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\ClientReply;
use App\Models\TeamMember;
use App\Models\OrderStatus;
use App\Models\OrderProjectData;
use App\Models\Tag;
use App\Models\History;
use App\Models\TicketStatus;
use App\Models\TicketTeam;
use App\Models\OrderTeam;
use App\Models\Intakeform;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Subscription;
use Stripe\Customer;
use Stripe\Price;
use Stripe\Product;
use Stripe\PaymentMethod;
use App\Models\InvoiceSubscription;
use Carbon\Carbon;
use DB;
use GuzzleHttp\Client as GClient;
use App\Services\PayPalService;
use App\Models\UserFund;

class MainClientController extends Controller
{
    public $client_id;
    protected $payPalService;

    public function __construct(PayPalService $payPalService) {
        $this->client_id = getUserID();
        $this->payPalService = $payPalService;
    }

    public function dashboard()
    {
        $services = Service::where('user_id', $this->client_id)->count();
        $orders = Order::select('orders.status_id', 'order_statuses.name as status_name', DB::raw('COUNT(orders.id) as total_orders'))
            ->leftjoin('order_statuses', 'orders.status_id', '=', 'order_statuses.id')
            ->where('orders.client_id', $this->client_id)
            ->groupBy('orders.status_id', 'order_statuses.name')
            ->get();

        return view('c_main.c_pages.c_dashboard_page', compact('services', 'orders'));
    }

    public function orders(Request $request)
    {
        $search = $request->input('search'); // Get the search query from the request
        
        // Query with optional search on 'title' and 'note'
        $orders = Order::where('client_id', $this->client_id)
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('title', 'LIKE', '%' . $search . '%')
                            ->orWhere('note', 'LIKE', '%' . $search . '%');
                });
            })
            ->orderBy('orders.id', 'desc')
            ->paginate(10);
        
        return view('c_main.c_pages.c_order.c_index', compact('orders', 'search'));
    }
   
    public function show($id)
    {
        $order = Order::with(['client', 'teamMembers', 'service', 'tasks' => function($query) {
            $query->where('status', 0);
        }])->where('order_no', $id)->firstOrFail();

        $team_members = TeamMember::where('added_by', $order->user_id)->get();
        $orderStatus = OrderStatus::find($order->status_id);
        $project_data = OrderProjectData::where('order_id', $order->id)->get();
        $client_replies = ClientReply::where('order_id', $order->id)->get();
        $orderstatus = OrderStatus::where('added_by', $order->user_id)->get();
        $tags = Tag::where('added_by', $order->user_id)->get();
        $existingTags = \DB::table('tags')
            ->join('order_tag', 'tags.id', '=', 'order_tag.tag_id')
            ->select('tags.id', 'tags.name') // Specify the table name for the id
            ->where('order_tag.order_id', $order->id) // Replace $orderId with your variable
            ->get();
        
        $existingTagsName = \DB::table('tags')
            ->join('order_tag', 'tags.id', '=', 'order_tag.tag_id')
            ->select('tags.name') // Only select the name
            ->where('order_tag.order_id', $order->id)
            ->pluck('name') // Get the names as a collection
            ->implode(','); // Convert the collection to a comma-separated string
        

        $orderHistory = History::where('order_id', $order->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function($date) {
                return \Carbon\Carbon::parse($date->created_at)->format('Y-m-d'); // Group by date only
            });

        $teamMembers = TeamMember::with('role')->where('added_by', $order->user_id)->get();
        //echo "<pre>"; print_r($order->added_by); die;
       
        return view('c_main.c_pages.c_order.c_detail', compact('order','team_members','project_data','client_replies','orderHistory','orderstatus','orderStatus','tags','existingTags','existingTagsName','teamMembers'));
    }

    public function tickets(Request $request)
    {
        $search = $request->input('search'); // Get the search query from the request

        // Query tickets with optional search on 'subject', 'message', and 'note'
        $tickets = Ticket::with('ticket_status')
            ->where('client_id', $this->client_id)
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('subject', 'LIKE', '%' . $search . '%')
                            ->orWhere('message', 'LIKE', '%' . $search . '%')
                            ->orWhere('note', 'LIKE', '%' . $search . '%');
                });
            })
            ->paginate(5);

        return view('c_main.c_pages.c_ticket.c_index', compact('tickets', 'search'));
    }

    public function ticket_add(Request $request)
    {
        $orders = Order::where('client_id', $this->client_id)->get();
        return view('c_main.c_pages.c_ticket.c_add', compact('orders'));
    }

    public function ticket_show($id)
    {
        $ticket = Ticket::with('ticket_status')->where('ticket_no', $id)->first();
        $team_members = TeamMember::where('added_by', $ticket->user_id)
            ->whereIn('role_id', [1, 2])
            ->get();

        return view('c_main.c_pages.c_ticket.c_show', compact('ticket','team_members'));
    }

    public function ticket_store(Request $request){
        
        try{
            $validatedData = $request->validate([
                'subject' => 'required',
                'order' => 'nullable',
                'editor_content' => 'required'
            ], [
                'editor_content.required' => 'The message field is required.',
            ]);            

            $statues = TicketStatus::where('is_default', 1)->first();
            $status_id = $statues ? $statues->id : null;
            $client = Client::where('id', $this->client_id)->first();

            $ticket = new Ticket;
            $ticket->subject = $request->subject;
            $ticket->order_id = $request->order;
            $ticket->client_id = $this->client_id;
            $ticket->user_id = $client->added_by;
            $ticket->message = $request->editor_content;
            $ticket->status_id = $status_id;
            $ticket->ticket_no = $this->generateTicketNumber();
            $ticket->save();

            return redirect()->route('portal.tickets')->with('success', 'Ticket created successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function generateTicketNumber($length = 6) {
        // Characters allowed: numbers and uppercase letters
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $ticketNumber = '';
    
        // Generate a random string of the given length
        for ($i = 0; $i < $length; $i++) {
            $ticketNumber .= $characters[rand(0, strlen($characters) - 1)];
        }
    
        return $ticketNumber;
    }

    // Save assigned team members to the ticket
    public function saveTeamMembers(Request $request)
    {
        $ticketId = $request->input('ticket_id');
        $teamMemberIds = $request->input('team_member_ids');

        // First, remove any existing team members for this ticket
        TicketTeam::where('ticket_id', $ticketId)->delete();

        // Now, insert the new selected team members
        if(!is_null($teamMemberIds)){
            foreach ($teamMemberIds as $teamMemberId) {
                TicketTeam::create([
                    'ticket_id' => $ticketId,
                    'team_member_id' => $teamMemberId,
                ]);
            }
        }

        return response()->json(['success' => 'Team members saved successfully!']);
    }

    public function saveTeamMembersOrder(Request $request)
    {
        // First, remove any existing team members for this order
        $orderId = $request->input('order_id');
        $teamMemberIds = $request->input('team_member_ids');

        OrderTeam::where('order_id', $orderId)->delete();

        // Now, insert the new selected team members
        if(!is_null($teamMemberIds)){
            foreach ($teamMemberIds as $teamMemberId) {
                OrderTeam::create([
                    'order_id' => $orderId,
                    'team_member_id' => $teamMemberId,
                ]);
            }
        }

        return response()->json(['success' => 'Team members saved successfully!']);
    }

    public function invoices(Request $request)
    {
        $search = $request->input('search'); // Get the search query from the request
        
        // Query with optional search on 'title' and 'note'
        $invoices = Invoice::with(['client', 'service', 'items'])->where('client_id', $this->client_id)
            ->when($search, function ($query, $search) {
                return $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('billing_first_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('billing_last_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('note', 'LIKE', '%' . $search . '%');
                });
            })->orderBy('created_at','desc')
            ->paginate(10);
        
        
        //echo "<pre>"; print_r($invoices); die;
        return view('c_main.c_pages.c_invoice.c_index', compact('invoices', 'search'));
    }

    public function invoice_show($id)
    {
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);

        $services = Service::where('user_id', auth()->id())->get();
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', auth()->id())->get();

        $nextPaymentRecurring = 0;
        $totalDiscount = 0;
        $intervalTotal = [];
        $trialServices = [];
        $nonTrialServices = [];
        $upfrontPayment = $invoice->upfront_payment_amount > 0 ? $invoice->upfront_payment_amount : 0;
        $currency = $invoice->currency;
        $firstServiceType = null;
        $intake_forms = [];

        $user = auth()->user();

        $order_no_base = strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
        $globalOrderIndex = 1;
        $invoice_service_ids = $invoice->items->pluck('service_id')->map(fn($id) => (string) $id)->toArray();

        foreach ($invoice->items as $item) {
            $originalService = $item->service;
            $parentService = null;

            if (!empty($originalService->parent_services)) {
                $parentIds = array_map('trim', explode(',', $originalService->parent_services));
                foreach ($parentIds as $pid) {
                    if (in_array($pid, $invoice_service_ids)) {
                        $parentService = Service::find($pid);
                        break;
                    }
                }
            }

            $orderService = $parentService ?: $originalService;

            $existingOrder = Order::where('invoice_id', $invoice->id)
                ->where('add_on_service', $originalService->id)
                ->first();

            if (!$existingOrder && $invoice->paid_at) {
                $user = getAuthenticatedUser();
                $multipleOrders = (int) ($orderService->multiple_orders ?? 0);
                $quantity = (int) ($item->quantity ?? 1);
                $groupMultiple = (int) ($orderService->group_multiple ?? 0);

                if ($multipleOrders > 0) {
                    $order_count = $multipleOrders * $quantity;

                    for ($i = 1; $i <= $order_count; $i++) {
                        $order_no = "{$order_no_base}_{$globalOrderIndex}";

                        $order = Order::create([
                            'title' => $item->item_name ?? $orderService->service_name,
                            'client_id' => $invoice->client_id,
                            'invoice_id' => $invoice->id,
                            'user_id' => $invoice->added_by,
                            'service_id' => $orderService->id,
                            'add_on_service' => $originalService->id,
                            'order_date' => now(),
                            'note' => 'Auto-created from invoice #' . $invoice->invoice_no,
                            'order_no' => $order_no,
                            'price' => $item->price,
                            'date_started' => now(),
                            'date_due' => ($orderService->set_a_deadline && $orderService->set_a_deadline_duration)
                                ? match (strtolower($orderService->set_a_deadline_duration)) {
                                    'days' => now()->addDays((int) $orderService->set_a_deadline),
                                    'hours' => now()->addHours((int) $orderService->set_a_deadline),
                                    'minutes' => now()->addMinutes((int) $orderService->set_a_deadline),
                                    default => null,
                                }
                                : null,
                        ]);

                        History::create([
                            'order_id' => $order->id,
                            'user_id' => $orderService->user_id ?? $user->id,
                            'action_type' => 'order_created',
                            'action_details' => 'Order auto-created for service: ' . ($item->item_name ?? $orderService->service_name),
                        ]);

                        $globalOrderIndex++;
                    }
                } elseif ($groupMultiple === 1) {
                    $title = $quantity . ' x ' . ($item->item_name ?? $orderService->service_name);

                    // Only drop index if this is the only invoice item
                    if ($invoice->items->count() === 1) {
                        $order_no = $order_no_base;
                    } else {
                        $order_no = "{$order_no_base}_{$globalOrderIndex}";
                    }

                    $order = Order::create([
                        'title' => $title,
                        'client_id' => $invoice->client_id,
                        'invoice_id' => $invoice->id,
                        'user_id' => $invoice->added_by,
                        'service_id' => $orderService->id,
                        'add_on_service' => $originalService->id,
                        'order_date' => now(),
                        'note' => 'Auto-created from invoice #' . $invoice->invoice_no,
                        'order_no' => $order_no,
                        'price' => $item->price,
                        'date_started' => now(),
                        'date_due' => ($orderService->set_a_deadline && $orderService->set_a_deadline_duration)
                            ? match (strtolower($orderService->set_a_deadline_duration)) {
                                'days' => now()->addDays((int) $orderService->set_a_deadline),
                                'hours' => now()->addHours((int) $orderService->set_a_deadline),
                                'minutes' => now()->addMinutes((int) $orderService->set_a_deadline),
                                default => null,
                            }
                            : null,
                    ]);


                    History::create([
                        'order_id' => $order->id,
                        'user_id' => $orderService->user_id ?? $user->id,
                        'action_type' => 'order_created',
                        'action_details' => 'Grouped order auto-created for service: ' . $title,
                    ]);

                    $globalOrderIndex++;
                } else {
                    $order_no = "{$order_no_base}_{$globalOrderIndex}";

                    $order = Order::create([
                        'title' => $item->item_name ?? $orderService->service_name,
                        'client_id' => $invoice->client_id,
                        'invoice_id' => $invoice->id,
                        'user_id' => $invoice->added_by,
                        'service_id' => $orderService->id,
                        'add_on_service' => $originalService->id,
                        'order_date' => now(),
                        'note' => 'Auto-created from invoice #' . $invoice->invoice_no,
                        'order_no' => $order_no,
                        'price' => $item->price,
                        'date_started' => now(),
                        'date_due' => ($orderService->set_a_deadline && $orderService->set_a_deadline_duration)
                            ? match (strtolower($orderService->set_a_deadline_duration)) {
                                'days' => now()->addDays((int) $orderService->set_a_deadline),
                                'hours' => now()->addHours((int) $orderService->set_a_deadline),
                                'minutes' => now()->addMinutes((int) $orderService->set_a_deadline),
                                default => null,
                            }
                            : null,

                    ]);

                    History::create([
                        'order_id' => $order->id,
                        'user_id' => $orderService->user_id ?? $user->id,
                        'action_type' => 'order_created',
                        'action_details' => 'Single order auto-created for service: ' . ($item->item_name ?? $orderService->service_name),
                    ]);

                    $globalOrderIndex++;
                }
            }

            if ($originalService?->intake_form) {
                $intake_forms[] = Intakeform::find($originalService->intake_form);
            }

            if (!$firstServiceType && $originalService?->service_type == 'recurring') {
                $firstServiceType = $originalService->recurring_service_currency_value_two_type ?? '';
            }

            if (!empty($originalService->trial_for)) {
                $trialServices[] = [
                    'name' => $originalService->service_name ?? $item->item_name,
                    'trialPrice' => ($originalService->trial_price - $item->discount),
                    'trialDuration' => $originalService->trial_for . ' ' . ($originalService->trial_for > 1 ? $originalService->trial_period . 's' : $originalService->trial_period),
                    'nextPrice' => ($originalService->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment,
                    'interval' => $originalService->recurring_service_currency_value_two . ' ' . ($originalService->recurring_service_currency_value_two > 1 ? $originalService->recurring_service_currency_value_two_type . 's' : $originalService->recurring_service_currency_value_two_type),
                ];

                $nextPaymentRecurring += ($originalService->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                $intervalTotal[] = $originalService->trial_for;
            } else {
                if ($originalService) {
                    $price = ($originalService->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                    $nonTrialServices[] = [
                        'name' => $originalService->service_name ?? $item->item_name,
                        'price' => $originalService->recurring_service_currency_value,
                        'quantity' => $item->quantity,
                        'interval' => $originalService->recurring_service_currency_value_two . ' ' . ($originalService->recurring_service_currency_value_two > 1 ? $originalService->recurring_service_currency_value_two_type . 's' : $originalService->recurring_service_currency_value_two_type),
                    ];

                    $nextPaymentRecurring += $price;
                    $totalDiscount += $item->discount;
                    $intervalTotal[] = $originalService->recurring_service_currency_value_two;
                }
            }
        }

        $nextTotalWithoutTrial = array_sum(array_column($nonTrialServices, 'price')) * ($item->quantity ?? 1);

        $main_data = [
            'currency' => $currency,
            'trialServices' => $trialServices,
            'nonTrialServices' => $nonTrialServices,
            'nextPaymentRecurring' => $nextPaymentRecurring,
            'totalDiscount' => $totalDiscount,
            'upfrontPayment' => $upfrontPayment,
            'firstServiceType' => $firstServiceType
        ];

        if ($invoice->landing_page_open == 0) {
            Invoice::where('id', $invoice->id)->update(['landing_page_open' => 1]);
            $landing_page = \App\Models\LandingPage::find($invoice->landing_page);

            if ($landing_page && $landing_page->intake_form && $invoice) {
                $intake_form = Intakeform::where('id', $landing_page->intake_form)
                    ->where('form_fields', '<>', '')
                    ->first();

                if ($intake_form) {
                    return redirect()->route('intakeform', [
                        'id' => $invoice->landing_page,
                        'invoice' => $invoice->invoice_no
                    ]);
                }
            }
        }

        $invoice_items = InvoiceItem::where('invoice_id', $invoice->id)->get();

        return view('c_main.c_pages.c_invoice.c_show', compact(
            'invoice',
            'services',
            'users',
            'teamMembers',
            'main_data',
            'intake_forms',
            'invoice_items'
        ));
    }

    public function invoice_payment($id)
    {
        // Retrieve the invoice by its ID along with the associated client and items
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);
        if ($invoice->paid_at) {
            return redirect()->route('portal.invoices.show',$invoice->id)->with('error', 'Invoice already paid.');
        }

        // Retrieve the services in case you want to display service information in the invoice
        $services = Service::where('user_id', auth()->id())->get();

        // Fetch all users and team members added by the current logged-in user
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', auth()->id())->get();
        $addedByUser = User::findOrFail($invoice->added_by);

        // Initialize variables
        $nextPaymentRecurring = 0;
        $totalDiscount = 0;
        $intervalTotal = [];
        $trialServices = [];
        $nonTrialServices = [];
        $upfrontPayment = $invoice->upfront_payment_amount > 0 ? $invoice->upfront_payment_amount : 0;
        $currency = $invoice->currency;
        $firstServiceType = null;

        foreach ($invoice->items as $item) {
            $service = $item->service;

            // Determine the first service type (month, day, year, or week)
            if (!$firstServiceType && $service->service_type == 'recurring') {
                $firstServiceType = $service->recurring_service_currency_value_two_type ?? '';
            }

            if (!empty($service->trial_for)) {
                // Trial-based services
                $trialServices[] = [
                    'name' => $service->service_name ?? $item->item_name,
                    'trialPrice' => ($service->trial_price - $item->discount),
                    'trialDuration' => $service->trial_for . ' ' . ($service->trial_for > 1 ? $service->trial_period . 's' : $service->trial_period),
                    'nextPrice' => ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment,
                    'interval' => $service->recurring_service_currency_value_two . ' ' . ($service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type)
                ];

                $nextPaymentRecurring += ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                $intervalTotal[] = $service->trial_for;
            } else {
                // Non-trial services
                $price = ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                $nonTrialServices[] = [
                    'name' => $service->service_name ?? $item->item_name,
                    'price' => $service->recurring_service_currency_value,
                    'quantity' => $item->quantity,
                    'interval' => $service->recurring_service_currency_value_two . ' ' . ($service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type)
                ];

                $nextPaymentRecurring += $price;
                $totalDiscount += $item->discount;
                $intervalTotal[] = $service->recurring_service_currency_value_two;
            }
        }

        // Calculate totals
        $nextTotalWithoutTrial = array_sum(array_column($nonTrialServices, 'price')) * $item->quantity;

        // Summary data
        $main_data = [
            'currency' => $currency,
            'trialServices' => $trialServices,
            'nonTrialServices' => $nonTrialServices,
            'nextPaymentRecurring' => $nextPaymentRecurring,
            'totalDiscount' => $totalDiscount,
            'upfrontPayment' => $upfrontPayment,
            'firstServiceType' => $firstServiceType
        ];

        $summary = [
            'total' => 0,
            'trial_amount' => 0,
            'next_payment_recurring' => 0,
            'total_discount' => 0,
            'payment_type' => 'fixed', // Default to 'fixed'
            'interval' => 1, // Default interval is 1
            'interval_type' => 'month', // Default interval type
        ];

        foreach ($invoice->items as $key => $item) {
            $service = $item->service;

            // Calculate totals and discounts
            $itemTotal = ($item->price * $item->quantity) - ($item->discount * $item->quantity);
            $summary['total'] += $itemTotal;
            $summary['total_discount'] += $item->discount * $item->quantity;

            // Trial-based services
            if (!empty($service->trial_for)) {
                $trialPrice = $service->trial_price - $item->discount;
                $summary['trial_amount'] += $trialPrice;
                $summary['next_payment_recurring'] += ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment * $item->quantity;
                $summary['payment_type'] = 'recurring';
                $summary['interval'] = $service->recurring_service_currency_value_two ?? 1;
                $summary['interval_type'] = strtolower($service->recurring_service_currency_value_two_type ?? 'month');
            } elseif ($service->service_type == 'recurring') {
                // Recurring services without trial
                $summary['next_payment_recurring'] += ($service->recurring_service_currency_value * $item->quantity) - $item->discount * $item->quantity;
                $summary['payment_type'] = 'recurring';
                $summary['interval'] = $service->recurring_service_currency_value_two ?? 1;
                $summary['interval_type'] = strtolower($service->recurring_service_currency_value_two_type ?? 'month');
            }
        }

        // Adjust totals based on upfront payments
        if ($invoice->upfront_payment_amount > 0) {
            $summary['total'] -= $invoice->upfront_payment_amount;
        }

        //Pass the invoice data to the view
        return view('c_main.c_pages.c_invoice.c_payment', compact('invoice', 'services', 'users', 'teamMembers', 'addedByUser', 'main_data'));
    }

    public function invoice_payment_paypal($id)
    {
        // Retrieve the invoice by its ID along with the associated client and items
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);

        if ($invoice->paid_at) {
            return redirect()->route('portal.invoices.show',$invoice->id)->with('error', 'Invoice already paid.');
        }

        // Retrieve the services in case you want to display service information in the invoice
        $services = Service::where('user_id', auth()->id())->get();

        // Fetch all users and team members added by the current logged-in user
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', auth()->id())->get();
        $addedByUser = User::findOrFail($invoice->added_by);

        // Initialize variables
        $nextPaymentRecurring = 0;
        $totalDiscount = 0;
        $intervalTotal = [];
        $trialServices = [];
        $nonTrialServices = [];
        $upfrontPayment = $invoice->upfront_payment_amount > 0 ? $invoice->upfront_payment_amount : 0;
        $currency = $invoice->currency;
        $firstServiceType = null;

        foreach ($invoice->items as $item) {
            $service = $item->service;

            // Determine the first service type (month, day, year, or week)
            if (!$firstServiceType && $service->service_type == 'recurring') {
                $firstServiceType = $service->recurring_service_currency_value_two_type ?? '';
            }

            if (!empty($service->trial_for)) {
                // Trial-based services
                $trialServices[] = [
                    'name' => $service->service_name ?? $item->item_name,
                    'trialPrice' => ($service->trial_price - $item->discount),
                    'trialDuration' => $service->trial_for . ' ' . ($service->trial_for > 1 ? $service->trial_period . 's' : $service->trial_period),
                    'nextPrice' => ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment,
                    'interval' => $service->recurring_service_currency_value_two . ' ' . ($service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type)
                ];

                $nextPaymentRecurring += ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                $intervalTotal[] = $service->trial_for;
            } else {
                // Non-trial services
                $price = ($service->recurring_service_currency_value * $item->quantity) - $item->discountsnextpayment;
                $nonTrialServices[] = [
                    'name' => $service->service_name ?? $item->item_name,
                    'price' => $service->recurring_service_currency_value,
                    'quantity' => $item->quantity,
                    'interval' => $service->recurring_service_currency_value_two . ' ' . ($service->recurring_service_currency_value_two > 1 ? $service->recurring_service_currency_value_two_type . 's' : $service->recurring_service_currency_value_two_type)
                ];

                $nextPaymentRecurring += $price;
                $totalDiscount += $item->discount;
                $intervalTotal[] = $service->recurring_service_currency_value_two;
            }
        }

        // Calculate totals
        $nextTotalWithoutTrial = array_sum(array_column($nonTrialServices, 'price')) * $item->quantity;

        // Summary data
        $main_data = [
            'currency' => $currency,
            'trialServices' => $trialServices,
            'nonTrialServices' => $nonTrialServices,
            'nextPaymentRecurring' => $nextPaymentRecurring,
            'totalDiscount' => $totalDiscount,
            'upfrontPayment' => $upfrontPayment,
            'firstServiceType' => $firstServiceType
        ];

        // Pass the invoice data to the view
        return view('c_main.c_pages.c_invoice.c_paypal_payment', compact('invoice', 'services', 'users', 'teamMembers', 'addedByUser', 'main_data'));
    }

    //this is the original method for stripe payment
    public function processRecurringPayment(Request $request, $id)
    {
        try {
            $invoice = Invoice::with(['client', 'items'])->findOrFail($id);
            $client = Client::findOrFail($invoice->client_id);
            $addedByUser = User::findOrFail($invoice->added_by);

            if ($invoice->paid_at) {
                return response()->json(['success' => false, 'message' => 'Invoice already paid.'], 400);
            }

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            // Validate input
            $paymentMethodId = $request->input('payment_method');
            if (!$paymentMethodId) {
                return response()->json(['success' => false, 'message' => 'Payment method is required.'], 400);
            }

            if (empty($client->billing_address)) {
                return response()->json(['success' => false, 'message' => 'Billing address is required for export transactions.'], 400);
            }

            // Retrieve or create a customer on the connected account
            $stripeCustomer = null;
            if (empty($client->stripe_customer_id)) {
                // Create a new customer if not already exists
                $stripeCustomer = $stripe->customers->create(
                    [
                        'email' => $client->email,
                        'name' => $client->first_name . ' ' . $client->last_name,
                        'address' => [
                            'line1' => $client->billing_address ?? null,
                            'line2' => $client->billing_address ?? null,
                            'city' => $client->city ?? null,
                            'state' => $client->state ?? null,
                            'postal_code' => $client->postal_code ?? null,
                            'country' => $client->country ?? null,
                        ],
                    ],
                    ['stripe_account' => $addedByUser->stripe_connect_account_id]
                );
                // Save the Stripe customer ID to the client
                $client->update(['stripe_customer_id' => $stripeCustomer->id]);
            } else {
                try {
                    // Retrieve the existing customer
                    $stripeCustomer = $stripe->customers->retrieve(
                        $client->stripe_customer_id,
                        [],
                        ['stripe_account' => $addedByUser->stripe_connect_account_id]
                    );

                    // Update the customer details if they differ
                    $stripe->customers->update(
                        $client->stripe_customer_id,
                        [
                            'email' => $client->email,
                            'name' => $client->first_name . ' ' . $client->last_name,
                            'address' => [
                                'line1' => $client->billing_address ?? null,
                                'line2' => $client->billing_address ?? null,
                                'city' => $client->city ?? null,
                                'state' => $client->state ?? null,
                                'postal_code' => $client->postal_code ?? null,
                                'country' => $client->country ?? null,
                            ],
                        ],
                        ['stripe_account' => $addedByUser->stripe_connect_account_id]
                    );
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // Handle the case where the customer ID is invalid or not found
                    $stripeCustomer = $stripe->customers->create(
                        [
                            'email' => $client->email,
                            'name' => $client->first_name . ' ' . $client->last_name,
                            'address' => [
                                'line1' => $client->billing_address ?? null,
                                'line2' => $client->billing_address ?? null,
                                'city' => $client->city ?? null,
                                'state' => $client->state ?? null,
                                'postal_code' => $client->postal_code ?? null,
                                'country' => $client->country ?? null,
                            ],
                        ],
                        ['stripe_account' => $addedByUser->stripe_connect_account_id]
                    );
                    // Update the client record with the new Stripe customer ID
                    $client->update(['stripe_customer_id' => $stripeCustomer->id]);
                }
            }

            // Attach the payment method to the customer in the connected account
            $stripe->paymentMethods->attach(
                $paymentMethodId,
                ['customer' => $stripeCustomer->id],
                ['stripe_account' => $addedByUser->stripe_connect_account_id]
            );

            // Update the default payment method for the customer
            $stripe->customers->update(
                $stripeCustomer->id,
                [
                    'invoice_settings' => ['default_payment_method' => $paymentMethodId],
                ],
                ['stripe_account' => $addedByUser->stripe_connect_account_id]
            );

            // Create a product and price for the subscription
            $product = $stripe->products->create(
                [
                    'name' => 'Recurring Payment for Invoice #' . $invoice->invoice_no,
                ],
                ['stripe_account' => $addedByUser->stripe_connect_account_id]
            );

            $summary = invoiceSummary($invoice);
            //echo "<pre>"; print_r($summary); die;

            $price = $stripe->prices->create(
                [
                    'unit_amount' => $summary['next_payment_recurring'] * 100,
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => $summary['interval_type'],
                        'interval_count' => $summary['interval'],
                    ],
                    'product' => $product->id,
                ],
                ['stripe_account' => $addedByUser->stripe_connect_account_id]
            );

            // Determine the start date for the subscription
            $billingDate = $invoice->billing_date 
            ? Carbon::parse($invoice->billing_date)->startOfDay() 
            : now()->addDay(); // Default to tomorrow if no billing_date is set

            if ($billingDate->isPast()) {
                $billingDate = now()->addDay();
            }


            $inv = $this->payPalService->getPaymentType($invoice->id);

            // Determine interval settings for Stripe
            $intervalUnit = strtolower($summary['interval_type']); // 'month', 'week', etc.
            $intervalCount = (int)$summary['interval']; // 1, 2, etc.

            // Prepare subscription data
            $subscriptionData = [
                'customer' => $stripeCustomer->id,
                'items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1, // Adjust quantity as needed
                    ]
                ],
                'expand' => ['latest_invoice.payment_intent'],
                'billing_cycle_anchor' => $billingDate->timestamp,
                'metadata' => [
                    'invoice_id' => $invoice->invoice_no,
                    'payment_type' => $inv['payment_type'], // 'recurring'
                    'interval_text' => $summary['interval_type'], // 'month', 'week'
                    'interval' => $summary['interval'], // '1', '2'
                    'total_amount' => $summary['total'], // Total amount for reference
                    'recurring_payment' => $summary['next_payment_recurring'], // Recurring charge
                ],
            ];

            // Add upfront charge if total_amount > 0
            if ($summary['total'] > 0) {
                $subscriptionData['add_invoice_items'] = [
                    [
                        'price_data' => [
                            'currency' => 'usd', // Replace with your currency
                            'product' => $product->id, // Reference the product ID
                            'unit_amount' => $summary['total'] * 100, // Stripe expects amount in cents
                        ],
                        'quantity' => 1, // Specify the quantity
                    ]
                ];
            }

            // Create the subscription
            $subscription = $stripe->subscriptions->create(
                $subscriptionData,
                ['stripe_account' => $addedByUser->stripe_connect_account_id]
            );


            // Save subscription details
            $ins = InvoiceSubscription::create([
                'invoice_id' => $invoice->id,
                'subscription_id' => $subscription->id,
                'amount' => $summary['total'] ? $summary['total'] : $summary['next_payment_recurring'],
                'currency' => 'usd',
                'intervel' => $summary['interval_type'],
                'starts_at' => now(),
                'ends_at' => now()->addMonths($request->input('interval_count', 1)),
            ]);

            // Handle 3D Secure (SCA)
            if ($subscription->latest_invoice->payment_intent->status === 'requires_action') {
                return response()->json([
                    'requires_action' => true,
                    'client_secret' => $subscription->latest_invoice->payment_intent->client_secret,
                    'subscription_id' => $subscription->id, // Include subscription ID
                    'stored_subscrption_id' => $ins->id,
                ]);
            }

            $ins->update([
                'completed' => 1
            ]);

            $invoice->update([
                'paid_at' => now(),
                'payment_method' => 'stripe_subscription',
            ]);

            Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
            Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));

            return response()->json(['success' => true, 'message' => 'Recurring payment created successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    //this method used after subscription is created after finalize
    public function finalizeSubscription(Request $request)
    {
        try {
            $subscriptionId = $request->input('subscription_id');
            $invoiceId = $request->input('invoice_id');
            $stored_subscrption_id = $request->input('stored_subscrption_id');

            // Fetch invoice, client, and user
            $invoice = Invoice::with(['client', 'items'])->findOrFail($invoiceId);
            $client = Client::findOrFail($invoice->client_id);
            $addedByUser = User::findOrFail($invoice->added_by);
            $user = auth()->user();

            // Mark subscription as complete
            $sub = InvoiceSubscription::find($stored_subscrption_id);
            $sub->update(['completed' => 1]);

            // Update invoice as paid
            $invoice->update([
                'paid_at' => now(),
                'payment_method' => 'stripe_subscription',
            ]);

            // Send email notifications
            Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
            Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));

            return response()->json(['success' => true, 'message' => 'Subscription finalized and orders created.']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ]);
        }
    }

    public function processOneTimePayment(Request $request, $id)
    {
        try {
            $invoice = Invoice::with(['client'])->findOrFail($id);
            $client = Client::findOrFail($invoice->client_id);
            $addedByUser = User::findOrFail($invoice->added_by);

            if ($invoice->paid_at) {
                return response()->json(['success' => false, 'message' => 'Invoice already paid.'], 400);
            }

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            // Validate input
            $paymentMethodId = $request->input('payment_method');
            if (!$paymentMethodId) {
                return response()->json(['success' => false, 'message' => 'Payment method is required.'], 400);
            }

            if (empty($client->billing_address)) {
                return response()->json(['success' => false, 'message' => 'Billing address is required for export transactions.'], 400);
            }

            // Retrieve or create a Stripe customer
            if (empty($client->stripe_customer_id)) {
                $stripeCustomer = $stripe->customers->create(
                    [
                        'email' => $client->email,
                        'name' => $client->first_name . ' ' . $client->last_name,
                        'address' => [
                            'line1' => $client->billing_address ?? null,
                            'line2' => $client->billing_address ?? null,
                            'city' => $client->city ?? null,
                            'state' => $client->state ?? null,
                            'postal_code' => $client->postal_code ?? null,
                            'country' => $client->country ?? null,
                        ],
                    ],
                    ['stripe_account' => $addedByUser->stripe_connect_account_id]
                );
                $client->update(['stripe_customer_id' => $stripeCustomer->id]);
            } else {
                $stripeCustomer = $stripe->customers->retrieve(
                    $client->stripe_customer_id,
                    [],
                    ['stripe_account' => $addedByUser->stripe_connect_account_id]
                );
            }

            // Attach the payment method to the customer
            $stripe->paymentMethods->attach(
                $paymentMethodId,
                ['customer' => $stripeCustomer->id],
                ['stripe_account' => $addedByUser->stripe_connect_account_id]
            );

            $summary = invoiceSummary($invoice);

            // Create a PaymentIntent
            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => $summary['total'] * 100, // Amount in cents
                'currency' => 'usd',
                'customer' => $stripeCustomer->id,
                'payment_method' => $paymentMethodId,
                'description' => 'Payment for Invoice #' . $invoice->invoice_no,
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ], ['stripe_account' => $addedByUser->stripe_connect_account_id]);            

            if ($paymentIntent->status === 'requires_action') {
                return response()->json([
                    'requires_action' => true,
                    'client_secret' => $paymentIntent->client_secret,
                ]);
            }

            if ($paymentIntent->status === 'succeeded') {
                $invoice->update([
                    'paid_at' => now(),
                    'payment_method' => 'stripe_one_time',
                ]);

                Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
                Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));
                return response()->json(['success' => true, 'message' => 'Payment completed successfully.'], 200);
            }

            return response()->json(['success' => false, 'message' => 'Payment failed.'], 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function paymentonetimecompleted($id, Request $request){
        $invoice = Invoice::with(['client'])->findOrFail($id);
        $client = Client::findOrFail($invoice->client_id);
        $addedByUser = User::findOrFail($invoice->added_by);

        if ($invoice->paid_at === Null) {
            $invoice->update([
                'paid_at' => now(),
                'payment_method' => 'stripe_one_time',
            ]);

            Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
            Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));
            return redirect()->route('portal.invoices.show', $id)
                            ->with('success', 'Payment completed successfully.');
        }

        return redirect()->route('portal.invoices.show', $id)
                            ->with('success', 'Payment already completed.');
    }

    public function handleReturn(Request $request)
    {
        $paymentIntentId = $request->input('payment_intent');
        $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

        if ($paymentIntent->status === 'succeeded') {
            // Payment succeeded
            return redirect()->route('portal.invoices.show', $invoiceId)
                            ->with('success', 'Payment completed successfully.');
        } else {
            // Payment failed
            return redirect()->route('portal.invoices.show', $invoiceId)
                            ->with('error', 'Payment failed. Please try again.');
        }
    }

    public function createPaymentIntent(Request $request, Invoice $invoice)
    {
        // Ensure the authenticated user can access the invoice
        if ($invoice->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Set your Stripe secret key
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Create a PaymentIntent
        $paymentIntent = PaymentIntent::create([
            'amount' => $invoice->total * 100, // Amount in cents
            'currency' => 'usd',
            'metadata' => ['invoice_id' => $invoice->id],
        ]);

        return response()->json(['clientSecret' => $paymentIntent->client_secret]);
    }

    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method' => 'required|string',
            'recurring_payment' => 'nullable|numeric|min:0',
            'interval' => 'nullable|numeric',
        ]);

        try {
            $invoice = Invoice::findOrFail($id);
            $client = Client::findOrFail($invoice->client_id);
            $addedByUser = User::findOrFail($invoice->added_by);

            if ($invoice->paid_at) {
                return redirect()->route('portal.invoices.show', $invoice->id)->with('error', 'Invoice already paid.');
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));

            // Process Stripe payment logic as shown earlier...

            // Mark the invoice as paid
            $invoice->update([
                'paid_at' => now(),
                'payment_method' => 'stripe',
            ]);

            // Send email notifications
            Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
            Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));

            // Redirect to the invoice page with a success message
            return redirect()->route('portal.invoices.show', $invoice->id)
                            ->with('success', 'Payment successful. Invoice has been paid.');
        } catch (\Exception $e) {
            // Redirect back with an error message
            return redirect()->route('portal.invoices.show', $id)
                            ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function processPaymentOld(Request $request, $id)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method' => 'required|string',
            'recurring_payment' => 'nullable|numeric|min:0',
            'interval' => 'nullable|numeric',
        ]);

        try {
            $invoice = Invoice::findOrFail($id);
            $client = Client::findOrFail($invoice->client_id);
            $addedByUser = User::findOrFail($invoice->added_by);

            if ($invoice->paid_at) {
                return response()->json(['success' => false, 'message' => 'Invoice already paid.'], 400);
            }

            Stripe::setApiKey(env('STRIPE_SECRET'));

            // 1. Create or retrieve a Stripe Customer
            $stripeCustomer = null;
            if ($client->stripe_customer_id) {
                try {
                    $stripeCustomer = \Stripe\Customer::retrieve($client->stripe_customer_id);
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    // If the customer does not exist on Stripe, create a new one
                    if (str_contains($e->getMessage(), 'No such customer')) {
                        $stripeCustomer = \Stripe\Customer::create([
                            'email' => $client->email,
                            'name' => $client->first_name . ' ' . $client->last_name,
                        ]);
                        $client->update(['stripe_customer_id' => $stripeCustomer->id]);
                    } else {
                        throw $e; // Re-throw other exceptions
                    }
                }
            } else {
                // Create a new Stripe Customer if no ID exists in the database
                $stripeCustomer = \Stripe\Customer::create([
                    'email' => $client->email,
                    'name' => $client->first_name . ' ' . $client->last_name,
                ]);
                $client->update(['stripe_customer_id' => $stripeCustomer->id]);
            }

            // 2. Retrieve and attach the PaymentMethod to the Customer
            $paymentMethodId = $request->input('payment_method');
            $paymentMethod = \Stripe\PaymentMethod::retrieve($paymentMethodId);
            $paymentMethod->attach(['customer' => $stripeCustomer->id]);

            \Stripe\Customer::update($stripeCustomer->id, [
                'invoice_settings' => ['default_payment_method' => $paymentMethodId],
            ]);

            // 3. Mark the invoice as paid
            $invoice->update([
                'paid_at' => now(),
                'payment_method' => 'stripe',
            ]);

            // 4. If `recurring_payment` is set and valid, create a subscription
            if ($request->filled('recurring_payment') && $request->recurring_payment > 0) {
                $product = \Stripe\Product::create([
                    'name' => 'Subscription for Invoice #' . $invoice->id,
                    'metadata' => ['invoice_id' => $invoice->id],
                ]);

                $intervalType = 'month';
                $intervalCount = $request->input('interval', 1);

                $price = \Stripe\Price::create([
                    'unit_amount' => $request->recurring_payment * 100,
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => $intervalType,
                        'interval_count' => $intervalCount,
                    ],
                    'product' => $product->id,
                ]);

                // Determine the start date for the subscription
                $billingDate = $invoice->billing_date 
                    ? Carbon::parse($invoice->billing_date)->startOfDay() 
                    : now()->addDay(); // Default to tomorrow if no billing_date is set

                if ($billingDate->isPast()) {
                    $billingDate = now()->addDay();
                }

                $subscription = \Stripe\Subscription::create([
                    'customer' => $stripeCustomer->id,
                    'items' => [['price' => $price->id]],
                    'default_payment_method' => $paymentMethodId,
                    'metadata' => ['invoice_id' => $invoice->id],
                    'billing_cycle_anchor' => $billingDate->timestamp, // Set start date
                ]);

                InvoiceSubscription::create([
                    'invoice_id' => $invoice->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $request->recurring_payment,
                    'currency' => 'usd',
                    'intervel' => 'month',
                    'starts_at' => $billingDate,
                    'payment_by' => 'paypal',
                    'ends_at' => $billingDate->copy()->addMonth(),
                ]);
            }

            // 5. Send email notifications
            Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
            Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));

            return response()->json([
                'success' => true,
                'message' => 'Invoice successfully paid and email notifications sent.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function profile(){
        return view('c_main.c_profile');
    }

    public function createCheckoutSession(Request $request, Invoice $invoice)
    {
        try {
            // Ensure the authenticated user can access the invoice
            if ($invoice->user_id !== auth()->id()) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }

            $addedByUser = User::findOrFail($invoice->added_by);

            if (empty($addedByUser->stripe_connect_account_id)) {
                return response()->json(['error' => 'Seller has not connected their Stripe account'], 400);
            }

            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            // Check for recurring payment
            $nextPaymentRecurring = $request->input('next_payment_recurring', 0);
            $intervalText = $request->input('interval_text', '1 month');

            if ($nextPaymentRecurring > 0) {
                // Extract interval type and count from $intervalText
                $intervalDetails = explode(' ', $intervalText);
                $intervalCount = intval($intervalDetails[0]) ?? 1; // Default to 1
                $intervalType = isset($intervalDetails[1]) && str_contains(strtolower($intervalDetails[1]), 'month') 
                                ? 'month' 
                                : 'year'; // Default to 'month'
    
                // Create a product and price for the subscription
                $product = $stripe->products->create([
                    'name' => 'Recurring Payment for Invoice #' . $invoice->invoice_no,
                    'description' => 'Recurring payment every ' . $intervalText,
                ]);
    
                $price = $stripe->prices->create([
                    'unit_amount' => $nextPaymentRecurring * 100,
                    'currency' => 'usd',
                    'recurring' => [
                        'interval' => $intervalType,
                        'interval_count' => $intervalCount,
                    ],
                    'product' => $product->id,
                ]);
    
                // Create a Checkout Session for subscriptions
                $session = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        [
                            'price' => $price->id,
                            'quantity' => 1,
                        ],
                    ],
                    'mode' => 'subscription',
                    'subscription_data' => [
                        'application_fee_percent' => 100, // Adjust as needed (e.g., 1 dollar)
                        'transfer_data' => [
                            'destination' => $addedByUser->stripe_connect_account_id,
                        ],
                    ],
                    'success_url' => route('portal.invoices.show.new', $invoice->id) . '?payment_status=success&is_subscription=true',
                    'cancel_url' => route('portal.invoices.show.new', $invoice->id) . '?payment_status=canceled',
                ]);
            }  else {
                // One-time payment session
                $session = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'line_items' => [
                        [
                            'price_data' => [
                                'currency' => 'usd',
                                'product_data' => [
                                    'name' => 'Invoice Payment for ' . $invoice->invoice_no,
                                    'description' => 'Payment for invoice #' . $invoice->invoice_no,
                                ],
                                'unit_amount' => $invoice->total * 100,
                            ],
                            'quantity' => 1,
                        ],
                    ],
                    'mode' => 'payment',
                    'payment_intent_data' => [
                        'on_behalf_of' => $addedByUser->stripe_connect_account_id,
                        'transfer_data' => [
                            'destination' => $addedByUser->stripe_connect_account_id,
                        ],
                    ],
                    'success_url' => route('portal.invoices.show.new', $invoice->id) . '?payment_status=success',
                    'cancel_url' => route('portal.invoices.show.new', $invoice->id) . '?payment_status=canceled',
                ]);
            }

            return response()->json(['url' => $session->url], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    public function invoice_show_new($id, Request $request)
    {
        $invoice = Invoice::with(['client', 'items'])->findOrFail($id);
        $paymentStatus = $request->query('payment_status');
        $isSubscription = $request->query('is_subscription') === 'true';

        $message = null;

        if ($paymentStatus === 'success' && $invoice->paid_at==null) {
            $client = Client::findOrFail($invoice->client_id);
            $addedByUser = User::findOrFail($invoice->added_by);

            if (!$invoice->paid_at) {
                // Mark the invoice as paid
                $invoice->update([
                    'paid_at' => now(),
                    'payment_method' => 'stripe',
                ]);

                // If it's a subscription, store the subscription details
                if ($isSubscription) {
                    Stripe::setApiKey(env('STRIPE_SECRET'));

                    $subscriptionId = $request->query('subscription_id'); // Pass the subscription ID from Stripe
                    $subscription = \Stripe\Subscription::retrieve($subscriptionId);

                    InvoiceSubscription::create([
                        'invoice_id' => $invoice->id,
                        'subscription_id' => $subscription->id,
                        'amount' => $subscription->plan->amount / 100,
                        'currency' => $subscription->plan->currency,
                        'intervel' => $subscription->plan->interval,
                        'starts_at' => Carbon::createFromTimestamp($subscription->current_period_start),
                        'ends_at' => Carbon::createFromTimestamp($subscription->current_period_end),
                    ]);
                }

                // Send email notifications
                Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
                Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));

                $message = 'Payment successful. Thank you for your payment.';
            } else {
                $message = 'Invoice already paid.';
            }
        } elseif ($paymentStatus === 'canceled') {
            $message = 'Payment canceled. Please try again.';
        }

        // Retrieve the services in case you want to display service information in the invoice
        $services = Service::where('user_id', auth()->id())->get();

        // Fetch all users and team members added by the current logged-in user
        $users = User::all();
        $teamMembers = TeamMember::where('added_by', auth()->id())->get();

        // Pass the invoice data to the view
        return view('c_main.c_pages.c_invoice.c_show', compact('invoice', 'services', 'users', 'teamMembers', 'message'));
    }

    public function invoice_subscription(Request $request)
    {
        $search = $request->input('search'); // Get the search query from the request

        // Query InvoiceSubscription and related Invoice data
        $subscriptions = InvoiceSubscription::with(['invoice.client'])
            ->whereHas('invoice', function ($query) use ($search) {
                $query->where('client_id', $this->client_id) // Ensure this condition is applied
                    ->when($search, function ($query, $search) {
                        $query->where('billing_first_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('billing_last_name', 'LIKE', '%' . $search . '%')
                            ->orWhere('note', 'LIKE', '%' . $search . '%');
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('c_main.c_pages.c_invoice.c_subscription', compact('subscriptions', 'search'));
    }

    public function cancel_subscription(Request $request, $id)
    {
        $subscription = InvoiceSubscription::findOrFail($id);

        // Check if the subscription is already canceled
        if ($subscription->cancelled_at) {
            return redirect()->back()->with('error', 'Subscription is already cancelled.');
        }

        try {
            // Cancel the subscription on Stripe
            if ($subscription->subscription_id) {
                \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

                $stripeSubscription = \Stripe\Subscription::retrieve($subscription->subscription_id);
                $stripeSubscription->cancel();
            }

            // Update the database to reflect the cancellation
            $subscription->update([
                'cancelled_at' => now(),
            ]);

            // Send email notifications
            $invoice = $subscription->invoice; // Assuming `invoice` relation is defined in the model
            $client = $invoice->client; // Assuming `client` relation is defined in the model
            $owner = User::find($invoice->added_by); // Retrieve the user who added the invoice

            $details = [
                'subject' => 'Subscription Cancelled',
                'message' => 'The subscription for invoice #' . $invoice->invoice_no . ' has been cancelled.',
                'subscription' => $subscription,
                'invoice' => $invoice,
                'client' => $client,
            ];

            Mail::to($owner->email)->send(new \App\Mail\CommonNotification($details));
            Mail::to($client->email)->send(new \App\Mail\CommonNotification($details));

            return redirect()->back()->with('success', 'Subscription cancelled successfully, and email notifications sent.');
        } catch (\Exception $e) {
            // Handle any errors that occur during the cancellation process
            return redirect()->back()->with('error', 'An error occurred while cancelling the subscription: ' . $e->getMessage());
        }
    }

    public function addFund(Request $request){
        $accountId = Client::where('clients.id', getAuthenticatedUser()->id)
            ->join('users', 'users.id', '=', 'clients.added_by')
            ->pluck('users.stripe_connect_account_id')
            ->first();
        
        //echo $accountId; die;
        if(!$accountId){
            return redirect()->back()->with('error', 'user has not connected the stripe connect id' );
        }

        return view('c_main.c_pages.c_fund')->with('accountId', $accountId);
    }

    public function processPaymentFundAdd(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $amount = (int) ($request->amount * 100); // Convert to cents
            $accountId = $request->accountId;
            $user = getAuthenticatedUser();

            $intent = \Stripe\PaymentIntent::create([
                'amount' => $amount,
                'currency' => 'usd',
                'payment_method' => $request->payment_method,
                'confirm' => true,
                'description' => "Funds added for services rendered by " . $user->first_name . ' ' . $user->last_name,
                'shipping' => [
                    'name' => $user->first_name . ' ' . $user->last_name,
                    'address' => [
                        'line1'       => $user->billing_address ?: 'N/A',
                        'postal_code' => $user->postal_code ?: 'N/A',
                        'city'        => $user->city ?: 'N/A',
                        'state'       => $user->state ?: 'N/A',
                        'country'     => $user->country ?: 'IN', // Default to India
                    ],
                ],
                'return_url' => route('portal.fund.success'),
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never'
                ],
            ], [
                'stripe_account' => $accountId
            ]);

            // Store PaymentIntent in DB
            $transaction = new UserFund();
            $transaction->user_id = $user->id;
            $transaction->stripe_payment_intent = $intent->id;
            $transaction->amount = $request->amount;
            $transaction->currency = 'usd';
            $transaction->status = $intent->status;
            $transaction->save();

            // Handle Payment Status
            if ($intent->status === 'requires_action') {
                return response()->json([
                    'requires_action' => true,
                    'client_secret' => $intent->client_secret,
                    'payment_intent_id' => $intent->id, // Store PaymentIntent ID for verification
                ]);
            } elseif ($intent->status === 'succeeded') {
                // ✅ **Update User Balance Immediately**
                $user->account_balance += $request->amount;
                $user->save();

                return response()->json(['success' => true, 'balance_updated' => true]);
            } else {
                return response()->json(['error' => 'Unexpected payment status: ' . $intent->status], 400);
            }

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function verifyPaymentStatus(Request $request)
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Ensure we retrieve the PaymentIntent from the connected account
            $paymentIntent = \Stripe\PaymentIntent::retrieve(
                $request->payment_intent_id,
                ['stripe_account' => $request->accountId] // ✅ Add this
            );

            if ($paymentIntent->status === 'succeeded') {
                // ✅ **Update User Balance**
                $user = getAuthenticatedUser();
                $user->account_balance += UserFund::where('stripe_payment_intent', $paymentIntent->id)->value('amount');
                $user->save();

                // ✅ **Mark Transaction as Completed**
                UserFund::where('stripe_payment_intent', $paymentIntent->id)
                    ->update(['status' => 'succeeded']);

                return response()->json(['success' => true, 'balance_updated' => true]);
            } else {
                return response()->json(['error' => 'Payment not successful'], 400);
            }

        } catch (\Stripe\Exception\ApiErrorException $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function paymentSuccess()
    {
        return view('funds.success'); // Create a success page
    }

    public function payWithBalance(Request $request, $invoiceId)
    {
        DB::beginTransaction();
        try {
            $user = getAuthenticatedUser();
            $invoice = Invoice::findOrFail($invoiceId);
            $client = Client::findOrFail($invoice->client_id);
            $addedByUser = User::findOrFail($invoice->added_by);

            // Validate JSON input
            $request->validate([
                'invoice_id' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
            ]);

            $amountDue = (float) $request->amount; // Convert amount to float

            // Check if the user has enough balance
            if ($user->account_balance < $amountDue) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance.',
                ], 400);
            }

            // Deduct balance
            $user->account_balance -= $amountDue;
            $user->save();

            // Record the deduction in the user_funds table
            UserFund::create([
                'user_id' => $user->id,
                'amount' => -$amountDue,
                'currency' => 'usd',
                'status' => 'paid',
            ]);

            // Mark invoice as paid
            $invoice->update([
                'paid_at' => now(),
                'payment_method' => 'balance',
            ]);

            // Send email notifications
            Mail::to($addedByUser->email)->send(new \App\Mail\InvoicePaid($invoice, $client, $addedByUser));
            Mail::to($client->email)->send(new \App\Mail\InvoicePaidConfirmation($invoice, $client));

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Payment successful. Invoice has been paid.',
                'redirect_url' => route('portal.invoices.show', $invoice->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function intakeform($id, $invoice, $order)
    {
        $service = Service::findOrFail($id);
        $intake_form = Intakeform::find($service->intake_form);

        if (!$intake_form) {
            abort(404, 'No intake form associated with this service.');
        }

        $invoiceModel = Invoice::findOrFail($invoice);

        return view('c_main.c_pages.c_order.c_intake_form', [
            'intake_form' => $intake_form,
            'invoice_no' => $invoice,
            'landing_page' => $service->id,
            'order' => $order
        ]);
    }

    public function storeIntakeForm(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                'order_id'     => 'required|exists:orders,id',
                'landing_page' => 'required|integer',
                'invoice_id'   => 'nullable|integer',
                'form_data'    => 'required|array',
            ]);

            $orderId = $validated['order_id'];
            $formData = $validated['form_data'];

            foreach ($formData as $field) {
                $fieldName = trim(str_replace('*', '', $field['name'] ?? 'unknown'));
                $fieldType = $field['type'] ?? 'text';
                $fieldValue = $field['value'] ?? null;

                // If it's a file in base64, save it as an uploaded file
                if (
                    $fieldType === 'file' &&
                    !empty($fieldValue) &&
                    preg_match('/^data:(image|application)\/(\w+);base64,/', $fieldValue)
                ) {
                    $fieldValue = $this->saveBase64File($fieldValue);
                }

                // Save each field
                OrderProjectData::create([
                    'order_id'    => $orderId,
                    'field_name'  => $fieldName,
                    'field_type'  => $fieldType,
                    'field_value' => $fieldValue,
                ]);
            }

            // Mark the order as intake form submitted
            Order::where('id', $orderId)->update(['is_intake_form_data_submitted' => 1]);
            return response()->json(['success' => true, 'message' => 'Intake form submitted successfully.']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save intake form: ' . $e->getMessage()
            ], 500);
        }
    }

    private function saveBase64File($base64String)
    {
        if (preg_match('/^data:(image|application)\/(\w+);base64,/', $base64String, $matches)) {
            $extension = $matches[2];
            $data = substr($base64String, strpos($base64String, ',') + 1);
            $data = base64_decode($data);

            $filename = uniqid('upload_') . '.' . $extension;
            $path = storage_path('app/public/uploads/' . $filename);

            file_put_contents($path, $data);

            return 'storage/uploads/' . $filename;
        }

        return null;
    }

    public function notifications()
    {
        $clientId = getUserID(); // Replace with your method to get the current client ID

        $notifications = History::leftJoin('orders', 'history.order_id', '=', 'orders.id')
            ->leftJoin('tickets', 'history.ticket_id', '=', 'tickets.id')
            ->where(function ($query) use ($clientId) {
                $query->where('orders.client_id', $clientId)
                    ->orWhere('tickets.client_id', $clientId);
            })
            ->select('history.*') // important to avoid ambiguous columns
            ->orderBy('history.created_at', 'desc')
            ->paginate(10);

        return view('c_main.c_pages.c_notifications', compact('notifications'));
    }

    public function destroynotification($id)
    {
        History::findOrFail($id)->delete();
        return back()->with('success', 'Notification deleted successfully.');
    }
}
