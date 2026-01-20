<?php

namespace Tightenco\Lectern\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Tightenco\Lectern\Models\Mention;
use Tightenco\Lectern\Models\Post;

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
