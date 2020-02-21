<?php

namespace App;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
//use Illuminate\Database\Eloquent\Model;

class Book extends Eloquent
{
    protected $connection = 'mongodb';
	protected $collection = 'books';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'detail', 'user_id'
    ];

    public function user()
  {
    return $this->belongsTo('App\User');
  }
}
