<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Actions\Customers\ListCustomers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class ListCustomerController extends Controller
{
    //
    public function __invoke(Request $request, ListCustomers $listCustomers)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $direction = $request->query('direction', 'desc');

        $customers = $listCustomers->handle($sortBy, $direction);

        return Inertia::render('Customer/List', [
            'customers_data' => $customers,
            'sort_by' => $sortBy,
            'direction' => $direction,
        ]);
    }
}
