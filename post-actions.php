<?php

// set the post parent to a post
wp_update_post([
  'ID' => $post_id, 
  'post_parent' => $post_parent_id,
]);
