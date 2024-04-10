<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class EventsController extends Controller
{
    public function createEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'main' => ['required', 'bool'],
            'category_id' => ['required', 'int'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string'],
            'limit' => ['required', 'int'],
            'start' => ['required', 'date'],
        ]);
        $category = Category::findOrFail($validated['category_id']);
        $category->events()->create([
            'main' => $validated['main'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'limit' => $validated['limit'],
            'start' => $validated['start'],
        ]);
        return response()->json([]);
    }

    public function groupedByCategory(): JsonResponse
    {
        $currentDay = now()->startOfDay();
        $events = Event::where('start', '>=', $currentDay)->orderBy('start')->get();
        $grouped = $events->groupBy('category.name');
        return response()->json($grouped);
    }

    public function byDate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'year' => ['required', 'int'],
            'month' => ['required', 'int'],
            'withEvent' => ['required', 'bool'],
        ]);
        $events = Event::whereYear('start', $validated['year'])
            ->whereMonth('start', $validated['month'])
            ->get();
        $daysInMonth = Date::create($validated['year'], $validated['month'])->daysInMonth;
        $result = [];
        if (!$validated['withEvent']) {
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $result[$day] = null;
            }
        }
        foreach ($events as $event) {
            $result[$event->start->day] = $event;
        }
        return response()->json($result);
    }
}
