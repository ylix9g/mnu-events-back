<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;

class EventsController extends Controller
{
    public function event($eventId): JsonResponse
    {
        $event = Event::with('sectors')->findOrFail($eventId);
        return response()->json($event);
    }

    public function createEvent(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'main' => ['required', 'bool'],
            'category_id' => ['required', 'int'],
            'image' => ['required', 'string'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string'],
            'limit' => ['required', 'int'],
            'start' => ['required', 'date'],
            'with_sectors' => ['required', 'bool'],
            'sectors.*.name' => ['required', 'string'],
            'sectors.*.limit' => ['required', 'int'],
        ]);
        $category = Category::findOrFail($validated['category_id']);
        $event = $category->events()->create([
            'main' => $validated['main'],
            'with_sectors' => $validated['with_sectors'],
            'image' => $validated['image'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'location' => $validated['location'],
            'limit' => $validated['limit'],
            'start' => $validated['start'],
        ]);
        if ($validated['with_sectors']) {
            foreach ($validated['sectors'] as $sector) {
                $event->sectors()->create([
                    'name' => $sector['name'],
                    'limit' => $sector['limit'],
                ]);
            }
        }
        return response()->json();
    }

    public function closestMain(): JsonResponse
    {
        $closestMainEvent = Event::with('sectors')
            ->with('sectors')
            ->where('main', true)
            ->where('start', '>=', now())
            ->orderBy('start')
            ->first();
        return response()->json($closestMainEvent);
    }

    public function groupedByCategory(): JsonResponse
    {
        $currentDay = now()->startOfDay();
        $events = Event::with('sectors')->where('start', '>=', $currentDay)->orderBy('start')->get();
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
        $events = Event::with('sectors')
            ->whereYear('start', $validated['year'])
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
