<?php

namespace App\Models;

use App\Events\PageCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    var $fillable = ["path", "base_url", "scheme", "host", "level", "site_id"];

    protected $dispatchesEvents = [
        'created' => PageCreated::class,
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
