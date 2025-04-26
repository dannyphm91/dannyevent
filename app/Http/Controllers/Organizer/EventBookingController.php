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

class EventBookingController extends Controller
{
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
        $events = Event::with('information')
            ->where('organizer_id', Auth::id())
            ->get();
        $customers = Customer::all();
        
        return view('organizer.event.booking.create', compact('events', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'customer_id' => 'required|exists:customers,id',
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1',
            'payment_status' => 'required|in:pending,completed',
            'payment_method' => 'required|in:offline,cash,bank'
        ]);

        try {
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

            return redirect()->route('organizer.event.booking')
                ->with('success', 'Booking created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Something went wrong! Please try again.');
        }
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