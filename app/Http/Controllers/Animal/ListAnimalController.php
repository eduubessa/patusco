<?php

declare(strict_types=1);

namespace App\Http\Controllers\Animal;

use App\Actions\Animal\ListAnimals;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class ListAnimalController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ListAnimals $listAnimals)
    {
        $sortBy = $request->query('sort_by', 'created_at');
        $direction = $request->query('direction', 'desc');

        $animals = $listAnimals->handle($sortBy, $direction);

        return Inertia::render('Animal/List', [
            'animals_data' => $animals,
            'sort_by' => $sortBy,
            'direction' => $direction,
        ]);
    }
}
