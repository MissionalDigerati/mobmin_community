var totalStories = 0;
$(document).ready(function($) {
    if ($('div.tweet-content').length > 0) {
        convertAllTweets();
        totalStories = $('div.stories').length;
        setInterval(function(){checkStories()}, 3000);
    };
});
function convertAllTweets() {
    $('div.tweet-content').each(function(index, el) {
        $(this).html(twttr.txt.autoLink($(this).text()));
    });
};
function checkStories() {
    var currentStories = $('div.stories').length;
    if (currentStories > totalStories) {
        convertAllTweets();
        totalStories = currentStories;
    };
};