<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $table = "card";

    public function labels()
    {
        return $this->belongsToMany(
            Label::class,
            'card_label_mapping',
            'card_id',
            'label_id',
        );
    }


}
