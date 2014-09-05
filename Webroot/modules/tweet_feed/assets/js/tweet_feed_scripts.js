var totalStories = 0;
var nextPage = '';
var pageUpdating = false;
$(document).ready(function($) {
    if ($('div.tweet-content').length > 0) {
        $('div.edit, div#page-loading-icon').hide();
        convertAllTweets();
        totalStories = $('div.stories').length;
        setInterval(function(){checkStories()}, 3000);
        $(window).scroll(function(){
            if ($(window).scrollTop() == $(document).height() - $(window).height()) {
                if ($('a.next-link').length > 0) {
                    nextPage = $('a.next-link').attr('href');
                    $('div.pagination-holder').remove();
                };
                if ((pageUpdating === false) && (nextPage != null)) {
                    $('div#page-loading-icon').fadeIn('slow');
                    pageUpdating = true;
                    loadTweets();
                };
            }
        });
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
function loadTweets() {
    $.get(nextPage, function(data) {
        $(data).find('div.post-box').each(function(index, el) {
            $('div.tweet-feed-box').append(el);
        });
        $('div#page-loading-icon').fadeOut('slow');
        pagination = $(data).find('ul.pagination a.next-link');
        if (pagination.length > 0) {
            nextPage = pagination.attr('href');
        } else {
            nextPage = null;
        };
        pageUpdating = false;
    });
};