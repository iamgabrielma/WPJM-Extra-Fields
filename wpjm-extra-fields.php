<?php
/**
 * Plugin Name: WPJM Extra Fields
 * Plugin URI: https://tilcode.blog/wpjm-extra-fields-adds-extra-fields-to-wp-job-manager-job-listings
 * Description: Adds an extra Salary and Important Information fields to WP Job Manager job listings
 * Version: 1.3.0
 * Author: Gabriel Maldonado
 * Author URI: http://tilcode.blog/
 * Text Domain: wpjm-extra-fields
 *
 * License: GPLv2 or later
 */

/**
 * Prevent direct access data leaks
 **/
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'gma_wpjmef_add_support_link_to_plugin_page' );

// Submit form filters
add_filter( 'submit_job_form_fields', 'gma_wpjmef_frontend_add_salary_field');
add_filter( 'submit_job_form_fields', 'gma_wpjmef_frontend_add_important_info_field');
// Text fields filters
add_filter( 'job_manager_job_listing_data_fields', 'gma_wpjmef_admin_add_salary_field' ); // #
add_filter( 'job_manager_job_listing_data_fields', 'gma_wpjmef_admin_add_important_info_field' );
// Single Job page filters
add_action( 'single_job_listing_meta_end', 'gma_wpjmef_display_job_salary_data' );
add_action( 'single_job_listing_meta_end', 'gma_wpjmef_display_important_info_data' );
// Dashboard: Job Listings > Jobs filters
add_filter( 'manage_edit-job_listing_columns', 'gma_wpjmef_retrieve_salary_column' );
add_filter( 'manage_job_listing_posts_custom_column', 'gma_wpjmef_display_salary_column' );

/**
* Sets the job_salary metadata as a new $column that can be used in the back-end
**/
function gma_wpjmef_retrieve_salary_column($columns){

  $columns['job_salary']         = __( 'Salary', 'wpjm-extra-fields' );
  return $columns;

};

/**
* Adds a new case to WP-Job-Manager/includes/admin/class-wp-job-manager-cpt.php
**/

function gma_wpjmef_display_salary_column($column){
  
  global $post;

  switch ($column) {    
    case 'job_salary':
      
      $salary = get_post_meta( $post->ID, '_job_salary', true );
      
      if ( !empty($salary)) {
        echo $salary;
      } else {
        echo '-';
      
      }
    break;
  }

  return $column;

};


/**
* Adds a direct support link under the Plugins Page once the plugin is activated
**/
function gma_wpjmef_add_support_link_to_plugin_page( $links ){

    $links = array_merge( array(
        '<a href="https://wordpress.org/support/plugin/wpjm-extra-fields" target="_blank">' . __( 'Support', 'wpjm-extra-fields' ) . '</a>'
    ), $links );
    return $links;
}

/**
* Adds a new optional "Salary" text field at the "Submit a Job" form, generated via the [submit_job_form] shortcode
**/
function gma_wpjmef_frontend_add_salary_field( $fields ) {
  
  $fields['job']['job_salary'] = array(
    'label'       => __( 'Salary', 'wpjm-extra-fields' ),
    'type'        => 'text',
    'required'    => false,
    'placeholder' => 'e.g. USD$ 20.000',
    'description' => '',
    'priority'    => 7,
  );

  return $fields;

}

/**
* Adds a new optional "Important Information" text field at the "Submit a Job" form, generated via the [submit_job_form] shortcode
**/
function gma_wpjmef_frontend_add_important_info_field( $fields ) {
  
  $fields['job']['job_important_info'] = array(
    'label'       => __( 'Important information: ', 'wpjm-extra-fields' ),
    'type'        => 'text',
    'required'    => false,
    'placeholder' => 'e.g. Work visa required',
    'description' => '',
    'priority'    => 8,
  );
  
  return $fields;

}

/**
* Adds a text field to the Job Listing wp-admin meta box named “Salary”
**/
function gma_wpjmef_admin_add_salary_field( $fields ) {
  
  $fields['_job_salary'] = array(
    'label'       => __( 'Salary', 'wpjm-extra-fields' ),
    'type'        => 'text',
    'placeholder' => 'e.g. USD$ 20.000',
    'description' => ''
  );

  return $fields;

}

/**
* Adds a text field to the Job Listing wp-admin meta box named "Important Information"
**/
function gma_wpjmef_admin_add_important_info_field( $fields ) {
  
  $fields['_job_important_info'] = array(
    'label'       => __( 'Important information', 'wpjm-extra-fields' ),
    'type'        => 'text',
    'placeholder' => 'e.g. Work visa required',
    'description' => ''
  );

  return $fields;

}

/**
* Displays "Salary" on the Single Job Page, by checking if meta for "_job_salary" exists and is displayed via do_action( 'single_job_listing_meta_end' ) on the template
**/
function gma_wpjmef_display_job_salary_data() {
  
  global $post;

  $salary = get_post_meta( $post->ID, '_job_salary', true );
  $important_info = get_post_meta( $post->ID, '_job_important_info', true );

  if ( $salary ) {
    echo '<li class="wpjmef-field-salary">' . __( 'Salary: ' ) . esc_html( $salary ) . '</li>';
  }

}

/**
* Displays the content of the "Important Information" text-field on the Single Job Page, by checking if meta for "_job_important_info" exists and is displayed via do_action( 'single_job_listing_meta_end' ) on the template
**/
function gma_wpjmef_display_important_info_data() {
  
  global $post;

  $important_info = get_post_meta( $post->ID, '_job_important_info', true );

  if ( $important_info ) {
    echo '<li class="wpjmef-field-important-info">' . esc_html( $important_info ) . '</li>';
  }

}

/**
 * Display an error message notice in the admin if WP Job Manager is not active
 */
function gma_wpjmef_admin_notice__error() {
	
  $class = 'notice notice-error';
	$message = __( 'An error has occurred. WP Job Manager must be installed in order to use this plugin', 'wpjm-extra-fields' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 

}