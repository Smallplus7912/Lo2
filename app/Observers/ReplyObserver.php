<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;
use Auth;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    // public function creating(Reply $reply){
    //     //XSS安全过滤
    //     $reply->content = clean($reply->contrnt, 'user_topic_body');
    // }
    
    public function created(Reply $reply){
        if ($reply->topic->user->id != Auth::id()) {    //不要通知话题的作者本人
            if (method_exists(TopicReplied::class, 'toDatabase')) {
                $reply->topic->user->increment('notification_count');   //只有数据库类型通知才需提醒，直接发送 Email 或者其他的都 Pass
            }
            $reply->topic->user->notify(new TopicReplied($reply));
        }
        $reply->topic->updateReplyCount();
    }

    public function deleted(Reply $reply)
    {
        $reply->topic->updateReplyCount();
    }

    
}