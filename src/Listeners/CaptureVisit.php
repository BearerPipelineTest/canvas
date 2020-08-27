<?php

namespace Canvas\Listeners;

use Canvas\Events\PostViewed;
use Canvas\Helpers\URL;
use Canvas\Models\Post;

class CaptureVisit
{
    /**
     * A visit is captured when a user loads a post for the first time
     * in a given day. The ID of the post and the IP associated with
     * the request are stored in session to be validated against.
     *
     * @param PostViewed $event
     * @return void
     */
    public function handle(PostViewed $event)
    {
        $ip = request()->getClientIp();

        if ($this->visitIsUnique($event->post, $ip)) {
            $referer = request()->header('referer');

            $data = [
                'post_id' => $event->post->id,
                'ip' => $ip,
                'agent' => request()->header('user_agent'),
                'referer' => URL::isValid($referer) ? URL::trim($referer) : false,
            ];

            $event->post->visits()->create($data);

            $this->storeInSession($event->post, $ip);
        }
    }

    /**
     * Check if a given post and IP are unique to the session.
     *
     * @param Post $post
     * @param string $ip
     * @return bool
     */
    private function visitIsUnique(Post $post, string $ip): bool
    {
        $visits = session()->get('visited_posts', []);

        if (array_key_exists($post->id, $visits)) {
            $visit = $visits[$post->id];

            return $visit['ip'] != $ip;
        } else {
            return true;
        }
    }

    /**
     * Add a given post and IP to the session.
     *
     * @param Post $post
     * @param string $ip
     * @return void
     */
    private function storeInSession(Post $post, string $ip)
    {
        session()->put("visited_posts.{$post->id}", [
            'timestamp' => now()->timestamp,
            'ip' => $ip,
        ]);
    }
}