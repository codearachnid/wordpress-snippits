/* Delete all posts over X days (aged) - REPLACE X with (int) days */
DELETE FROM wp_posts WHERE post_type = 'post' AND DATEDIFF(NOW(), post_date) > X

/* Delete post meta */
DELETE FROM wp_postmeta WHERE meta_key = 'YourMetaKey';

/* Delete all revisions */
DELETE a, b, c FROM wp_posts a LEFT JOIN wp_term_relationships b ON (a.ID = b.object_id) LEFT JOIN wp_postmeta c ON (a.ID = c.post_id) WHERE a.post_type = 'revision';

/* Delete all unapproved & spam comments */
DELETE FROM wp_comments WHERE comment_approved = 0
DELETE FROM wp_comments WHERE comment_approved = 'spam';