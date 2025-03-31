<?php

namespace App\Http\Controllers\Api\V1;

use App\Filters\CompanyFilters;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CreateCompanyRequest;
use App\Http\Requests\Api\V1\UpdateCompanyRequest;
use App\Models\Company;
use App\Services\PhoneValidationService;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompaniesController extends Controller
{
    use Responses;

    /**
     * Create the controller instance.
     */
    public function __construct(
        protected CompanyFilters $companyFilters,
        protected PhoneValidationService $phoneValidationService
    ) {
        $this->authorizeResource(Company::class, 'company');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = $this->companyFilters->applyFilters(Company::query())->get();
        return $this->indexOrShowResponse('companies', $companies);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateCompanyRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $formattedPhone = $this->phoneValidationService->formatPhoneNumber($request->phone);
            $user = Auth::user();

            $user->companies()->create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $formattedPhone,
                'country_id' => $request->country_id,
                'industry_id' => $request->industry_id,
                'phone_verified_at' => now()
            ]);

            return $this->sudResponse('Company created successfully.', 201);
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        return $this->indexOrShowResponse('Company', $company);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        return DB::transaction(function () use ($request, $company) {
            $user = Auth::user();
            $company->update($request->validated());
            return $this->sudResponse('Company updated successfully.');
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Company $company)
    {
        return DB::transaction(function () use ($company) {
            $company->delete();
            return $this->sudResponse('Company Deleted Successfully');
        });
    }
}
