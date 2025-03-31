<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\InquiryRequest;
use App\Models\Inquiry;
use App\Services\PhoneValidationService;
use App\Traits\Responses;
use Illuminate\Support\Facades\DB;

class InquiriesController extends Controller
{
    use Responses;

    public function __construct(
        protected PhoneValidationService $phoneValidationService
    ) {
    }

    /**
     * Store a newly created inquiry.
     */
    public function store(InquiryRequest $request)
    {
        return DB::transaction(function () use ($request) {
            // Format phone number before storing
            $formattedPhone = $this->phoneValidationService->formatPhoneNumber($request->phone);

            $inquiry = Inquiry::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $formattedPhone,
                'message' => $request->message,
                'company_id' => $request->company_id,
            ]);

            return $this->sudResponse('Inquiry submitted successfully.', 201);
        });
    }
}
