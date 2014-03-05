# these statements will update your options and user meta tables to persist 
# user permissions and ties to the database tables
# 
# set the {old_prefix} to your current prefix of the tables
# set the {new_prefix} to the prefix of the tables you wish to migrate
# this assumes you will run the table rename queries to migrate to the new prefix

UPDATE {old_prefix}options SET option_name='{new_prefix}user_roles' WHERE option_name='{old_prefix}user_roles';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}autosave_draft_ids' WHERE meta_key='{old_prefix}autosave_draft_ids';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}capabilities' WHERE meta_key='{old_prefix}capabilities';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}metaboxorder_post' WHERE meta_key='{old_prefix}metaboxorder_post';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}user_level' WHERE meta_key='{old_prefix}user_level';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}usersettings' WHERE meta_key='{old_prefix}usersettings';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}usersettingstime' WHERE meta_key='{old_prefix}usersettingstime';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}user-settings' WHERE meta_key='{old_prefix}user-settings';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}user-settings-time' WHERE meta_key='{old_prefix}user-settings-time';
UPDATE {old_prefix}usermeta SET meta_key='{new_prefix}dashboard_quick_press_last_post_id' WHERE meta_key='{old_prefix}dashboard_quick_press_last_post_id';
