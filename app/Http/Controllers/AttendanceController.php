<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceEventRequest;
use App\Models\AttendanceEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(StoreAttendanceEventRequest $request)
    {
        $data = $request->validated();

        $exists = AttendanceEvent::where('worker_id', $data['workerId'])
            ->where('event_time', $data['event_time'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Duplicate event'
            ], 409);
        }

        AttendanceEvent::create([
            'worker_id'   => $data['workerId'],
            'event_time'  => $data['event_time'],
            'type'        => $data['type'],
        ]);

        return response()->json([
            'message' => 'Event stored successfully'
        ], 201);
    }

    public function report(int $workerId, Request $request)
    {
        $date = $request->query('date');

        if (!$date) {
            return response()->json([
                'message' => 'date query parameter is required'
            ], 422);
        }

        $events = AttendanceEvent::where('worker_id', $workerId)
            ->whereDate('event_time', $date)
            ->orderBy('event_time')
            ->get();

        $totalMinutes = 0;
        $errors = [];

        for ($i = 0; $i < $events->count(); $i++) {
            $current = $events[$i];

            if ($current->type === 'IN') {
                $next = $events[$i + 1] ?? null;

                if (!$next || $next->type !== 'OUT') {
                    $errors[] = 'Missing OUT after IN at ' . $current->event_time;
                    continue;
                }

                $in  = Carbon::parse($current->event_time);
                $out = Carbon::parse($next->event_time);

                $totalMinutes += max(0, $in->diffInMinutes($out));

                $i++;
            }
        }

        return response()->json([
            'workerId'   => $workerId,
            'date'       => $date,
            'totalHours' => sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60),
            'entries'    => $events->map(fn ($e) => [
                'type' => $e->type,
                'time' => Carbon::parse($e->event_time)->format('H:i'),
            ]),
            'errors' => $errors,
        ]);
    }
}
