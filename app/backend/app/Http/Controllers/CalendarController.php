<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $calendars[] = [
            "id" => 1,
            "start" => date('Y-m-d') . "T00:00:00", //"2021-09-10T10:00:00"
            "end" => date('Y-m-d') . "T00:00:00", //"2021-09-10T10:00:00"
            "text" => "Event Teste",
            "backColor" => "#6aa84f",
            "borderColor" => "#38761d"
        ];
        
        $calendars[] = [
            "id" => 2,
            "start" => "2023-02-28T13:00:00",
            "end" => "2025-02-28T16:00:00",
            "text" => "Event Teste 2",
            "backColor" => "#f1c232",
            "borderColor" => "#bf9000",
        ];

        return response()->json([
            'success' => !!$calendars,
            'data' => $calendars
        ]);
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
    public function show(Calendar $calendar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Calendar $calendar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Calendar $calendar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Calendar $calendar)
    {
        //
    }
}
