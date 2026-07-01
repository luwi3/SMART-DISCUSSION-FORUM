<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    // 🔑 SDD Requirement: Set custom primary key attributes
    protected $primaryKey = 'regNo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'regNo',
        'user_id',
        'courseCode',
        'status',
        'lastCommDate',
        'banExpiry'
    ];

    // Connection back to the main login User account
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}