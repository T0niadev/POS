<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerStoreRequest;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->wantsJson()) {
            return response(
                Customer::all()
            );
        }
        $customers = Customer::latest()->paginate(10);
        return view('customers.index')->with('customers', $customers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('customers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CustomerStoreRequest $request)
    {
        $avatar_path = '';

        // if ($request->hasFile('avatar')) {
        //     $avatar_path = $request->file('avatar')->store('customers', 'public');
        // }


        if ($request->hasFile("avatar")) {
            $file = $request->avatar;
            $avatarName = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('avatar'), $avatarName);
            $title = $request->title;
        }    

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'avatar' => $avatarName,
            // 'user_id' => $request->user()->id,
        ]);

        if (!$customer) {
            return redirect()->back()->with('error', 'Sorry, there\'re a problem while creating customer.');
        }
        return redirect()->route('customers.index')->with('success', 'Success, your customer have been created.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {
        $customer->first_name = $request->first_name;
        $customer->last_name = $request->last_name;
        $customer->email = $request->email;
        $customer->phone = $request->phone;
        $customer->address = $request->address;

        // if ($request->hasFile('avatar')) {
        //     // Delete old avatar
        //     if ($customer->avatar) {
        //         Storage::delete($customer->avatar);
        //     }
        //     // Store avatar
        //     $avatar_path = $request->file('avatar')->store('customers', 'public');
        //     // Save to Database
        //     $customer->avatar = $avatar_path;
        // }



        if ($request->hasFile("avatar")) {
            // unlink(public_path('images/' . $customer->avatar));
            $file = $request->avatar;
            $avatarName = time() . "_" . $file->getClientOriginalName();
            $file->move(public_path('images/'), $avatarName);
            $customer->avatar = $avatarName;
        }

        if (!$customer->save()) {
            return redirect()->back()->with('error', 'Sorry, there\'re a problem while updating customer.');
        }
        return redirect()->route('customers.index')->with('success', 'Success, your customer have been updated.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->avatar) {
            Storage::delete($customer->avatar);
        }

        $customer->delete();

       return response()->json([
           'success' => true
       ]);
    }
}
