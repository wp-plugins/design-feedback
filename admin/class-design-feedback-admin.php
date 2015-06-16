<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://designfeedbackplugin.com
 * @since      0.1
 *
 * @package    Design Feedback
 * @subpackage Design_Feedback/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Design_Feedback
 * @subpackage Design_Feedbacke/admin
 * @author     Design Feedback
 */
class Design_Feedback_Admin {

	private $plugin_name;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
                
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    0.1
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in  Design_Feedback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Design_Feedback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

                wp_enqueue_style('thickbox');
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/design-feedback-admin.css', array(), $this->version, 'all' );
         }

	/**
	 * Register the JavaScript for the admin area.
	 *
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Design_Feedback_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Design_Feedback_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
                
                wp_enqueue_media();

                wp_enqueue_script( $this->plugin_name . "image-upload", plugin_dir_url( __FILE__ ) . 'js/meta-box-image-upload.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script( $this->plugin_name . "admin", plugin_dir_url( __FILE__ ) . 'js/design-feedback-admin.js', array( 'jquery' ), $this->version, false );
                wp_enqueue_script('jquery-ui-dialog');
                wp_enqueue_script( $this->plugin_name . "zero", plugin_dir_url( __FILE__ ) . 'js/zeroclipboard-2.2.0/dist/ZeroClipboard.js', array( 'jquery' ), $this->version, false );


	}
        
        public function define_post_types(){
            register_post_type( 'designfeedback',
            array(
                'labels' => array(
                            'name'          => __( 'Design Feedback' ),
                            'singular_name' => __( 'design' ),
                            'menu_name'     => __( 'Design Feedback' ),
                            'name_admin_bar'=> __( 'Design Feedback' ),
                            'all_items'     => __( 'All Designs' ),
                            'new_item'      => __( 'Add New Design' ),
                            'add_new'       => __( 'Add New' ),
                            'add_new_item'  => __( 'Add New Design' ),
                            'edit_item'     => __( 'Edit Design' ),
                            'view_item'     => __( 'View Design' ),
                            'search_items'  => __( 'Search Designs' )
                            ),
                'public'             => true,
                'has_archive'        => false,
                'menu_icon'          => 'dashicons-feedback'
              )
            );  


            // Removing the content editor that appears by standard
            remove_post_type_support( 'designfeedback', 'editor'); 

            flush_rewrite_rules(); 
        }
        
        
        public function define_row_actions($actions, $post){
            
            if ( $post->post_type == "designfeedback"){
                unset($actions["inline hide-if-no-js"]);
                $actions["share"] = '<a href = "javascript: designFeedback_openShareWindow(\'' . get_permalink($post->ID) . '\')">Share</a>';
            }
            
            return $actions;
            
        }
        
        public function define_meta_boxes(){
            
            $id = "design-feedback-meta-image";
            $title = "Image";
            $callback = array($this, "render_meta_box_image");
            $page = "designfeedback";
            $context = "normal";
            $priority = "high";
            $callback_args = array();
            
            add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args ); 
            
            
            //Second meta box
            $id = "design-feedback-meta-feedback";
            $title = "Feedback";
            $callback = array($this, "render_meta_box_feedback");    
            $priority = "low";
        
            
            add_meta_box( $id, $title, $callback, $page, $context, $priority, $callback_args ); 
            
        }
        
        public function render_meta_box_image(){
   
            wp_nonce_field( 'case_study_bg_submit', 'case_study_bg_nonce' );
            $meta = get_post_meta( get_the_ID(), "image_design" );            
            ?>


            <div class = "meta_box_no_image">
                <p>Add new image</p>
                <input type="hidden" name="meta-image" id="meta-image" value="<?php if ( isset ( $meta[0] ) ){ echo $meta[0]; } ?>" />
                <input type="button" id="meta-image-button" class="button" value="Select File" />
            </div>
            
            <div class = "meta_box_image">
                
            </div>

            <?php
            
            if (( isset ( $meta[0] ) ) && ( !empty ( $meta[0] ) ) ){
                echo "<script>designFeedback_loadImage();</script>";
            }
            
        }
        
        public function save_image_metabox( $postID ){
            
            if ( isset( $_POST["meta-image"] ) ) {
                update_post_meta($postID, "image_design", $_POST["meta-image"]); 
            }
            
	}

        
        public function ajax_get_image() {
            
            $image = $_POST["image"];
            $data = wp_get_attachment_metadata( $image );
            $uploadDir = wp_upload_dir();
            
            ?>

            <div class ="image">
                <?php echo wp_get_attachment_image( $image, "medium" ); ?>
            </div>
            
            <div class ="desc">
                <p>File name: <b><?php echo str_replace( "150x150", "", $data["sizes"]["thumbnail"]["file"] ); ?></b></p>
                <p>Uploaded on: <b><?php echo date("M d, Y @ H:i", filectime( $uploadDir["basedir"] . "/" . $data["file"] ) );?></b></p>
                <p>File type: <b><?php echo $data["sizes"]["thumbnail"]["mime-type"]?></b></p>
                <p>File size: <b><?php echo round(filesize($uploadDir["basedir"] . "/" . $data["file"]) / 1024, 2); ?>kb</b></p>
                <p>Dimensions: <b><?php echo $data["width"]; ?>px x <?php echo $data["height"]; ?>px</b></p>
                <p class = "delete"><a href = "javascript: designFeedback_removeImage();">Remove</a></p>
            </div>

            <div class="clearfix"></div>
            <?php
            wp_die(); // this is required to terminate immediately and return a proper response
            
        }
        
        public function render_meta_box_feedback(){
            
            $handler = new Design_Feedback_Handler(get_the_ID());
            $counter = $handler->get_feedbacks_counter();
            
            if ($counter == 0){
                ?>
                <div class ="no_feedback">
                    No feedback
                </div>
                <?php
            }else{
                $feedbacks = $handler->get_feedbacks();
                ?>
            
                <table class ="wp-list-table widefat fixed striped posts">
                    <thead>
                        <tr>
                            <th class = "manage-column column-did check-column"><b>#</b></th>
                            <th class = "manage-column column-date"><b>Name</b></th>
                            <th class = "manage-column column-title"><b>Feedback</b></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($feedbacks as $feedback){
                        ?>
                        
                        <tr class = "iedit author-self level-0 post-55 type-designfeedback status-publish hentry">
                            <td><?php echo $feedback->generated_id; ?></td>
                            <td><?php echo $feedback->name; ?></td>
                            <td>
                                <p>Submitted: <b><?php echo date("M d, Y @ H:i", $feedback->time); ?></b></p>
                                <p><?php echo stripcslashes($feedback->feedback); ?></p>
                                <div class="row-actions">
                                    <span class="trash"><a class="submitdelete" title="Delete this feedback" href="javascript: designFeedback_deleteFeedback('<?php echo $feedback->id; ?>');">Delete</a> </span>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            
                <?php
            }
            
        }
        
        
        /*
         * 3 next methods deal with columns
         */
        
        
        public function define_designs_columns( $columns ){
            
            unset($columns["date"]);
            $columns['feedbacks'] = '<img src = "' . plugin_dir_url( __FILE__ ) . 'images/comments-icon.gif"/>';
            $columns['date'] = "Date";
            return $columns;
            
        }
        
        public function define_designs_columns_content( $column, $postId ){
            if ( 'feedbacks' != $column ){
                return;
            }else{
                $handler = new Design_Feedback_Handler(get_the_ID());
                echo $handler->get_feedbacks_counter();
                
            }
            return $column;
        }
        
        
        public function define_designs_sortable_columns( $columns ){
            
            $columns['feedbacks'] = 'feedbacks';
            
            return $columns;
            
        }
        
        public function define_footer($text) {
            
            // There's a few other things in here besides the footer, such as the dialog for the url sharing
            
            $post_type = filter_input( INPUT_GET, 'post_type' );
            if ( !$post_type )
                $post_type = get_post_type( filter_input( INPUT_GET, 'post' ) );

            if ( 'designfeedback' == $post_type ){
                
                if ( isset( $_GET["post_type"] ) ){
                    
                    if ($_GET["post_type"] == "designfeedback"){
                        echo '<script>jQuery("#misc-publishing-actions").hide();</script>';
                    }
                    
                }
                
                $text = "";
                $text = <<<EOD
                        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

                        <div id="designFeedback_share" title="Share this design for feedback">
                            <form>
                                <fieldset>
                                    <p class= "share">Copy and share the URL below</p>
                                    <input type="text" name="shareURL" id="shareURL" disabled = "disabled" value="" class="text ui-widget-content ui-corner-all">                                    <input type = "button" value = "Done" id = "done" onClick = "jQuery( '#designFeedback_share' ).dialog( 'close' );"/>
                                </fieldset>
                            </form>
                         </div>
                        
                         <script>
                            var modal;
                            jQuery( document ).ready(function() {
                                modal = jQuery( "#designFeedback_share" ).dialog({
                                  autoOpen: false,
                                  height: 160,
                                  width: 450,
                                  modal: true,
                                  
                                  close: function() {

                                  }
                                });
                        
                                jQuery( "#publish" ).val("Save");
                                jQuery( "#submitdiv h3.hndle" ).html("<span>Save</span>");
                                jQuery( "#shortlink" ).next().css( "display", "none" );

                            });
                            
                            
                        </script>
                        <a href = "http://designfeedbackplugin.com/" target = "_blank">Design Feedback</a> | v1.0
EOD;
                        
         
            }
            
            return $text;
            
        }
        
        public function ajax_delete_feedback() {
            
            $feedbackId = $_POST["feedbackId"];
            $handler = new Design_Feedback_Handler(0);
            $handler->delete_feedback($feedbackId);
            wp_die();
            
        }
        
}
