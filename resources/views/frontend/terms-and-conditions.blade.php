@extends('frontend.layout')

@section('content')
<style>
  .terms-container {
    max-width: 1000px;
    margin: 40px auto;
    padding: 25px 30px;
    background: #f9f9f9;
    border-radius: 8px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #333;
    line-height: 1.6;
  }

  .terms-container h1 {
    text-align: center;
    font-size: 1.9rem;
    margin-bottom: 20px;
    color: #2c3e50;
  }

  .terms-container h2 {
    font-size: 1.3rem;
    margin-top: 25px;
    color: #34495e;
    border-bottom: 2px solid #3498db;
    padding-bottom: 6px;
  }

  .terms-container p {
    margin: 12px 0;
  }

  .terms-container ul {
    list-style: disc inside;
    margin: 12px 0 20px 20px;
    color: #555;
  }

  .terms-container a {
    color: #3498db;
    text-decoration: none;
  }

  .terms-container a:hover {
    text-decoration: underline;
  }
</style>

<div class="terms-container">
  <h1>SMS Messaging Opt-In Terms of Use</h1>

  <p>By verbally opting into our SMS messaging service, you expressly consent to receive text messages from us related to upcoming events, event reminders, updates, announcements, and event-related promotions. You confirm that you have provided this verbal consent willingly during registration, event booking, or through direct communication with our representatives.</p>

  <h2>Message Frequency</h2>
  <p>Message frequency varies depending on event schedules but typically does not exceed 5 messages per month.</p>

  <h2>Opt-Out Instructions</h2>
  <p>To stop receiving messages at any time, reply <strong>"STOP"</strong> to any message received. After texting STOP, you will no longer receive event updates via SMS.</p>

  <h2>Data and Privacy</h2>
  <p>Your privacy is important to us. We will never sell or share your phone number or personal information with third parties for unrelated marketing purposes. All data is securely stored and managed according to standard privacy practices.</p>

  <h2>Support</h2>
  <p>If you have questions or require assistance, reply <strong>"HELP"</strong> to any SMS received, or contact us at <a href="mailto:info@eventlytickets.com">info@eventlytickets.com</a>.</p>

  <p>By opting into our SMS messaging service, you acknowledge understanding these terms and agree to abide by them.</p>
</div>
@endsection

