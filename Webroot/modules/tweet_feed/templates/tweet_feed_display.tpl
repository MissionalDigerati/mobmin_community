{foreach from=$tweets item=tweet}
    <div class="post-box resizable-video col-md-12">
        <div class="powered-by-box"><img src="{$my_base_url}{$my_pligg_base}/modules/tweet_feed/assets/img/twitter/bird_blue_16.png" alt=""> Powered by Twitter</div>
        <div class="profile-box">
            <a href="https://twitter.com/{$tweet.tweeter_name}" target="_blank">
                <img alt="{$tweet.tweeter_name}" class="profile-img" src="{$tweet.tweeter_avatar_url}">
            </a>
            <div class="details">
                <a href="https://twitter.com/{$tweet.tweeter_name}" target="_blank" class="profile-name">{$tweet.tweeter_name}</a><br>
                <a href="https://twitter.com/search?q=%23MobMin" target="_blank">#MobMin</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="tweet-content">{$tweet.content}</div>
        <div class="date-box">{$tweet.published_date|date_format:"%l:%M %p - %e %B %Y"}</div>
        <div class="twitter-action-box">
            <a href="https://twitter.com/intent/tweet?in_reply_to={$tweet.tweet_id}"><i class="socialicon-twit-reply" title="reply"></i></a> 
            <a href="https://twitter.com/intent/retweet?tweet_id={$tweet.tweet_id}"><i class="socialicon-twit-retweet" title="retweet"></i></a> 
            <a href="https://twitter.com/intent/favorite?tweet_id={$tweet.tweet_id}"><i class="socialicon-twit-fav" title="favorite"></i></a>
        </div>
    </div>
    <div class="clearfix"></div>
{/foreach}
<br>