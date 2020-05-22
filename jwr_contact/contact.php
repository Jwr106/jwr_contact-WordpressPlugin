<?php
/**
 * @package jwr_contact
 * @version 1.0.0
 */
/*
Plugin Name: jwr_contact
Description: Admin can receive notice by email when visitors submit a message, also can view and edit at 'Manage Messages' in the backend. 
Author: Jiawei Huang
Version: 1.0.0
*/

// plugin file path
define( 'CONTACT_DIR', plugin_dir_path( __FILE__ ));
// plugin URL path
define( 'CONTACT_URL', plugin_dir_url(__FILE__));

// active plugin, create database
register_activation_hook( __FILE__, function(){
    $sql = 'CREATE TABLE IF NOT EXISTS `contact` (
        `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
        `email` varchar(255) NOT NULL,
        `phone` varchar(100) NOT NULL,
        `message` text NOT NULL,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
      COMMIT;';
      
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});

// load static file
add_action( 'wp_enqueue_scripts', function(){
    wp_enqueue_style( 'contact-css', CONTACT_URL.'/static/contact.css');
    // depend on jquery，load in the tail
    wp_enqueue_script( 'layer-js', CONTACT_URL.'/static/layer/layer.js', ['jquery'],'',true);
    wp_enqueue_script( 'contact-js', CONTACT_URL.'/static/contact.js', ['layer-js'],'',true);
    // variable used by contact.js
    $js_var = [
        'ajax_url'=> admin_url('admin-ajax.php'),
        'success' =>'Submit successful',
        'fail' =>'Submit failed, please try again',
    ];
    wp_localize_script('contact-js', 'contact', $js_var);
});

// handle the database submitted by the front end ajax.
// non-user
add_action('wp_ajax_nopriv_contact_us', 'contact_us');
// user
add_action('wp_ajax_contact_us', 'contact_us');
function contact_us(){
    global $wpdb;
    $data = [
        'email' => '',
        'phone' => '',
        'message' => '',
    ];
    $data['email'] = isset($_POST['email']) ? $_POST['email']:'';
    $data['phone'] = isset($_POST['phone']) ? $_POST['phone']:'';
    $data['message'] = isset($_POST['message']) ? $_POST['message']:'';
    if($wpdb->insert('contact', $data)){
        // send mail
        $opt = get_option('smtp_666',[]);
        $to = isset($opt['smtp_username']) ? $opt['smtp_username'] : '';
        wp_mail($to, $data['email'],  'Phone:'.$data['phone'].'<br>Message:'.$data['message']);
        // Success
        echo json_encode(['code'=>1]);
    }else{
        // Failed
        echo json_encode(['code'=>0]);
    }
    die();
}

// set the email smtp protocal
add_action('phpmailer_init', function($phpmailer){
    $opt = get_option('smtp_666',[]);
    $phpmailer->isSMTP(); 
    $phpmailer->Host = isset($opt['smtp_host']) ? $opt['smtp_host'] : '';
    $phpmailer->SMTPAuth = isset($opt['smtp_auth']) ? (bool)$opt['smtp_auth'] : true;
    $phpmailer->SMTPSecure =isset($opt['smtp_encrypt']) ? $opt['smtp_encrypt'] : 'ssl';
    $phpmailer->Port = isset($opt['smtp_port']) ? $opt['smtp_port'] : 465;
    $phpmailer->From = isset($opt['smtp_username']) ? $opt['smtp_username'] : '';
    $phpmailer->isHTML(true);
    $phpmailer->Username = isset($opt['smtp_username']) ? $opt['smtp_username'] : '';
    $phpmailer->Password = isset($opt['smtp_password']) ? $opt['smtp_password'] : '';
    // $phpmailer->addAddress('xxxx@gmail.com');
});


add_action('wp_footer',function(){
    $html = <<<HTML
        <span id="contact">
            <svg t="1589983682134" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2042" xmlns:xlink="http://www.w3.org/1999/xlink" width="200" height="200"><defs><style type="text/css"></style></defs><path d="M692.044 907.99c-50.823 0-116.435-33.972-162.532-62.48-61.727-38.171-128.228-90.978-187.255-148.687l-0.215-0.21-18.74-18.813c-57.514-59.226-110.135-125.963-148.176-187.902-28.405-46.254-62.269-112.094-62.269-163.091 0-54.575 38.893-112.498 55.614-134.868 13.045-17.459 58.634-74.42 97.364-74.42 15.904 0 33.029 10.444 55.54 33.871 19.909 20.711 39.013 46.46 51.534 64.412 16.813 24.1 32.364 49.335 43.792 71.059 18.5 35.17 20.64 50.256 20.64 58.54 0 16.72-8.703 31.226-25.867 43.106-11.302 7.82-24.867 13.709-37.984 19.404-8.85 3.841-22.976 9.969-28.368 14.282 1.882 7.99 10.101 27.289 32.308 60.006 20.692 30.485 47.453 63.523 71.629 88.435 24.822 24.255 57.743 51.111 88.134 71.877 32.594 22.276 51.822 30.528 59.794 32.416 4.29-5.406 10.404-19.582 14.229-28.458 5.676-13.166 11.542-26.781 19.341-38.123 11.841-17.222 26.294-25.951 42.958-25.951 8.26 0 23.29 2.148 58.343 20.707 21.648 11.467 46.8 27.071 70.815 43.943 17.887 12.565 43.548 31.738 64.192 51.711 23.341 22.591 33.75 39.773 33.75 55.737 0 38.861-56.762 84.605-74.157 97.695-22.301 16.781-80.028 55.805-134.415 55.805l0 0 0 0zM374.821 663.5c56.627 55.348 120.22 105.871 179.098 142.274 57.077 35.298 107.423 55.545 138.125 55.545 27.35 0 66.337-16.762 104.302-44.829 16.64-12.31 31.785-26.015 42.649-38.593 10.191-11.809 13.917-19.51 14.864-22.62-3.941-8.492-27.335-35.039-81.934-72.833-21.419-14.825-43.562-28.45-62.351-38.365-23.594-12.445-33.428-14.594-35.676-14.949-0.876 0.603-3.444 2.758-7.351 9.329-4.373 7.358-8.575 17.107-12.64 26.537-5.001 11.61-10.177 23.61-16.525 33.298-10.322 15.751-22.995 23.746-37.668 23.754-0.262 0-0.542-0.004-0.826-0.015-7.137-0.151-28.875-0.613-87.887-40.941-32.578-22.272-67.961-51.146-94.629-77.251l-0.417-0.411c-26.009-26.773-54.782-62.269-76.975-94.956-40.195-59.216-40.662-81.028-40.812-88.197-0.32-14.99 7.566-27.926 23.423-38.467 9.698-6.441 21.76-11.677 33.423-16.742 9.396-4.076 19.109-8.292 26.441-12.676 6.547-3.915 8.703-6.498 9.297-7.376-0.353-2.258-2.494-12.125-14.898-35.802-9.878-18.849-23.459-41.074-38.228-62.563-37.668-54.795-64.124-78.265-72.583-82.22-3.101 0.954-10.775 4.693-22.542 14.918-12.539 10.893-26.197 26.098-38.457 42.797-27.974 38.087-44.674 77.213-44.674 104.658 0 30.805 20.172 81.322 55.346 138.599 36.286 59.081 86.631 122.894 141.787 179.715l18.318 18.382zM651.641 496.02c-8.117 0-15.308-5.839-16.764-14.139-4.172-23.738-17.871-44.363-38.573-58.077-20.997-13.905-46.66-19.069-72.271-14.538-9.275 1.643-18.113-4.573-19.747-13.875-1.634-9.302 4.556-18.176 13.826-19.815 34.23-6.058 68.669 0.932 96.97 19.68 28.601 18.942 47.554 47.595 53.37 80.686 1.634 9.302-4.557 18.176-13.827 19.814-1.003 0.177-1.998 0.263-2.982 0.263l0 0zM754.26 451.847c-8.113 0-15.308-5.837-16.764-14.133-8.094-46.068-34.623-86.057-74.694-112.599-40.369-26.736-89.652-36.674-138.77-27.984-9.275 1.639-18.11-4.573-19.747-13.875-1.634-9.302 4.556-18.176 13.826-19.814 57.739-10.215 115.793 1.543 163.468 33.125 47.972 31.771 79.754 79.787 89.491 135.207 1.634 9.302-4.556 18.176-13.827 19.814-1.004 0.173-1.998 0.258-2.982 0.258l0 0zM891.27 439.097c-8.117 0-15.311-5.839-16.767-14.139-13.811-78.614-59.043-146.832-127.355-192.074-68.609-45.438-152.318-62.337-235.706-47.584-9.27 1.639-18.11-4.573-19.743-13.875-1.634-9.302 4.556-18.176 13.826-19.814 92.006-16.281 184.489 2.443 260.404 52.721 76.208 50.473 126.693 126.719 142.15 214.686 1.634 9.302-4.56 18.176-13.83 19.814-0.999 0.178-1.995 0.267-2.98 0.267l0 0zM891.27 439.097z" p-id="2043" fill="#1296db"></path></svg>
        </span>  
HTML;
    echo $html;
});

// add functions page
add_action('admin_menu', function () {
    add_menu_page('Manage Messages', 'Manage Messages', 'edit_theme_options', 'contact', function () {
        require CONTACT_DIR . "/contact_manage.php";
	},'',25);
	add_menu_page('Mail Setting', 'Mail Setting', 'edit_theme_options', 'mail_setting', function () {
        require CONTACT_DIR . "/mail_setting.php";
	},'',25);
});

// delete messages
add_action('wp_ajax_contact_del', function(){
	$id = (int)$_POST['id'];
	global $wpdb;
	$wpdb->query($wpdb->prepare("DELETE FROM  `contact` WHERE `id` = %d", $id));
	die();
});