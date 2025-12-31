<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model{
    protected $fillable = [
        'business_id',
        'user_id',
        'manager_id',
        'worker_identifier',
        'job_title',
        'salary',
        'salary_type',
        'status',
    ];

    public function business(){
        return $this->belongsTo(Business::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function manager(){
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function employees(){
        return $this->hasMany(Employee::class,'manager_id');
    }

    public function attendanceEvents(){
        return $this->hasMany(AttendanceEvent::class);
    }

    public function isClockedIn(): bool {
        $ins = $this->attendanceEvents()->where('type', 'IN')->count();
        $outs = $this->attendanceEvents()->where('type','OUT')->count();

        return $ins > $outs;
    }

}