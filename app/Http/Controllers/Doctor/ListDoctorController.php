<?php

declare(strict_types=1);

namespace App\Http\Controllers\Doctor;

use App\Actions\Doctors\ListDoctors;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class ListDoctorController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ListDoctors $listDoctors)
    {
        //
        $sortBy = $request->query('sort_by', 'created_at');
        $direction = $request->query('direction', 'desc');

        $doctors = $listDoctors->handle($sortBy, $direction);

        return Inertia::render('Doctor/List', [
            'doctors_data' => $doctors,
            'sort_by' => $sortBy,
            'direction' => $direction,
        ]);
    }
}
