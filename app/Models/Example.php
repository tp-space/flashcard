<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    use HasFactory;

    protected $table = "example";

    public function cards()
    {
        return $this->belongsToMany(
            Card::class,
            'card_example_mapping',
            'example_id',
            'card_id',
        );
    }


}
