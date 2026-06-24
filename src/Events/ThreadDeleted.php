<?php

namespace Tighten\Lectern\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tighten\Lectern\Models\Thread;

class ThreadDeleted
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Thread $thread,
    ) {}
}
