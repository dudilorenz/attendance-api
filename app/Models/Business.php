<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $fillable = ['name', 'email', 'status'];

    public function users(){
        return $this->hasMany(User::class);
    }

    public function employees(){
        return $this->hasMany(Employee::class);
    }
}