/* Get all posts over X days (aged) - REPLACE X with (int) days */
SELECT * FROM wp_posts WHERE post_type = 'post' AND DATEDIFF(NOW(), post_date) > X

/* Identify unused tags */
SELECT * From wp_terms term INNER JOIN wp_term_taxonomy tax ON term.term_id = tax.term_id WHERE tax.taxonomy = 'post_tag' AND tax.count=0;

/* Export all comment author emails with no duplicates */
SELECT DISTINCT comment_author_email FROM wp_comments;