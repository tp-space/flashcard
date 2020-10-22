<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $table = "label";

    public function cards()
    {
        return $this->belongsToMany(
            Card::class,
            'card_label_mapping',
            'label_id',
            'card_id',
        );
    }


}
