<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Site extends Model
{
    use HasFactory;
    var $fillable = ["url", "scheme", "host", "max_depth"];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }
}
