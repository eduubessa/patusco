<?php

namespace App\Http\Controllers\Animal;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CreateAnimalController extends Controller
{

    use AuthorizesRequests;

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        //
        $this->authorize("create", Animal::class);



        return Inertia::render("animals/create", [
            'customers_data' => User::Customer()->get()
        ]);
    }
}
