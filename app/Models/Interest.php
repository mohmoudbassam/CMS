<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
	public $timestamps = false;
	protected $table = 'interests';
	protected $fillable = ['name'];

}
