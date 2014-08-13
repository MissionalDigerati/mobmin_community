{if isset($is_tweet) and $is_tweet === true}
    <div class="clearfix"></div>
    <div class="post-box resizable-video col-lg-11 col-lg-offset-1">
        <div class="powered-by-box"><img src="{$my_base_url}{$my_pligg_base}/modules/tweet_display/assets/img/twitter/bird_blue_16.png" alt=""> Powered by Twitter</div>
        <div class="profile-box">
            <!-- Image HERE -->
            <div class="details">
                <a href="https://twitter.com/{$social_media_account}" target="_blank" class="profile-name">{$social_media_account}</a><br>
                <a href="https://twitter.com/search?q=%23MobMin" target="_blank">#MobMin</a>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="tweet-content">
{/if}