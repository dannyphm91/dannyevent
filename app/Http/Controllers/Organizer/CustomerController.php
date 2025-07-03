<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    private $admin_user_name;

    public function __construct()
    {
        $admin = Admin::select('username')->first();
        $this->admin_user_name = $admin->username ?? 'admin';
    }

    public function store(Request $request)
    {

        $rules = [
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'username' => [
                'required',
                'alpha_dash',
                "not_in:$this->admin_user_name",
                Rule::unique('customers', 'username')
            ],
            'password' => 'required|confirmed|min:6',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $customerData = [
                'fname' => $request->fname,
                'lname' => $request->lname,
                'email' => $request->email,
                'username' => $request->username,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'address' => $request->address,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'status' => 1,
                'email_verified_at' => now()
            ];


            $customer = Customer::create($customerData);


            return response()->json([
                'status' => 'success',
                'message' => 'Customer created successfully',
                'customer' => [
                    'id' => $customer->id,
                    'fname' => $customer->fname,
                    'lname' => $customer->lname,
                    'email' => $customer->email,
                    'phone' => $customer->phone,
                    'username' => $customer->username
                ]
            ], 200);

        } catch (\Exception $e) {
           
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create customer: ' . $e->getMessage()
            ], 500);
        }
    }
} 