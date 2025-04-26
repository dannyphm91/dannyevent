<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'customer_id',
        'ticket_id',
        'quantity',
        'total_amount',
        'payment_status',
        'payment_method'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function ticket()
    {
        return $this->belongsTo(\App\Models\Event\Ticket::class, 'ticket_id');
    }
} 