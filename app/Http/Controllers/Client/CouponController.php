<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Service;
use App\Models\CouponService;
use Illuminate\Support\Facades\Validator;

class CouponController extends Controller
{
    // List all coupons
    public function index(Request $request)
    {
        $query = Coupon::query();

        // Apply search filter if provided
        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('coupon_code', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Fetch and paginate coupons
        $coupons = $query->orderBy('id', 'desc')->paginate(10);

        // Return the view with the filtered results
        return view('client.pages.coupons.index', compact('coupons'));
    }

    // Show form to create a new coupon
    public function create()
    {
        $services = Service::where('user_id', getUserID())->orderBy('id', 'desc')->get();
        return view('client.pages.coupons.add', compact('services'));
    }

    // Store new coupon
    public function store(Request $request)
    {
        // Validate the incoming request
        $validatedData = $request->validate([
            'coupon_code' => 'required|string|max:50|unique:coupons',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:Fixed,Percentage',
            'discount_duration' => 'required|in:Forever,First Payment',
            'applies_to' => 'required|array',  // Applies to multiple services
            'discount' => 'required|array',   // Discounts corresponding to services
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'min_cart_amount_value' => 'nullable|numeric|min:0',
        ]);

        // Handle checkbox values
        $limitToOne = $request->has('limit_to_one') ? 1 : 0;
        $limitToNewCustomers = $request->has('limit_to_new_customers') ? 1 : 0;
        $setExpiry = $request->has('set_expiry') ? 1 : 0;
        $minCartAmount = $request->has('min_cart_amount') ? 1 : 0;

        // Create the coupon
        $coupon = Coupon::create([
            'coupon_code' => $validatedData['coupon_code'],
            'description' => $validatedData['description'],
            'discount_type' => $validatedData['discount_type'],
            'discount_duration' => $validatedData['discount_duration'],
            'limit_to_one' => $limitToOne,
            'limit_to_new_customers' => $limitToNewCustomers,
            'set_expiry' => $setExpiry,
            'expiry_date' => $validatedData['expiry_date'] ?? null,
            'min_cart_amount' => $minCartAmount ? $validatedData['min_cart_amount_value'] : null,
            'added_by' => auth()->id(),
        ]);

        // Save coupon services
        foreach ($validatedData['applies_to'] as $index => $serviceIds) {
            // Ensure $serviceIds is an array
            if (!is_array($serviceIds)) {
                $serviceIds = [$serviceIds];
            }

            // Each index in applies_to could have multiple services selected
            foreach ($serviceIds as $serviceId) {
                CouponService::create([
                    'coupon_id' => $coupon->id,
                    'service_id' => $serviceId,
                    'discount' => $validatedData['discount'][$index] ?? 0, // Apply the corresponding discount
                ]);
            }
        }

        return redirect()->route('coupon.list')->with('success', 'Coupon added successfully.');
    }

    // Show form to edit coupon
    public function edit(Coupon $coupon)
    {
        // Get all services for the current user
        $services = Service::where('user_id', getUserID())->orderBy('id', 'desc')->get();

        // Get the associated services with the coupon, including the discount from the pivot table
        $couponServices = $coupon->services->map(function ($service) {
            return [
                'service_id' => $service->pivot->service_id,
                'discount' => $service->pivot->discount
            ];
        });

        return view('client.pages.coupons.edit', compact('coupon', 'services', 'couponServices'));
    }

    // Update coupon
    public function update(Request $request, $id)
    {
        $coupon = Coupon::find($id);

        if (!$coupon) {
            return redirect()->back()->with('error', 'Coupon not found.');
        }

        $validatedData = $request->validate([
            'coupon_code' => 'required|string|max:50|unique:coupons,coupon_code,' . $coupon->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:Fixed,Percentage',
            'discount_duration' => 'required|in:Forever,First Payment',
            'applies_to' => 'required|array',
            'discount' => 'required|array',
            'limit_to_one' => 'nullable|boolean',
            'limit_to_new_customers' => 'nullable|boolean',
            'set_expiry' => 'nullable|boolean',
            'expiry_date' => 'nullable|date|after_or_equal:today',
            'min_cart_amount' => 'nullable|numeric|min:0',
            'min_cart_amount_value' => 'nullable|numeric|min:0',
        ]);

        $limitToOne = $request->has('limit_to_one') ? 1 : 0;
        $limitToNewCustomers = $request->has('limit_to_new_customers') ? 1 : 0;
        $setExpiry = $request->has('set_expiry') ? 1 : 0;
        $minCartAmount = $request->has('min_cart_amount') ? 1 : 0;

        // Update coupon details
        $coupon->update([
            'coupon_code' => $validatedData['coupon_code'],
            'description' => $validatedData['description'],
            'discount_type' => $validatedData['discount_type'],
            'discount_duration' => $validatedData['discount_duration'],
            'limit_to_one' => $validatedData['limit_to_one'] ?? 0,
            'limit_to_new_customers' => $validatedData['limit_to_new_customers'] ?? 0,
            'set_expiry' => $validatedData['set_expiry'] ?? 0,
            'expiry_date' => $validatedData['expiry_date'] ?? null,
            'min_cart_amount' => $minCartAmount ? $validatedData['min_cart_amount_value'] : null,
        ]);

        // Delete old services and discounts associated with the coupon
        CouponService::where('coupon_id', $coupon->id)->delete();

        // Insert new services and discounts
        foreach ($validatedData['applies_to'] as $index => $serviceIds) {
            if (!is_array($serviceIds)) {
                $serviceIds = [$serviceIds];
            }

            foreach ($serviceIds as $serviceId) {
                CouponService::create([
                    'coupon_id' => $coupon->id,
                    'service_id' => $serviceId,
                    'discount' => $validatedData['discount'][$index] ?? 0,
                ]);
            }
        }

        return redirect()->route('coupon.list')->with('success', 'Coupon updated successfully.');
    }


    // Delete coupon
    public function destroy(Coupon $coupon)
    {
        // Delete the coupon services related to this coupon
        CouponService::where('coupon_id', $coupon->id)->delete();

        // Delete the coupon
        $coupon->delete();

        return redirect()->route('coupon.list')->with('success', 'Coupon deleted successfully.');
    }
}
