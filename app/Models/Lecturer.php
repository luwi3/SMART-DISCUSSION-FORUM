<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;

    // 🔑 SDD Requirement: Set custom primary key attributes
    protected $primaryKey = 'staffNo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'staffNo',
        'user_id',
        'department'
    ];

    // Connection back to the main login User account
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}