<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Event\Booking;
use App\Models\Event\Ticket;
use App\Models\Customer;
use App\Models\BasicSettings\Basic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Event\EventContent;
use App\Models\Language;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Mail;
use App\Mail\BookingMail;
<<<<<<< HEAD

class EventBookingController extends Controller
{
=======
use Vonage\Client;
use Vonage\Client\Credentials\Basic as VonageBasic;
use Illuminate\Support\Facades\Log;
use App\Services\ClickSendService;

class EventBookingController extends Controller
{
    protected $clickSendService;

    public function __construct(ClickSendService $clickSendService)
    {
        $this->clickSendService = $clickSendService;
    }

>>>>>>> 3ffe933778e70ed78ff7578c224db97e7b9eed6e
    public function index()
    {
        $bookings = Booking::with(['event.information', 'customerInfo'])
            ->whereHas('event', function ($query) {
                $query->where('organizer_id', Auth::id());
            })
            ->latest()
            ->paginate(10);

        return view('organizer.event.booking.index', compact('bookings'));
    }

    public function create()
    {
<<<<<<< HEAD
=======
       
>>>>>>> 3ffe933778e70ed78ff7578c224db97e7b9eed6e
        $events = Event::with('information')
            ->where('organizer_id', Auth::id())
            ->get();
        $customers = Customer::all();
        
        return view('organizer.event.booking.create', compact('events', 'customers'));
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'event_id' => 'required|exists:events,id',
        //     'customer_id' => 'required|exists:customers,id',
        //     'ticket_id' => 'required|exists:tickets,id',
        //     'quantity' => 'required|integer|min:1',
        //     'payment_status' => 'required|in:pending,completed',
        //     'payment_method' => 'required|in:offline,cash,bank'
        // ]);

        // try {
            // Get ticket and calculate price
            $ticket = Ticket::findOrFail($request->ticket_id);
            $event = Event::findOrFail($request->event_id);
            $customer = Customer::findOrFail($request->customer_id);
            
            // Verify ticket belongs to event
            if ($ticket->event_id != $request->event_id) {
                return redirect()->back()->with('error', 'Invalid ticket for this event.');
            }

            // Check ticket availability based on type
            if ($ticket->ticket_available_type == 'limited') {
                // For limited tickets, check if enough tickets are available
                if ($ticket->ticket_available <= 0) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'This ticket is sold out.'])
                        ->withInput();
                }
                
                if ($request->quantity > $ticket->ticket_available) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'Only ' . $ticket->ticket_available . ' tickets are available.'])
                        ->withInput();
                }
            }

            // Check max buy ticket limit if set
            if ($ticket->max_buy_ticket) {
                if ($request->quantity > $ticket->max_buy_ticket) {
                    return redirect()->back()
                        ->withErrors(['quantity' => 'Maximum ' . $ticket->max_buy_ticket . ' tickets can be purchased per booking.'])
                        ->withInput();
                }
            }

            // Calculate total price
            $price = $ticket->price * $request->quantity;

            // Get basic settings for tax and commission
            $basic = Basic::select('tax', 'commission')->first();
            $tax = ($price * $basic->tax) / 100;
            $commission = ($price * $basic->commission) / 100;

            // Create variations array for single ticket type
            $variations = [];
            for ($i = 1; $i <= $request->quantity; $i++) {
                $variations[] = [
                    'ticket_id' => $ticket->id,
                    'early_bird_dicount' => 0,
                    'name' => $ticket->ticketContent->first() ? $ticket->ticketContent->first()->title : 'Ticket #' . $ticket->id,
                    'qty' => 1,
                    'price' => $ticket->price,
                    'scan_status' => 0,
                    'unique_id' => uniqid()
                ];
            }

            // Create booking
            $booking = Booking::create([
                'customer_id' => $request->customer_id,
                'booking_id' => uniqid(),
                'event_id' => $request->event_id,
                'organizer_id' => Auth::id(),
                'fname' => $customer->fname,
                'lname' => $customer->lname,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'country' => $customer->country,
                'state' => $customer->state,
                'city' => $customer->city,
                'zip_code' => $customer->zip_code,
                'address' => $customer->address,
                'variation' => json_encode($variations),
                'price' => $price,
                'tax' => $tax,
                'commission' => $commission,
                'tax_percentage' => $basic->tax,
                'commission_percentage' => $basic->commission,
                'quantity' => $request->quantity,
                'paymentMethod' => $request->payment_method,
                'gatewayType' => 'offline',
                'paymentStatus' => $request->payment_status,
                'event_date' => now()
            ]);

            // Update ticket availability if limited
            if ($ticket->ticket_available_type == 'limited') {
                $ticket->ticket_available = $ticket->ticket_available - $request->quantity;
                $ticket->save();
            }

            // Generate invoice
            $invoice = $this->generateInvoice($booking, $event->id);
            $booking->update(['invoice' => $invoice]);

            //send mail
            Mail::to($customer->email)->send(new BookingMail($booking));
<<<<<<< HEAD
=======
            
            // send email to  click send 
            try {
                $message = "Thank you for booking {$event->information->title}! Your booking ID is {$booking->booking_id}. You can download your invoice from this link: " . asset('assets/admin/file/invoices/' . $invoice);
                $this->clickSendService->sendSMS(preg_replace('/[^\d+]/', '', '1' . $customer->phone), $message);
            } catch (\Exception $e) {
                Log::error('ClickSend SMS Error: ' . $e->getMessage());
                // Continue with the booking process even if SMS fails
            }
>>>>>>> 3ffe933778e70ed78ff7578c224db97e7b9eed6e

            return redirect()->route('organizer.event.booking')
                ->with('success', 'Booking created successfully.');

<<<<<<< HEAD
        // } catch (\Exception $e) {
        //     return redirect()->back()
        //         ->with('error', 'Something went wrong! Please try again.');
        // }
=======
>>>>>>> 3ffe933778e70ed78ff7578c224db97e7b9eed6e
    }

    public function generateInvoice($bookingInfo, $eventId)
    {
        // try {
            $fileName = $bookingInfo->booking_id . '.pdf';
            $directory = public_path('assets/admin/file/invoices/');

            @mkdir($directory, 0775, true);

            $fileLocated = $directory . $fileName;

            //generate qr code
            @mkdir(public_path('assets/admin/qrcodes/'), 0775, true);
            if ($bookingInfo->variation != null) {
                //generate qr code for without wise ticket
                $variations = json_decode($bookingInfo->variation, true);
                foreach ($variations as $variation) {
                    QrCode::size(200)->generate($bookingInfo->booking_id . '__' . $variation['unique_id'], public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '__' . $variation['unique_id'] . '.svg');
                }
            } else {
                //generate qr code for without wise ticket
                for ($i = 1; $i <= $bookingInfo->quantity; $i++) {
                    QrCode::size(200)->generate($bookingInfo->booking_id . '__' . $i, public_path('assets/admin/qrcodes/') . $bookingInfo->booking_id . '__' . $i . '.svg');
                }
            }

            // get course title
            $language = Language::where('is_default', 1)->first();
            $event = Event::find($bookingInfo->event_id);
            $eventInfo = EventContent::where('event_id', $bookingInfo->event_id)->where('language_id', $language->id)->first();

            $width = "50%";
            $float = "right";
            $mb = "35px";
            $ml = "18px";

            PDF::loadView('frontend.event.invoice', compact('bookingInfo', 'event', 'eventInfo', 'width', 'float', 'mb', 'ml', 'language'))->save($fileLocated);

            return $fileName;
        // } catch (\Exception $e) {
        //     Session::flash('error', $e->getMessage());
        //     return;
        // }
    }

    public function getTickets(Request $request)
    {
        $eventId = $request->input('event_id');
        $tickets = Ticket::with(['ticketContent' => function($query) {
            $query->where('language_id', 8); // Default to English (language_id = 8)
        }])
        ->where('event_id', $eventId)
        ->get();
        return response()->json($tickets);
    }
} 