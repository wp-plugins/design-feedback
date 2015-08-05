<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://getCycles.io
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


	}
        
        public function define_post_types(){
            register_post_type( 'designfeedback',
            array(
                'labels' => array(
                            'name'          => __( 'Cycles' ),
                            'singular_name' => __( 'design' ),
                            'menu_name'     => __( 'Cycles' ),
                            'name_admin_bar'=> __( 'Cycles' ),
                            'all_items'     => __( 'All Designs' ),
                            'new_item'      => __( 'Add New Design' ),
                            'add_new'       => __( 'Add New' ),
                            'add_new_item'  => __( 'Add New Design' ),
                            'edit_item'     => __( 'Edit Design' ),
                            'view_item'     => __( 'View Design' ),
                            'search_items'  => __( 'Search Designs' )
                            ),
                'rewrite'            => array( 'slug' => 'cycles' ),
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

                add_thickbox();

                ob_start();
                ?>
                    <a href="#TB_inline?width=600&amp;height=120&amp;inlineId=cycles-share-<?php echo $post->ID; ?>" class="thickbox" title="Share this design for feedback">Share</a>

                    <div id="cycles-share-<?php echo $post->ID; ?>" style="display:none;" class="cycles-share">
                        <p>
                            Copy and share the URL below:<br />
                            <input type="text" class="large-text" value="<?php echo get_permalink( $post ); ?>" readonly>
                        </p>
                    </div>                    
                <?php
                $actions["share"] = ob_get_clean();
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
                                <p><?php echo stripcslashes(nl2br($feedback->feedback)); ?></p>
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
                
                $text = '';
                ob_start();
                ?>
                    <script>
                        jQuery( document ).ready( function( $ ) {

                            $('.cycles-share input').mouseover( function () {
                                $( this ).select();
                            } );

                            if ( $( '.add-new-h2' ).length ) {
                                $( '#publish' ).val( 'Update' );
                            } else {
                                $( '#publish' ).val( 'Save' );
                            }
                            $( '#submitdiv h3.hndle span' ).html( 'Save' );
                            $( '#shortlink' ).next().css( 'display', 'none' );
                        } );
                    </script>
                <?php
                $text .= ob_get_clean();
            }
            return $text;
        }
        
        public function ajax_delete_feedback() {
            
            $feedbackId = $_POST["feedbackId"];
            $handler = new Design_Feedback_Handler(0);
            $handler->delete_feedback($feedbackId);
            wp_die();
            
        }
        
    function hide_post_status_option() {

        global $post;

        $cpt = 'designfeedback';

        if ( $cpt === $post->post_type ) {
            echo '<style type="text/css">.misc-pub-section.misc-pub-post-status{ display: none }</style>';
        }
    }

    /**
     * Locks metaboxes
     *
     * Removes metaboxes injected by the theme or by plugins
     */
    public function lock_meta_boxes( $post_type, $post ){

        if ( 'designfeedback' !== $post_type ) {
            return;
        }

        global $wp_meta_boxes;

        // Metaboxes to show
        $allowed_meta_boxes = array(
            'submitdiv',
            'slugdiv',
            'design-feedback-meta-image',
            'design-feedback-meta-feedback',
        );

        // Loop through each page key of the '$wp_meta_boxes' global
        if ( ! empty( $wp_meta_boxes ) ) : foreach ( $wp_meta_boxes as $page => $page_boxes ) :

            // Loop through each contect
            if ( ! empty( $page_boxes ) ) : foreach ( $page_boxes as $context => $box_context ) :

                // Loop through each type of meta box
                if ( ! empty( $box_context ) ) : foreach ( $box_context as $box_type ) :

                    // Loop through each individual box
                    if ( ! empty( $box_type ) ) : foreach ( $box_type as $id => $box ) :

                        // Check to see if the meta box should be removed
                        if( ! in_array($id, $allowed_meta_boxes ) ) :

                            // Remove the meta box
                            remove_meta_box( $id, $page, $context );
                        endif;
                    endforeach; endif;
                endforeach; endif;
            endforeach; endif;
        endforeach; endif;
    }
}
