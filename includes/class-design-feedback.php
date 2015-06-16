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

class Design_Feedback {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 */
	public function __construct() {

		$this->plugin_name = 'design-feedback';
		$this->version = '0.1';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Design_Feedback_Loader. Orchestrates the hooks of the plugin.
	 * - Design_Feedback_i18n. Defines internationalization functionality.
	 * - Design_Feedback_Admin. Defines all hooks for the admin area.
	 * - Design_Feedback_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-design-feedback-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-design-feedback-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-design-feedback-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-design-feedback-public.php';
                
                /**
		 * The class responsible for handling feedbacks, I figured I put it here because it's going to be 
                 * useful for both admin and public sides
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-design-feedback-handler.php';

		$this->loader = new Design_Feedback_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 */
	private function set_locale() {

		$plugin_i18n = new Design_Feedback_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Design_Feedback_Admin( $this->get_plugin_name(), $this->get_version() );

                /*
                 * Enqueuing 
                 */ 
                
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
                
                /*
                 * Post Types and its addons 
                 */ 
                
                //Defining post types
                $this->loader->add_action( 'init', $plugin_admin, 'define_post_types' );
                
                // Action rows
                $this->loader->add_action( 'post_row_actions', $plugin_admin, 'define_row_actions', 10, 2 );
                
                // Metabox and custom fields related
                $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'define_meta_boxes' );
                $this->loader->add_action( 'wp_ajax_get_image', $plugin_admin, 'ajax_get_image' );
                $this->loader->add_action( 'save_post', $plugin_admin, 'save_image_metabox' );
                
                // Columns
                $this->loader->add_action( 'manage_designfeedback_posts_custom_column', $plugin_admin, 'define_designs_columns_content', 10, 2 );
                $this->loader->add_filter( 'manage_edit-designfeedback_columns', $plugin_admin, 'define_designs_columns', 10, 1 );
                $this->loader->add_filter( 'manage_edit-designfeedback_sortable_columns', $plugin_admin, 'define_designs_sortable_columns');

                // Footer
                $this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'define_footer');
                
                // Feedback deleting
                $this->loader->add_action( 'wp_ajax_delete_feedback', $plugin_admin, 'ajax_delete_feedback' );
               
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 */
	private function define_public_hooks() {

		$plugin_public = new Design_Feedback_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
                
                 //Changing Single Template
                $this->loader->add_filter( 'single_template', $plugin_public, 'design_feedback_custom_template');
                
                // Ajax Saving Feedback
                $this->loader->add_action( 'wp_ajax_leave_feedback', $plugin_public, 'design_feedback_leave_feedback_ajax' );
                $this->loader->add_action( 'wp_ajax_nopriv_leave_feedback', $plugin_public, 'design_feedback_leave_feedback_ajax' );
                
                // Ajax Refreshing Feedback
                $this->loader->add_action( 'wp_ajax_refresh_dots', $plugin_public, 'design_feedback_refresh_dots_ajax' );
                $this->loader->add_action( 'wp_ajax_nopriv_refresh_dots', $plugin_public, 'design_feedback_refresh_dots_ajax' );
                
                // Ajax Update Dot
                $this->loader->add_action( 'wp_ajax_update_dot', $plugin_public, 'design_feedback_update_dot_ajax' );
                $this->loader->add_action( 'wp_ajax_nopriv_update_dot', $plugin_public, 'design_feedback_update_dot_ajax' );
	
                // Ajax Get Dot Data
                $this->loader->add_action( 'wp_ajax_get_dot_data', $plugin_public, 'design_feedback_get_dot_data_ajax' );
                $this->loader->add_action( 'wp_ajax_nopriv_get_dot_data', $plugin_public, 'design_feedback_get_dot_data_ajax' );
	
        }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 */
	public function get_version() {
		return $this->version;
	}

}
