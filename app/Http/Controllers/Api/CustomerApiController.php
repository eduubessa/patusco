<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Helpers\Enums\UserRoles;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\User;
use Illuminate\Http\Request;

final class CustomerApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $searchTerm = $request->query('search');
        $sortBy = $request->query('sort');
        $sortDirection = $request->query('dir');
        $itemsPerPage = $request->query('items_per_page') ?? 10;

        if (empty($searchTerm) || mb_strlen($searchTerm) < 3) {
            $customers = User::Customer()->latest()->get();
        }

        $customers = User::query()
            ->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$searchTerm.'%')
                    ->orWhere('username', 'like', '%'.$searchTerm.'%');

                if (is_numeric($searchTerm)) {
                    $q->orWhere('phone_number', 'like', '%'.$searchTerm.'%');
                }
            })
            ->Customer();

        if ($sortBy && $sortDirection) {
            if (preg_match('(asc|desc)', $sortDirection) !== 1) {
                abort(404);
            }
            $customers = $customers->orderBy($sortBy, $sortDirection);
        }

        $customers = $customers->limit($itemsPerPage)
            ->select('id', 'name', 'username', 'email')
            ->get();

        return CustomerResource::collection($customers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, User $user)
    {
        //
        if ($user->role !== UserRoles::Customer->value) {
            abort(404);
        }

        $customer = $user->load('animals');

        return new CustomerResource($customer);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
