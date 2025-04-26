@extends('organizer.layout')

@section('content')
<div class="page-header">
  <h4 class="page-title">{{ __('Create New Booking') }}</h4>
  <ul class="breadcrumbs">
    <li class="nav-home">
      <a href="{{ route('organizer.dashboard') }}">
        <i class="flaticon-home"></i>
      </a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="{{ route('organizer.event.booking') }}">{{ __('Event Bookings') }}</a>
    </li>
    <li class="separator">
      <i class="flaticon-right-arrow"></i>
    </li>
    <li class="nav-item">
      <a href="#">{{ __('Create Booking') }}</a>
    </li>
  </ul>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card">
      <div class="card-header">
        <div class="card-title d-inline-block">{{ __('Create New Booking') }}</div>
        <a class="btn btn-info btn-sm float-right d-inline-block" href="{{ route('organizer.event.booking') }}">
          <span class="btn-label">
            <i class="fas fa-backward"></i>
          </span>
          {{ __('Back') }}
        </a>
      </div>
      <div class="card-body">
        <form action="{{ route('organizer.event.booking.store') }}" method="POST">
          @csrf
          <div class="row">
            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Select Event') }} *</label>
                <select name="event_id" class="form-control select2" required>
                  <option value="">{{ __('Select an Event') }}</option>
                  @foreach($events as $event)
                    <option value="{{ $event->id }}">{{ $event->information->title }}</option>
                  @endforeach
                </select>
                @error('event_id')
                  <p class="text-danger mb-0">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Select Customer') }} *</label>
                <select name="customer_id" class="form-control select2" required>
                  <option value="">{{ __('Select a Customer') }}</option>
                  @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->fname }} {{ $customer->lname }} ({{ $customer->email }})</option>
                  @endforeach
                </select>
                @error('customer_id')
                  <p class="text-danger mb-0">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Select Ticket') }} *</label>
                <select name="ticket_id" class="form-control select2" required>
                  <option value="">{{ __('Select a Ticket') }}</option>
                </select>
                @error('ticket_id')
                  <p class="text-danger mb-0">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Quantity') }} *</label>
                <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                @error('quantity')
                  <p class="text-danger mb-0">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Payment Status') }} *</label>
                <select name="payment_status" class="form-control" required>
                  <option value="completed">{{ __('Completed') }}</option>
                  <option value="pending">{{ __('Pending') }}</option>
                </select>
                @error('payment_status')
                  <p class="text-danger mb-0">{{ $message }}</p>
                @enderror
              </div>
            </div>

            <div class="col-lg-6">
              <div class="form-group">
                <label>{{ __('Payment Method') }}</label>
                <select name="payment_method" class="form-control">
                  <option value="offline">{{ __('Offline') }}</option>
                  <option value="cash">{{ __('Cash') }}</option>
                  <option value="bank">{{ __('Bank Transfer') }}</option>
                </select>
                @error('payment_method')
                  <p class="text-danger mb-0">{{ $message }}</p>
                @enderror
              </div>
            </div>
          </div>

          <div class="card-footer">
            <div class="row">
              <div class="col-12 text-center">
                <button type="submit" class="btn btn-success">
                  {{ __('Create Booking') }}
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('custom-script')

@endsection

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
  console.log('Document ready');
  // Initialize Select2 with debug logging
  $('.select2').select2({
    width: '100%',
    debug: true
  }).on('select2:open', function() {
    console.log('Select2 opened');
  });

  // Load tickets when event is selected - using multiple event handlers
  var eventSelect = $('select[name="event_id"]');
  
  // Handle both change and select2:select events
  eventSelect.on('change select2:select', function(e) {
    console.log('Event triggered:', e.type);
    var eventId = $(this).val();
    console.log('Selected event ID:', eventId);
    
    if(eventId) {
      $.ajax({
        url: "{{ route('organizer.event.tickets') }}",
        type: "GET",
        data: {event_id: eventId},
        success: function(data) {
          console.log('Tickets data received:', data);
          var ticketSelect = $('select[name="ticket_id"]');
          ticketSelect.empty();
          ticketSelect.append('<option value="">{{ __("Select a Ticket") }}</option>');
          $.each(data, function(key, ticket) {
            var price = ticket.price ? ticket.price : '0';
            var ticketTitle = '';
            
            // Get the ticket title from ticket_content
            if (ticket.ticket_content && ticket.ticket_content.length > 0) {
              ticketTitle = ticket.ticket_content[0].title;
            } else {
              ticketTitle = 'Ticket #' + ticket.id;
            }
            
            var ticketInfo = ticketTitle;
            
            if (ticket.ticket_available_type === 'limited') {
              ticketInfo += ' (' + ticket.ticket_available + ' available)';
            }
            ticketInfo += ' - $' + price;
            
            if (ticket.early_bird_discount === 'enable') {
              var discountAmount = ticket.early_bird_discount_amount;
              var discountType = ticket.early_bird_discount_type;
              ticketInfo += ' (Early Bird: ' + discountAmount + (discountType === 'percentage' ? '%' : '$') + ' off)';
            }
            
            ticketSelect.append('<option value="'+ ticket.id +'">'+ ticketInfo +'</option>');
          });
          // Reinitialize Select2 on ticket select
          ticketSelect.select2({
            width: '100%',
            debug: true
          });
        },
        error: function(xhr, status, error) {
          console.error('Ajax error:', error);
          console.error('Response:', xhr.responseText);
        }
      });
    } else {
      $('select[name="ticket_id"]').empty();
    }
  });

  // Add direct click handler as fallback
  eventSelect.on('click', function() {
    console.log('Event select clicked');
  });
});
</script>