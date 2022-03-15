<?php

namespace App\Events;

use App\Models\Page;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PageCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The page instance.
     *
     * @var \App\Models\Page
     */
    public $page;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Page $page)
    {
        //
        $this->page = $page;
    }
}
