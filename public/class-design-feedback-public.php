<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://designfeedbackplugin.com
 * @since      0.1
 *
 * @package    Design_Feedback
 * @subpackage Design_Feedbacke/admin
 * @author     Design Feedback
 */

class Design_Feedback_Public {

        private $plugin_name;

	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	public function enqueue_styles() {
            
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/design-feedback-public.css', array(), $this->version, 'all' );

	}

	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Design_Feedback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/design-feedback-public.js', array( 'jquery' ), $this->version, false );
                
	}
        
        function design_feedback_custom_template( $single ) {
            global $wp_query, $post;

            if ( $post->post_type == "designfeedback" ){
               return plugin_dir_path( __FILE__ ) . 'templates/single-designfeedback.php';
            }

        }
        
        function design_feedback_leave_feedback_ajax(){
           
            // Retrieving all data
            $name = strip_tags(trim($_POST["name"]));
            $feedback = strip_tags(trim($_POST["feedback"]));
            $X = str_replace("px", "", $_POST["X"]);
            $Y = str_replace("px", "", $_POST["Y"]);
            $postId = $_POST["postId"];
            $generatedId = $_POST["generatedId"];
            $feedbackId = $_POST["feedbackId"];
            
            //Database
            global $wpdb;
            if (!empty($feedbackId)){ // If it's got an id it means it's just an update
                $wpdb->update( 
                        $wpdb->prefix . 'feedbacks', 
                        array( 
                                'name'     => $name, 
                                'feedback' => $feedback,
                                
                        ),
                        array(
                            'id' => $feedbackId
                        )
                );
            }else{
                $wpdb->insert( 
                        $wpdb->prefix . 'feedbacks', 
                        array( 
                                'name'     => $name, 
                                'feedback' => $feedback,
                                'x' => $X,
                                'y' => $Y,
                                'post_id' => $postId,
                                'time'  => time(),
                                'generated_id' => $generatedId
                        ) 
                );
            }
            wp_die();
        }

        
        function design_feedback_update_dot_ajax(){
           
            // Retrieving all data
            $dotId = strip_tags(trim($_POST["dotId"]));
            $X = str_replace("px", "", $_POST["X"]);
            $Y = str_replace("px", "", $_POST["Y"]);
            //Database
            global $wpdb;
            $wpdb->update( 
                    $wpdb->prefix . 'feedbacks', 
                    array( 
                            'x' => $X,
                            'y' => $Y                          
                    ),
                    array( 
                        'id' => $dotId
                    )
            );
            
            echo $wpdb->last_query;
            wp_die();
        }
        
        function design_feedback_refresh_dots_ajax(){
            
            global $wpdb;
            $table = $wpdb->prefix . "feedbacks";
            $postId = $_POST["postId"];
            
            $query = "SELECT * FROM $table WHERE post_id = '$postId'";
            $results = $wpdb->get_results($query);
            
            foreach ($results as $each){
                ?>
                <div class ="dot" id ="<?php echo $each->id; ?>" style = "left: <?php echo $each->x; ?>px; top:<?php echo $each->y; ?>px;"><?php echo $each->generated_id; ?></div>
                <?php
            }
            
            wp_die();
        }
        
        function design_feedback_get_dot_data_ajax(){
            
            global $wpdb;
            $table = $wpdb->prefix . "feedbacks";
            $dotId = $_POST["dotId"];
            
            $query = "SELECT * FROM $table WHERE id = '$dotId'";
            $results = $wpdb->get_results($query);
            
            
            ?>
                <script>
                    $(".leave_feedback #lvf_name").val("<?php echo $results[0]->name; ?>");
                    $(".leave_feedback #lvf_feedback").val("<?php echo $results[0]->feedback; ?>");
                    $(".leave_feedback #lvf_feedbackId").val("<?php echo $results[0]->id; ?>");

                </script>
            <?php
            
            wp_die();
        }
}
