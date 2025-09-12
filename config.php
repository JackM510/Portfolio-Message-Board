<?php
// Root pathways
define('APP_BASE_PATH', '/Portfolio-Messageboard');
define('APP_ROOT', __DIR__);
// Includes (server-side)
define('HEAD_INC', APP_ROOT . '/includes/head.php');
define('NAV_INC', APP_ROOT . '/includes/nav.php');
define('DB_INC', APP_ROOT . '/includes/db_connection.php');
define('UTIL_INC', APP_ROOT . '/includes/utils/utilities.php');
define('CREATE_POST_INC', APP_ROOT . '/includes/create_post.php');
define('FETCH_POSTS_INC', APP_ROOT . '/includes/fetch_posts.php');
// Pages
define('LOGIN_URL', APP_BASE_PATH . '/subpages/login.php');
define('INDEX_URL', APP_BASE_PATH . '/index.php');
define('PROFILE_URL', APP_BASE_PATH . '/subpages/profile.php');
define('ADMIN_URL', APP_BASE_PATH . '/subpages/admin.php');
define('ACCOUNT_URL', APP_BASE_PATH . '/subpages/account.php');
define('LOGOUT_URL', APP_BASE_PATH . '/actions/logout_user.php');
// Actions 
define('ACTION_ADD_COMMENT', APP_BASE_PATH . '/actions/add_comment.php');
define('ACTION_ADD_POST', APP_BASE_PATH . '/actions/add_post.php');
define('ACTION_ADD_PROFILE', APP_BASE_PATH . '/actions/add_profile.php');
define('ACTION_ADD_USER', APP_BASE_PATH . '/actions/add_user.php');
define('ACTION_DELETE_COMMENT', APP_BASE_PATH . '/actions/delete_comment.php');
define('ACTION_DELETE_POST', APP_BASE_PATH . '/actions/delete_post.php');
define('ACTION_DELETE_USER', APP_BASE_PATH . '/actions/delete_user.php');
define('ACTION_EDIT_COMMENT', APP_BASE_PATH . '/actions/edit_comment.php');
define('ACTION_EDIT_POST', APP_BASE_PATH . '/actions/edit_post.php');
define('ACTION_EDIT_PROFILE', APP_BASE_PATH . '/actions/edit_profile.php');
define('ACTION_GET_ALL_USERS', APP_ROOT . '/actions/get_all_users.php');
define('ACTION_GET_USER', APP_BASE_PATH . '/actions/get_user.php');
define('ACTION_GET_PROFILE', APP_ROOT . '/actions/get_profile.php');
define('ACTION_GET_COMMENTS', APP_ROOT . '/actions/get_comments.php');
define('ACTION_GET_POSTS', APP_ROOT . '/actions/get_posts.php');
define('ACTION_LIKE_COMMENT', APP_BASE_PATH . '/actions/like_comment.php');
define('ACTION_LIKE_POST', APP_BASE_PATH . '/actions/like_post.php');
define('ACTION_LOGIN_USER', APP_BASE_PATH . '/actions/login_user.php');
define('ACTION_LOGOUT_USER', APP_BASE_PATH . '/actions/logout_user.php');
define('ACTION_UPDATE_EMAIL', APP_BASE_PATH . '/actions/update_email.php');
define('ACTION_UPDATE_PASSWORD', APP_BASE_PATH . '/actions/update_password.php');
// Icons
define('ICON_HOME', APP_BASE_PATH . '/assets/icon/forum.png');
define('ICON_PROFILE', APP_BASE_PATH . '/assets/icon/profile.png');
// CSS
define('CSS_ACCOUNT', APP_BASE_PATH . '/assets/CSS/account.css');
define('CSS_ADMIN', APP_BASE_PATH . '/assets/CSS/admin.css');
define('CSS_CREATE_POST', APP_BASE_PATH . '/assets/CSS/create_post.css');
define('CSS_FETCH_POSTS', APP_BASE_PATH . '/assets/CSS/fetch_posts.css');
define('CSS_INDEX', APP_BASE_PATH . '/assets/CSS/index.css');
define('CSS_LOGIN', APP_BASE_PATH . '/assets/CSS/login.css');
define('CSS_NAV', APP_BASE_PATH . '/assets/CSS/nav.css');
define('CSS_PROFILE', APP_BASE_PATH . '/assets/CSS/profile.css');
define('CSS_STYLES', APP_BASE_PATH . '/assets/CSS/styles.css');
// JS
define('JS_ACCOUNT', APP_BASE_PATH . '/assets/JS/account.js');
define('JS_ADMIN', APP_BASE_PATH . '/assets/JS/admin.js');
define('JS_CREATE_POST', APP_BASE_PATH . '/assets/JS/create_post.js');
define('JS_FETCH_POSTS', APP_BASE_PATH . '/assets/JS/fetch_posts.js');
define('JS_LOGIN', APP_BASE_PATH . '/assets/JS/login.js');
define('JS_PROFILE', APP_BASE_PATH . '/assets/JS/profile.js');
define('JS_TEXTAREA', APP_BASE_PATH . '/assets/JS/utils/textarea.js');
define('JS_PAGE_TRANSITIONS', APP_BASE_PATH . '/assets/JS/utils/page_transitions.js');
define('JS_ACCORDIAN', APP_BASE_PATH . '/assets/JS/utils/accordian.js');
define('JS_VALIDATE_CHECKBOXES', APP_BASE_PATH . '/assets/JS/utils/validate_checkboxes.js');
// Default assets
define('DEFAULT_PROFILE_PIC', APP_BASE_PATH . '/uploads/default/profile_picture.png');
// User uploads
define('DIR_PROFILE_UPLOADS', rtrim($_SERVER['DOCUMENT_ROOT'], '/\\') . APP_BASE_PATH . "/uploads/profiles");
define('URL_PROFILE_UPLOADS', '/uploads/profiles');
?>