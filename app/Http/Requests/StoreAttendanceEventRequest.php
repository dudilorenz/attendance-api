<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttendanceEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "workerId"  => ["required", "integer"],
            "event_time"      => ["required", "date"],
            "type"      => ["required", "in:IN,OUT"],
        ];
    }

    public function messages()
    {
        return [
            'workerId.required' => 'workerId is required',
            'workerId.integer'  => 'workerId must be an integer',

            'event_time.required' => 'time is required',
            'event_time.date'     => 'time must be a valid datetime',

            'type.required' => 'type is required',
            'type.in'       => 'type must be IN or OUT',
        ];
    }

}
