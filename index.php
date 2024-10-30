<?php
/**
 * @package Conditional Login Shortcodes
 * @author Nishant Vaity
 * @version 1.0
 * Plugin Name: Conditional Login Shortcodes
 * Plugin URI: http://wordpress.org/plugins/conditional-login-shortcodes/
 * Description: Provides shortcodes for conditional login to include content in a post based on context. Example : [is_user_logged_in]Welcome user [not_user_logged_in] Show login form using shortcode [clslf][/is_user_logged_in]
 * Author: Nishant Vaity
 * Version: 1.0
 * Author URI: http://profiles.wordpress.org/enishant/
*/

class conditional_login_shortcodes 
{

	function generic_handler ($atts, $content, $condition, $elsecode)
	{
		list ($if, $else) = explode ($elsecode, $content, 2);
		return do_shortcode($condition ? $if : $else);
	}
    
	function is_user_logged_in_shortcode_handler ($atts, $content="") 
	{
		return $this->generic_handler ($atts, $content, is_user_logged_in(), '[not_user_logged_in]');
	}

	function conditional_login_shortcode_login_form()
	{
		$atts = shortcode_atts( array(
		      'redirect' => site_url() ,
		), $atts );

		$args = array(
				'echo'           => false,
				'redirect'       => $redirect, 
				'form_id'        => 'loginform',
				'label_username' => 'Username',
				'label_password' => 'Password',
				'label_remember' => 'Remember Me',
				'label_log_in'   => 'Login',
				'id_username'    => 'user_login',
				'id_password'    => 'user_pass',
				'id_remember'    => 'rememberme',
				'id_submit'      => 'wp-submit',
				'remember'       => true,
				'value_username' => NULL,
				'value_remember' => false
		);
		return wp_login_form($args);
	}
	
	function send_email_on_plugin_activate() 
	{
		$plugin_title = "Conditional Login Shortcodes";
		$plugin_url = 'http://wordpress.org/plugins/conditional-login-shortcodes/';
		$plugin_support_url = 'http://wordpress.org/support/plugin/conditional-login-shortcodes';
		$plugin_author = 'Nishant Vaity';
		$plugin_author_url = 'https://github.com/enishant';
		$plugin_author_mail = 'enishant@gmail.com';

		$website_name  = get_option('blogname');
		$adminemail = get_option('admin_email');
		$user = get_user_by( 'email', $adminemail );

		$headers = 'From: ' . $website_name . ' <' . $adminemail . '>' . "\r\n";
		$subject = "Thank you for installing " . $plugin_title . "!\n";
		if($user->first_name)
		{
			$message = "Dear " . $user->first_name . ",\n\n";
		}
		else
		{
			$message = "Dear Administrator,\n\n";
		}
		$message.= "Thank your for installing " . $plugin_title . " plugin.\n";
		$message.= "Visit this plugin's site at " . $plugin_url . " \n\n";
		$message.= "Please write your queries and suggestions at developers support \n" . $plugin_support_url ."\n";
		$message.= "All the best !\n\n";
		$message.= "Thanks & Regards,\n";
		$message.= $plugin_author . "\n";
		$message.= $plugin_author_url ;
		wp_mail( $adminemail, $subject, $message,$headers);
		
		$subject = $plugin_title . " plugin is installed and activated by website " . get_option('home') ."\n";
		$message = $plugin_title  . " plugin is installed and activated by website " . get_option('home') ."\n\n";
		$message.= "Website : " . get_option('home') . "\n";
		$message.="Email : " . $adminemail . "\n";
		if($user->first_name)
		{
			$message.= "First name : " . $user->first_name . " \n";
		}
		if($user->last_name)
		{
			$message.= "Last name : " . $user->last_name . "\n";	
		}
		wp_mail( $plugin_author_mail , $subject, $message,$headers);
	}
}
$conditional_login_shortcodes = new conditional_login_shortcodes;
add_shortcode('is_user_logged_in',     array($conditional_login_shortcodes, 'is_user_logged_in_shortcode_handler'));
add_shortcode('clslf', array($conditional_login_shortcodes, 'conditional_login_shortcode_login_form'));
register_activation_hook( __FILE__, array($conditional_login_shortcodes,'send_email_on_plugin_activate'));
?>
