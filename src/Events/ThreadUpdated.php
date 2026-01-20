<?php

namespace Tightenco\Lectern\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tightenco\Lectern\Models\Thread;

class ThreadUpdated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Thread $thread,
    ) {}
}
