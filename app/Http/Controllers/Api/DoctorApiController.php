<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class DoctorApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        $period = $request->query('period') ?? null;
        $hour = $request->query('hour') ?? null;
        $date = $request->query('date') ?? null;


        if($period === null || $hour === null || $date === null){
            return response()->json(['data' => ['sem dados']]);
        }

        $availableDoctors = User::doctor()->get()->filter(function($doctor) use ($date, $hour) {
            return Appointment::where('doctor_id', $doctor->id)
                ->where('scheduled_at', $date . ' ' . $hour)
                ->count() < 3;
        });

        return DoctorResource::collection($availableDoctors);

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
    public function show(User $user)
    {
        //
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
