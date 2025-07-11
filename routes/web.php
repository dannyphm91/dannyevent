<?php

use Illuminate\Support\Facades\Route;
use Spatie\GoogleCalendar\Event;

/*
|--------------------------------------------------------------------------
| User Interface Routes
|--------------------------------------------------------------------------
*/

Route::get('/offline', 'FrontEnd\HomeController@offline');
Route::get('/terms-and-conditions', function () {
    return view('frontend.terms-and-conditions');
});
Route::get('login', function () {
  return view('frontend.organizer.login');
})->name('login');

// cron job for check iyzico payment 
Route::get('/check-payment', 'CronJobController@checkIyzicoPendingPayment')->name('cron.check.payment');

Route::get('midtrans/cancel', 'FrontEnd\HomeController@midtrans_cancel')->name('midtrans_cancel'); // banking er IPN
Route::post('xendit/callback', 'FrontEnd\HomeController@xendit_callback')->name('xendit_cancel');
Route::get('myfatoorah/callback', 'FrontEnd\HomeController@myfatoorah_callback')->name('myfatoorah_callback');

Route::get('myfatoorah/cancel', 'FrontEnd\HomeController@myfatoorah_cancel')->name('myfatoorah_cancel');

/*
|---------------------------------------------------------------------------------
| Customer routes are goes here
|---------------------------------------------------------------------------------
*/
/*---socialite---*/
##==facebook
Route::get('login/facebook/callback', 'FrontEnd\CustomerController@handleFacebookCallback');

#====google
Route::get('login/google/callback', 'FrontEnd\CustomerController@handleGoogleCallback');
/*---socialite end---*/

Route::middleware('change.lang')->prefix('/customer')->group(function () {
  Route::middleware('guest:customer', 'change.lang')->group(function () {
    Route::get('/login', 'FrontEnd\CustomerController@login')->name('customer.login');
    Route::get('/signup', 'FrontEnd\CustomerController@signup')->name('customer.signup');
    Route::post('/create', 'FrontEnd\CustomerController@create')->name('customer.create');
    Route::post('/store', 'FrontEnd\CustomerController@authentication')->name('customer.authentication');
    Route::post('/store', 'FrontEnd\CustomerController@authentication')->name('customer.authentication');

    /*---socialite---*/
    ##==facebook
    Route::get('auth/facebook', 'FrontEnd\CustomerController@facebookRedirect')->name('auth.facebook');

    #====google
    Route::get('auth/google', 'FrontEnd\CustomerController@googleRedirect')->name('auth.google');
    /*---socialite end---*/



    Route::get('/forget-password', 'FrontEnd\CustomerController@forget_passord')->name('customer.forget.password');
    Route::post('/send-forget-mail', 'FrontEnd\CustomerController@forget_mail')->name('customer.forget.mail');
    Route::get('/reset-password', 'FrontEnd\CustomerController@reset_password')->name('customer.reset.password');
    Route::post('/update-forget-password', 'FrontEnd\CustomerController@update_password')->name('customer.update-forget-password');
  });
  Route::get('/logout', 'FrontEnd\CustomerController@logout')->name('customer.logout');
  Route::get('/change-password', 'FrontEnd\CustomerController@change_password')->name('customer.change.password');
  Route::post('/update-password', 'FrontEnd\CustomerController@updated_password')->name('customer.password.update');
});

Route::get('customer/signup-verify/{token}', 'FrontEnd\CustomerController@signupVerify')->withoutMiddleware('change.lang');

Route::prefix('/customer')->middleware('auth:customer', 'Deactive:customer', 'change.lang', 'EmailStatus:customer')->group(function () {
  Route::get('/dashboard', 'FrontEnd\CustomerController@index')->name('customer.dashboard');
  Route::get('/edit-profile', 'FrontEnd\CustomerController@edit_profile')->name('customer.edit.profile');
  Route::post('/update-profile', 'FrontEnd\CustomerController@update_profile')->name('customer.profile.update');

  Route::get('/wishlist', 'FrontEnd\CustomerController@wishlist')->name('customer.wishlist');
  Route::get('/my-bookings', 'FrontEnd\Event\CustomerBookingController@my_booking')->name('customer.booking.my_booking');
  Route::get('/booking/details/{id}', 'FrontEnd\Event\CustomerBookingController@details')->name('customer.booking_details');

  Route::get('/support-ticket', 'FrontEnd\SupportTicketController@index')->name('customer.support_tickert');
  Route::get('/support-ticket/create', 'FrontEnd\SupportTicketController@create')->name('customer.support_tickert.create');
  Route::post('/support-ticket/store', 'FrontEnd\SupportTicketController@store')->name('customer.support_ticket.store');
  Route::get('/support-ticket/message/{id}', 'FrontEnd\SupportTicketController@message')->name('customer.support_ticket.message');
  Route::post('/support-ticket/reply/{id}', 'FrontEnd\SupportTicketController@reply')->name('customer-reply');

  Route::get('/my-orders', 'FrontEnd\Shop\CustomerOrderController@index')->name('customer.my_orders');
  Route::get('/my-orders/details/{id}', 'FrontEnd\Shop\CustomerOrderController@details')->name('customer.order_details');
});


/*
|---------------------------------------------------------------------------------
| Customer routes end
|---------------------------------------------------------------------------------
*/

/*
|---------------------------------------------------------------------------------
| event booking routes are goes here
|---------------------------------------------------------------------------------
*/
Route::middleware('change.lang')->group(function () {
  Route::post('/ticket-booking/{id}', 'FrontEnd\Event\BookingController@index')->name('ticket.booking');
  Route::get('/event-booking/{id}/cancel', 'FrontEnd\Event\BookingController@cancel')->name('event_booking.cancel');
  Route::get('/event-booking-complete', 'FrontEnd\Event\BookingController@complete')->name('event_booking.complete');
});


Route::prefix('event-booking')->group(function () {
  Route::get('/paypal/notify', 'FrontEnd\PaymentGateway\PayPalController@notify')->name('event_booking.paypal.notify');
  Route::get('/paypal/cancel', 'FrontEnd\PaymentGateway\PayPalController@cancel')->name('event_booking.cancel');

  Route::post('/apply-coupon', 'FrontEnd\EventController@applyCoupon')->name('apply-coupon');

  Route::get('/instamojo/notify', 'FrontEnd\PaymentGateway\InstamojoController@notify')->name('event_booking.instamojo.notify');

  Route::get('/paystack/notify', 'FrontEnd\PaymentGateway\PaystackController@notify')->name('event_booking.paystack.notify');

  Route::post('/flutterwave/notify', 'FrontEnd\PaymentGateway\FlutterwaveController@notify')->name('event_booking.flutterwave.notify');

  Route::post('/razorpay/notify', 'FrontEnd\PaymentGateway\RazorpayController@notify')->name('event_booking.razorpay.notify');

  Route::get('/mercadopago/notify', 'FrontEnd\PaymentGateway\MercadoPagoController@notify')->name('event_booking.mercadopago.notify');

  Route::get('/mollie/notify', 'FrontEnd\PaymentGateway\MollieController@notify')->name('event_booking.mollie.notify');

  Route::post('/paytm/notify', 'FrontEnd\PaymentGateway\PaytmController@notify')->name('event_booking.paytm.notify');

  Route::get('make-payment', 'FrontEnd\PaymentGateway\MidtransController@makePayment')->name('makePayment');
  Route::get('notify/{orderId}?', 'FrontEnd\PaymentGateway\MidtransController@ccNotify')->name('event.midtrans.notify'); // credit card er IPN
  Route::get('bank-notify', 'FrontEnd\PaymentGateway\MidtransController@bankNotify')->name('bank.notify'); // banking er IPN

  //iyzico
  Route::get('make-payment', 'FrontEnd\PaymentGateway\IyzipayController@makePayment');
  Route::post('/iyzico/notify', 'FrontEnd\PaymentGateway\IyzipayController@notify')->name('event_booking.iyzico.notify');
  //iyzico end

  //toyyibpay
  Route::get('/toyyibpay/notify', 'FrontEnd\PaymentGateway\ToyyibpayController@notify')->name('event_booking.toyyibpay.notify');

  //paytabs
  Route::get('/paytabs/make-payment', 'FrontEnd\PaymentGateway\PaytabsController@makePayment');

  Route::post('/paytabs/notify', 'FrontEnd\PaymentGateway\PaytabsController@notify')->name('event_booking.paytabs.notify');

  Route::any('/phonepe/notify', 'FrontEnd\PaymentGateway\PhonepeController@notify')->name('event_booking.phonepe.notify');
  //paytabs end

  //yoco
  Route::get('/yoco/notify', 'FrontEnd\PaymentGateway\YocoController@notify')->name('event_booking.yoco.notify');
  //xindit
  Route::get('/xendit/notify', 'FrontEnd\PaymentGateway\XenditController@notify')->name('event_booking.xindit.notify');

  //perfect money
  Route::get('/perfect-money/notify', 'FrontEnd\PaymentGateway\PerfectMoneyController@notify')->name('event_booking.perfect-money.notify');

  Route::get('/perfect-money/cancel', 'FrontEnd\PaymentGateway\PerfectMoneyController@cancel')->name('event_booking.perfect-money.cancel');
});
/*
|---------------------------------------------------------------------------------
| Event Booking payment routes are end
|---------------------------------------------------------------------------------
*/


Route::post('/push-notification/store-endpoint', 'FrontEnd\PushNotificationController@store');

Route::get('/change-language', 'Controller@changeLanguage')->name('change_language');

Route::post('/store-subscriber', 'Controller@storeSubscriber')->name('store_subscriber');

/*
|---------------------------------------------------------------------------------
| Frontend pages routes are goes here
|---------------------------------------------------------------------------------
*/

Route::middleware('change.lang')->group(function () {
  Route::get('/', 'FrontEnd\HomeController@index')->name('index');
  Route::get('events', 'FrontEnd\EventController@index')->name('events');
  Route::get('event/{slug}/{id}', 'FrontEnd\EventController@details')->name('event.details');
  Route::get('addto/wishlist/{id}', 'FrontEnd\EventController@add_to_wishlist')->name('addto.wishlist');
  Route::get('remove/wishlist/{id}', 'FrontEnd\CustomerController@remove_wishlist')->name('remove.wishlist');

  Route::post('/check-out2', 'FrontEnd\CheckOutController@checkout2')->name('check-out2');
  Route::get('/checkout', 'FrontEnd\CheckOutController@checkout')->name('check-out');

  Route::middleware('change.lang')->prefix('shop')->group(function () {
    Route::get('/', 'FrontEnd\Shop\ShopController@index')->name('shop');
    Route::get('/details/{slug}/{id}', 'FrontEnd\Shop\ShopController@details')->name('shop.details');
    Route::post('review-submit', 'FrontEnd\Shop\ShopController@review')->name('product.review.submit');
    Route::get('add-to-cart/{id}', 'FrontEnd\Shop\ShopController@addToCart')->name('add.cart');
    Route::get('add-to-cart-2/{id}/{qty?}', 'FrontEnd\Shop\ShopController@addToCart2')->name('add.cart2');

    Route::post('order-now', 'FrontEnd\Shop\ShopController@orderNow')->name('order-now');

    Route::get('cart/', 'FrontEnd\Shop\ShopController@cart')->name('shopping.cart');
    Route::get('cart/item/remove/{id}', 'FrontEnd\Shop\ShopController@cartitemremove')->name('cart.item.remove');
    Route::post('cart/update', 'FrontEnd\Shop\ShopController@updatecart')->name('cart.update');
    Route::get('checkout', 'FrontEnd\Shop\ShopController@checkout')->name('shop.checkout');
    Route::post('apply-coupon/', 'FrontEnd\Shop\ShopController@applyCoupon')->name('shop.apply-coupon');
    Route::post('buy/', 'FrontEnd\Shop\OrderController@enrol')->name('shop.buy');
  });

  Route::get('/product-order/{id}/cancel', 'FrontEnd\Shop\OrderController@cancel')->name('product_order.cancel');
  Route::get('/product-order-complete/complete/{via?}', 'FrontEnd\Shop\OrderController@complete')->name('product_order.complete');
  Route::get('organizer/details/{id}/{name}', 'FrontEnd\OrganizerController@details')->name('frontend.organizer.details');
  Route::get('organizers/', 'FrontEnd\OrganizerController@index')->name('frontend.all.organizer');

  Route::post('organizers/contact/send-mail', 'FrontEnd\OrganizerController@sendMail')->name('organizer.contact.send_mail');
});
/*
|---------------------------------------------------------------------------------
| Frontend pages routes are end
|---------------------------------------------------------------------------------
*/


/*
||===================================================
|| Product order routes are goes here
||===================================================
*/

Route::prefix('product-order')->group(function () {
  Route::get('paypal/notify', 'FrontEnd\Shop\PaymentGateway\PaypalController@notify')->name('product_order.paypal.notify');
  Route::get('paypal/cancel', 'FrontEnd\Shop\PaymentGateway\PaypalController@cancel')->name('product_order.cancel');
  Route::get('paystack/notify', 'FrontEnd\Shop\PaymentGateway\PaystackController@notify')->name('product_order.paystack.notify');
  Route::get('instamojo/notify', 'FrontEnd\Shop\PaymentGateway\InstamojoController@notify')->name('product_order.instamojo.notify');
  Route::post('razorpay/notify', 'FrontEnd\Shop\PaymentGateway\RazorpayController@notify')->name('product_order.razorpay.notify');
  Route::post('mercadopago/notify', 'FrontEnd\Shop\PaymentGateway\MercadoPagoController@notify')->name('product_order.mercadopago.notify');
  Route::get('mollie/notify', 'FrontEnd\Shop\PaymentGateway\MollieController@notify')->name('product_order.mollie.notify');
  Route::post('paytm/notify', 'FrontEnd\Shop\PaymentGateway\PaytmController@notify')->name('product_order.paytm.notify');

  Route::post('flutterwave/notify', 'FrontEnd\Shop\PaymentGateway\FlutterwaveController@notify')->name('product_order.flutterwave.notify');

  Route::get('make-payment', 'FrontEnd\Shop\PaymentGateway\MidtransController@makePayment')->name('shop.makePayment');
  Route::get('notify/{orderId}', 'FrontEnd\Shop\PaymentGateway\MidtransController@ccNotify')->name('shop.midtrans.notify'); // credit card er IPN
  Route::get('bank-notify', 'FrontEnd\Shop\PaymentGateway\MidtransController@bankNotify')->name('shop.bank.notify'); // banking er IPN

  //paytabs
  Route::get('/paytabs/make-payment', 'FrontEnd\Shop\PaymentGateway\PaytabsController@makePayment')->name('shop.paytabs.makePayment');
  Route::post('/paytabs/notify', 'FrontEnd\Shop\PaymentGateway\PaytabsController@notify')->name('shop.paytabs.notify');

  Route::get('/toyyibpay/notify', 'FrontEnd\Shop\PaymentGateway\ToyyibpayController@notify')->name('shop.toyyibpay.notify');

  //phonepe
  Route::any('/phonepe/notify', 'FrontEnd\Shop\PaymentGateway\PhonepeController@notify')->name('shop.phonepe.notify');

  //yoco
  Route::get('/yoco/notify', 'FrontEnd\Shop\PaymentGateway\YocoController@notify')->name('shop.yoco.notify');

  // xendit
  Route::get('/xendit/notify', 'FrontEnd\Shop\PaymentGateway\XenditController@notify')->name('shop.xendit.notify');

  Route::post('/iyzico/notify', 'FrontEnd\Shop\PaymentGateway\IyzipayController@notify')->name('shop.iyzico.notify');

  //perfect money
  Route::get('/perfect-money/notify', 'FrontEnd\Shop\PaymentGateway\PerfectMoneyController@notify')->name('shop.perfect-money.notify');

  Route::get('/perfect-money/cancel', 'FrontEnd\Shop\PaymentGateway\PerfectMoneyController@cancel')->name('shop.perfect-money.cancel');
});


/*
|---------------------------------------------------------------------------------
| Product order routes are end
|---------------------------------------------------------------------------------
*/




Route::middleware('change.lang')->group(function () {

  Route::get('/blog', 'FrontEnd\BlogController@blogs')->name('blogs');

  Route::get('/blog/{slug}', 'FrontEnd\BlogController@details')->name('blog_details');

  Route::get('/faq', 'FrontEnd\FaqController@faqs')->name('faqs');

  Route::get('/contact', 'FrontEnd\ContactController@contact')->name('contact');
  Route::get('/about-us', 'FrontEnd\HomeController@about')->name('about');
});

Route::post('/contact/send-mail', 'FrontEnd\ContactController@sendMail')->name('contact.send_mail');

Route::post('/advertisement/{id}/total-view', 'Controller@countAdView');



// service unavailable route
Route::get('/service-unavailable', 'Controller@serviceUnavailable')->name('service_unavailable')->middleware('exists.down');

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('/admin')->middleware('guest:admin')->group(function () {
  // admin redirect to login page route
  Route::get('/', 'BackEnd\AdminController@login')->name('admin.login');

  // admin login attempt route
  Route::post('/auth', 'BackEnd\AdminController@authentication')->name('admin.auth');

  // admin forget password route
  Route::get('/forget-password', 'BackEnd\AdminController@forgetPassword')->name('admin.forget_password');

  // send mail to admin for forget password route
  Route::post('/mail-for-forget-password', 'BackEnd\AdminController@sendMail')->name('admin.mail_for_forget_password');
});


/*
|--------------------------------------------------------------------------
| Custom Page Route For UI
|--------------------------------------------------------------------------
*/
Route::get('/{slug}', 'FrontEnd\PageController@page')->name('dynamic_page')->middleware('change.lang');

// fallback route
Route::fallback(function () {
  return view('errors.404');
})->middleware('change.lang');

// Organizer Event Booking Routes
Route::group(['middleware' => ['auth:organizer'], 'prefix' => 'organizer', 'as' => 'organizer.'], function () {
    Route::get('event/booking', 'App\Http\Controllers\Organizer\EventBookingController@index')->name('event.booking');
    Route::get('event/booking/create', 'App\Http\Controllers\Organizer\EventBookingController@create')->name('event.booking.create');
    Route::post('event/booking/store', 'App\Http\Controllers\Organizer\EventBookingController@store')->name('event.booking.store');
    Route::get('event/tickets', 'App\Http\Controllers\Organizer\EventBookingController@getTickets')->name('event.tickets');
    
    // Customer Management
    Route::post('customer/store', 'Organizer\CustomerController@store')->name('customer.store');
});

Route::post('/send-sms', 'SMSController@sendSMS')->name('send.sms');




