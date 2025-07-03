@extends('organizer.layout')

@section('content')
<style>
.customer-selection-wrapper {
  display: flex;
  align-items-end;
  gap: 10px;
}

.customer-select-container {
  flex: 1;
}

.customer-btn-container {
  flex-shrink: 0;
}

.error-text {
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.modal-lg {
  max-width: 800px;
}

#createCustomerModal .form-group {
  margin-bottom: 1rem;
}

#createCustomerModal label {
  font-weight: 600;
  margin-bottom: 0.5rem;
}
</style>

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
                <div class="customer-selection-wrapper">
                  <div class="customer-select-container">
                    <select name="customer_id" id="customer_select" class="form-control select2" required>
                      <option value="">{{ __('Select a Customer') }}</option>
                      @foreach($customers as $customer)
                        <option value="{{ $customer->id }}">{{ $customer->fname }} {{ $customer->lname }} ({{ $customer->email }})</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="customer-btn-container">
                    <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createCustomerModal">
                      <i class="fas fa-user-plus"></i> {{ __('New') }}
                    </button>
                  </div>
                </div>
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

// AJAX Customer Creation
$(document).ready(function() {
  $('#saveCustomerBtn').click(function() {
    let formData = new FormData($('#createCustomerForm')[0]);
    
    // Clear previous errors
    $('.error-text').text('');
    
    // Show loading state
    $(this).prop('disabled', true).text('{{ __("Creating...") }}');
    
    $.ajax({
      url: '{{ route("organizer.customer.store") }}',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        console.log('Success response:', response);
        if(response.status === 'success') {
          // Add new customer to select dropdown
          let customerText = response.customer.fname + ' ' + response.customer.lname + ' (' + response.customer.email + ')';
          let newOption = new Option(customerText, response.customer.id, true, true);
          
          // Clear existing selection and add new option
          $('#customer_select').append(newOption);
          
          // Reinitialize Select2 and trigger change
          $('#customer_select').val(response.customer.id).trigger('change');
          
          // Close modal and reset form
          $('#createCustomerModal').modal('hide');
          $('#createCustomerForm')[0].reset();
          
          // Show success message
          alert('{{ __("Customer created successfully!") }}');
        } else {
          console.error('Unexpected response format:', response);
          alert('{{ __("An error occurred. Please try again.") }}');
        }
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', {
          status: xhr.status,
          statusText: xhr.statusText,
          responseText: xhr.responseText,
          error: error
        });
        
        if(xhr.status === 422) {
          let errors = xhr.responseJSON.errors;
          $.each(errors, function(key, value) {
            $('.' + key + '_error').text(value[0]);
          });
        } else if(xhr.status === 500) {
          console.error('Server error:', xhr.responseText);
          alert('{{ __("Server error occurred. Please check your input and try again.") }}');
        } else {
          alert('{{ __("An error occurred. Please try again.") }}');
        }
      },
      complete: function() {
        $('#saveCustomerBtn').prop('disabled', false).text('{{ __("Create Customer") }}');
      }
    });
  });
  
  // Reset form and errors when modal is closed
  $('#createCustomerModal').on('hidden.bs.modal', function() {
    $('#createCustomerForm')[0].reset();
    $('.error-text').text('');
  });
});
</script>

<!-- Create Customer Modal -->
<div class="modal fade" id="createCustomerModal" tabindex="-1" role="dialog" aria-labelledby="createCustomerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="createCustomerModalLabel">{{ __('Create New Customer') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="createCustomerForm">
          @csrf
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label>{{ __('First Name') }} *</label>
                <input type="text" name="fname" class="form-control" required>
                <span class="text-danger error-text fname_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>{{ __('Last Name') }} *</label>
                <input type="text" name="lname" class="form-control" required>
                <span class="text-danger error-text lname_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>{{ __('Email') }} *</label>
                <input type="email" name="email" class="form-control" required>
                <span class="text-danger error-text email_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>{{ __('Username') }} *</label>
                <input type="text" name="username" class="form-control" required>
                <span class="text-danger error-text username_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>{{ __('Phone') }}</label>
                <input type="text" name="phone" class="form-control">
                <span class="text-danger error-text phone_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>{{ __('Password') }} *</label>
                <input type="password" name="password" class="form-control" required>
                <span class="text-danger error-text password_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>{{ __('Confirm Password') }} *</label>
                <input type="password" name="password_confirmation" class="form-control" required>
                <span class="text-danger error-text password_confirmation_error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label>{{ __('Address') }}</label>
                <input type="text" name="address" class="form-control">
                <span class="text-danger error-text address_error"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>{{ __('Country') }}</label>
                <input type="text" name="country" class="form-control">
                <span class="text-danger error-text country_error"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>{{ __('State') }}</label>
                <input type="text" name="state" class="form-control">
                <span class="text-danger error-text state_error"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group">
                <label>{{ __('City') }}</label>
                <input type="text" name="city" class="form-control">
                <span class="text-danger error-text city_error"></span>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="button" class="btn btn-primary" id="saveCustomerBtn">{{ __('Create Customer') }}</button>
      </div>
    </div>
  </div>
</div>

@section('custom-script')
</code_block_to_apply_changes_from>