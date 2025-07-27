<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('venue.sections.seats.payment')->get();
        return response()->json($events);
    }

    public function show($id)
    {
        $event = Event::with('venue.sections.seats.payment')->findOrFail($id);
        return response()->json($event);
    }
}
