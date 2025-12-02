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
    public function __invoke(Request $request, Animal $animal)
    {
        //
        $animal = $animal->load('doctor', 'owners', 'appointments', 'appointments.doctor');

        return Inertia::render('Animal/Show', [
            'animal' => $animal,
            'breadcrumbs' => [
                'title' => 'Animais',
                'url' => route('animals.list')
            ]
        ]);
    }
}
