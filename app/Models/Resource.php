<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    // Defines the database table associated with course uploads
    protected $table = 'resources';

    // Protects attributes from mass-assignment vulnerabilities
    protected $fillable = [
        'title',
        'file_path',
        'uploaded_by',
    ];

    /**
     * Relationship: Get the user/lecturer who uploaded the resource.
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}