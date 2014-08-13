{if isset($is_tweet) and $is_tweet === true}
        </div>
        <div class="date-box">{$link_published_date|date_format:"%l:%M %p - %e %B %Y"}</div>
        <div class="twitter-action-box">
            <a href="https://twitter.com/intent/tweet?in_reply_to={$social_media_id}"><i class="socialicon-twit-reply" title="reply"></i></a> 
            <a href="https://twitter.com/intent/retweet?tweet_id={$social_media_id}"><i class="socialicon-twit-retweet" title="retweet"></i></a> 
            <a href="https://twitter.com/intent/favorite?tweet_id={$social_media_id}"><i class="socialicon-twit-fav" title="favorite"></i></a>
        </div>
    </div>
    <div class="clearfix"></div>
{/if}