<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Squad extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'squadName',
        'reference'  
    ];
    public function people(){
        return $this->belongsToMany(Person::class);
    }
}
