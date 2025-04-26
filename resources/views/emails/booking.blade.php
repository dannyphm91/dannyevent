<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-2xl mx-auto p-8">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Booking Confirmation</h1>
                <p class="text-gray-600">Your booking has been successfully confirmed</p>
            </div>

            <div class="border-t border-gray-200 pt-6">

                <div class="text-center">
                    <a href="{{ asset('assets/admin/file/invoices/' . $booking->invoice) }}" 
                       class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition duration-300">
                        Download Invoice
                    </a>
                </div>
            </div>

            <div class="mt-8 text-center text-gray-600">
                <p>Thank you for your booking!</p>
                <p class="text-sm mt-2">If you have any questions, please contact our support team.</p>
            </div>
        </div>
    </div>
</body>
</html> 