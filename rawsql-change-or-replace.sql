/* Change the destination URL of a WordPress site. */
UPDATE wp_options SET option_value = replace(option_value, 'http://www.oldurl.com', 'http://www.newurl.com') WHERE option_name = 'home' OR option_name = 'siteurl';
UPDATE wp_posts SET guid = replace(guid, 'http://www.oldurl.com','http://www.newurl.com');
UPDATE wp_posts SET post_content = replace(post_content, ' http://www.oldurl.com ', ' http://www.newurl.com ');

/* Search and replace post/page content by string. */
UPDATE wp_posts SET post_content = REPLACE (post_content, 'original', 'replace_with');

/* Change username */
UPDATE wp_users SET user_login = 'new_username' WHERE user_login = 'username';

/* Reset user password */
UPDATE wp_users SET user_pass = MD5('password') WHERE user_login = 'username' LIMIT 1;

/* Change associated author on post/page */
UPDATE wp_posts SET post_author=(SELECT ID FROM wp_users WHERE user_login = 'username' LIMIT 1) WHERE post_author=(SELECT ID FROM wp_users WHERE user_login = 'username' LIMIT 1);

/* Change post type (post to page, etc) */
UPDATE wp_posts SET post_type = 'new_type' WHERE post_type = 'old_type';

/* Enable/Disable pingbacks & trackbacks before a certain date */
UPDATE wp_posts SET ping_status = 'closed|open' WHERE post_date < '2012-01-01' AND post_status = 'publish';

/* Enable/Disable comments before a certain date */
UPDATE wp_posts SET comment_status = 'closed' WHERE post_date < '2010-01-01' AND post_status = 'publish';

/* cleanup bad characters */
UPDATE wp_posts SET post_content = REPLACE(post_content, 'Â', '');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'â€œ', '“');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'â€', '”');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'â€™', '’');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'â€˜', '‘');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'â€”', '–');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'â€“', '—');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'â€¢', '-');
UPDATE wp_posts SET post_content = REPLACE(post_content, 'â€¦', '…');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'Â', '');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'â€œ', '“');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'â€', '”');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'â€™', '’');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'â€˜', '‘');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'â€”', '–');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'â€“', '—');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'â€¢', '-');
UPDATE wp_comments SET comment_content = REPLACE(comment_content, 'â€¦', '…');
