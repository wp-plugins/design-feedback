<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://designfeedbackplugin.com
 * @since      0.1
 *
 * @package    Design_Feedback
 * @subpackage Design_Feedbacke/admin
 * @author     Design Feedback
 */

class Design_Feedback_Handler {

	public $postId;
        public $table;
        
        public function __construct($postId){
        
            $this->postId = $postId;
            
            global $wpdb;
            $table = $wpdb->prefix . "feedbacks";
            $this->table = $table;
            
        }
        
        public function get_feedbacks(){
            
            global $wpdb;
            $table = $this->table;
            $postId = $this->postId;
            $query = "SELECT * FROM $table WHERE post_id = '$postId'";
            
            return $wpdb->get_results($query);
            
        }
        
        public function get_feedbacks_counter(){
            
            return count($this->get_feedbacks());
            
        }
        
        public function delete_feedback($feedbackId){
            
            global $wpdb;
            $table = $this->table;
            $postId = $this->postId;
            $query = "DELETE FROM $table WHERE id = '$feedbackId'";
            return $wpdb->get_results($query);
            
        }

}
