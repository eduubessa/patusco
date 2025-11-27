<?php

namespace App\Http\Controllers\Animal;

use App\Http\Controllers\Controller;
use App\Models\Animal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShowAnimalController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, string $id)
    {
        //
        $animal = Animal::with(['doctor', 'owners', 'appointments.doctor', 'appointments' => function ($query) {
            $query->latest()->take(5);
        }])->findOrFail($id);

        return Inertia::render('Animal/Show', [
            'animal' => $animal,
        ]);
    }
}
