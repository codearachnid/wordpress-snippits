# remove all spam comments
DELETE from wp_comments WHERE comment_approved = '0'; 
DELETE from wp_comments WHERE comment_approved = 'spam';
DELETE from wp_comments WHERE comment_approved = 'trash';

# remove entries which have no related entry in the main wp_comments table
DELETE FROM wp_commentmeta WHERE comment_id NOT IN (SELECT comment_id FROM wp_comments);

# if Akismet is installed & you don't care about dashboard stats - flush out akismet statuses
DELETE FROM wp_commentmeta WHERE meta_key LIKE '%akismet%';
