<?php

/* Gravity Forms Login User After Registration */

remove_action("gform_post_submission", array("GFUser", "gf_create_user"));
add_action("gform_after_submission", array("GFUser", "gf_create_user"), 10, 2);

add_action("gform_user_registered", "gforms_autologin", 10, 4);
function gforms_autologin($user_id, $config, $entry, $password) {
    
    $form = RGFormsModel::get_form_meta($entry['form_id']);
    
    $user_login = apply_filters("gform_username_{$form['id']}", 
            apply_filters('gform_username', GFUser::get_meta_value('username', $config, $form, $entry), $config, $form, $entry), 
            $config, $form, $entry);
            
    $redirect_url = rgars($form, 'confirmation/url') ? rgars($form, 'confirmation/url') : get_bloginfo('home');
    
    //pass the above to the wp_signon function
    $result = wp_signon(array('user_login' => $user_login, 'user_password' =>  $password, 'remember' => false));
    
    if(!is_wp_error($result))
        wp_redirect($redirect_url);
    
}