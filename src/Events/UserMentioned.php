<?php

namespace Tighten\Lectern\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tighten\Lectern\Models\Mention;
use Tighten\Lectern\Models\Post;

class UserMentioned
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Mention $mention,
        public Post $post,
        public mixed $user,
    ) {}
}
