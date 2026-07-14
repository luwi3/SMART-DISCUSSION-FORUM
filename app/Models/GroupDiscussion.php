<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupDiscussion extends Model
{


protected $fillable = [

'name',
'description',
'user_id'

];


public function user()
{
    return $this->belongsTo(User::class);
}


public function messages()
{
    return $this->hasMany(Message::class);
}


}