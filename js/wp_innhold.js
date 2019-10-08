jQuery(document).on('click', '.doLike', function() {
    console.log('doLike');

    var article = jQuery(this).parents('.likeAction');
    var icon = article.find('.likeIcon');
    var liked = icon.attr('src').indexOf('icon-like-full') > 0;
    var countContainer = article.find('.likeCount');

    if (liked) {
        icon.attr('src', icon.attr('src').replace('icon-like-full', 'icon-like-medium'));
    } else {
        icon.attr('src', icon.attr('src').replace('icon-like-medium', 'icon-like-full'));
    }

    jQuery.post(
        ajaxurl, {
            'action': 'UKMwp_innhold_ajax',
            'blog_id': article.attr('data-blog-id'),
            'post_id': article.attr('data-post-id'),
            'newsAction': liked ? 'dislike' : 'like',
        },
        function(response) {
            if (response.success) {
                countContainer.html(response.newCount);
                return;
            }
            alert('Beklager, kunne ikke lagre.');
            if (liked) {
                icon.attr('src', icon.attr('src').replace('icon-like-medium', 'icon-like-full'));
            } else {
                icon.attr('src', icon.attr('src').replace('icon-like-full', 'icon-like-medium'));
            }
        }
    );
});