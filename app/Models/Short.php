<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Interest;
class Short extends Model
{
    protected $table='shorts';
    protected $guarded = [];

    protected $casts = [
        'content' => 'array'
    ];
    public function interest()
    {
        return $this->belongsTo(Interest::class);
    }
    public function media()
    {
        return $this->hasMany(Media::class);
    }
}
