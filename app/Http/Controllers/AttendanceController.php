<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceEvent;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 422);
        }

        if ($employee->isClockedIn()) {
            return response()->json(['message' => 'Already clocked in'], 422);
        }


        AttendanceEvent::create([
            'business_id' => $employee->business_id,
            'employee_id' => $employee->id,
            'type' => 'IN',
            'event_time' => now(),
        ]);

        return response()->json(['message' => 'Clocked in']);
    }


    public function clockOut(Request $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 422);
        }

        $ins = AttendanceEvent::where('employee_id', $employee->id)
            ->where('type', 'IN')
            ->count();

        $outs = AttendanceEvent::where('employee_id', $employee->id)
            ->where('type', 'OUT')
            ->count();

        if ($outs >= $ins) {
            return response()->json(['message' => 'No open session to clock out'], 422);
        }

        AttendanceEvent::create([
            'business_id' => $employee->business_id,
            'employee_id' => $employee->id,
            'type' => 'OUT',
            'event_time' => now(),
        ]);

        return response()->json(['message' => 'Clocked out']);
    }

    public function dailyReport(Request $request)
    {
        $employee = $request->user()->employee;

        if (!$employee) {
            return response()->json(['message' => 'Employee not found'], 422);
        }

        $date = $request->query('date', now()->toDateString());

        $events = AttendanceEvent::where('employee_id', $employee->id)
            ->whereDate('event_time', $date)
            ->orderBy('event_time')
            ->get();

        $totalMinutes = 0;
        $errors = [];

        for ($i = 0; $i < $events->count(); $i++) {
            $current = $events[$i];

            if ($current->type === 'IN') {
                $next = $events[$i + 1] ?? null;

                if (! $next || $next->type !== 'OUT') {
                    $errors[] = "Missing OUT after IN at {$current->event_time}";
                    continue;
                }

                $in  = Carbon::parse($current->event_time);
                $out = Carbon::parse($next->event_time);

                $totalMinutes += $in->diffInMinutes($out);
                $i++; // skip next (OUT)
            }
        }

        return response()->json([
            'date' => $date,
            'total_minutes' => $totalMinutes,
            'total_hours' => sprintf('%02d:%02d', intdiv($totalMinutes, 60), $totalMinutes % 60),
            'events' => $events->map(fn ($e) => [
                'type' => $e->type,
                'time' => Carbon::parse($e->event_time)->format('H:i'),
            ]),
            'errors' => $errors,
        ]);
}


}
