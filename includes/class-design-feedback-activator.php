<?php

/**
 * Fired during plugin activation
 *
 * @link       http://getCycles.io
 * @since      0.1
 *
 * @package    Design_Feedback
 * @subpackage Design_Feedbacke/admin
 * @author     Design Feedback
 */

class Design_Feedback_Activator {

	public static function activate() {
            
            //Creating the feedbacks tables
            global $wpdb;
            $table = $wpdb->prefix . "feedbacks";
            $query = "CREATE TABLE IF NOT EXISTS `$table` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `post_id` int(11) NOT NULL,
                        `generated_id` int(11) NOT NULL,
                        `name` varchar(50) NOT NULL,
                        `feedback` text NOT NULL,
                        `x` varchar(6) NOT NULL,
                        `y` varchar(6) NOT NULL,
                        `time` varchar(20) NOT NULL,
                        PRIMARY KEY (`id`)
                      )";
            $wpdb->get_results($query);
            

	}

}
