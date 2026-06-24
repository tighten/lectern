<?php

namespace Tighten\Lectern\Services;

use Illuminate\Support\Collection;
use Tighten\Lectern\Events\UserMentioned;
use Tighten\Lectern\Models\Mention;
use Tighten\Lectern\Models\Post;

class MentionParser
{
    public function parse(Post $post): Collection
    {
        if (! config('lectern.mentions.enabled')) {
            return collect();
        }

        $pattern = config('lectern.mentions.pattern');
        $userModel = config('lectern.user.model');
        $displayNameAttribute = config('lectern.user.display_name_attribute');

        preg_match_all($pattern, $post->body, $matches);

        if (empty($matches[1])) {
            return collect();
        }

        $usernames = array_unique($matches[1]);

        $users = $userModel::query()
            ->whereIn($displayNameAttribute, $usernames)
            ->where('id', '!=', $post->user_id)
            ->get();

        $mentions = collect();

        foreach ($users as $user) {
            $mention = Mention::firstOrCreate([
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);

            if ($mention->wasRecentlyCreated) {
                event(new UserMentioned($mention, $post, $user));
            }

            $mentions->push($mention);
        }

        return $mentions;
    }

    public function syncMentions(Post $post): Collection
    {
        $post->mentions()->delete();

        return $this->parse($post);
    }
}
