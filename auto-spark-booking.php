<?php
/**
 * Plugin Name: Auto Spark Service Booking
 * Description: A simple booking system for car servicing appointments.
 * Version: 1.0
 * Author: Farhan Kanir
 * Author URI: https://autosparkbd.com
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Create database table on plugin activation
function auto_spark_create_booking_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'auto_spark_bookings';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        name varchar(255) NOT NULL,
        email varchar(255) NOT NULL,
        phone varchar(20) NOT NULL,
        service_type varchar(255) NOT NULL,
        appointment_date datetime NOT NULL,
        status varchar(20) DEFAULT 'pending',
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'auto_spark_create_booking_table');

// Shortcode for the booking form
function auto_spark_booking_form() {
    ob_start(); ?>
    <form method="post" action="">
        <label>Name:</label>
        <input type="text" name="asb_name" required>
        
        <label>Email:</label>
        <input type="email" name="asb_email" required>
        
        <label>Phone:</label>
        <input type="text" name="asb_phone" required>
        
        <label>Service Type:</label>
        <select name="asb_service">
            <option value="Oil Change">Oil Change</option>
            <option value="Tire Replacement">Tire Replacement</option>
            <option value="Engine Checkup">Engine Checkup</option>
        </select>
        
        <label>Appointment Date:</label>
        <input type="datetime-local" name="asb_date" required>
        
        <input type="submit" name="asb_submit" value="Book Appointment">
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('auto_spark_booking', 'auto_spark_booking_form');

// Handle form submission
function auto_spark_handle_booking() {
    if (isset($_POST['asb_submit'])) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'auto_spark_bookings';

        $wpdb->insert(
            $table_name,
            [
                'name' => sanitize_text_field($_POST['asb_name']),
                'email' => sanitize_email($_POST['asb_email']),
                'phone' => sanitize_text_field($_POST['asb_phone']),
                'service_type' => sanitize_text_field($_POST['asb_service']),
                'appointment_date' => sanitize_text_field($_POST['asb_date']),
            ]
        );
    }
}
add_action('init', 'auto_spark_handle_booking');
