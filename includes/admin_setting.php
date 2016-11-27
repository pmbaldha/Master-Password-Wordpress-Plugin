<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
require_once( MAPA_INCLUDES_PATH. 'class.settings-api.php'  );
if ( !class_exists('WeDevs_Settings_API_Test' ) ):
class WeDevs_Settings_API_Test {

    private $settings_api;

    function __construct() {
        $this->settings_api = new WeDevs_Settings_API;
		
		register_activation_hook( 'MAPA_FILE', array($this, 'register_activation') );
		register_deactivation_hook( 'MAPA_FILE', array($this, 'register_deactivation') );
		
        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }
	
	function register_activation() {
		$settings_fiels = $this->get_settings_fields();
		
		foreach($settings_fiels as $section=>$fields ) {
			$default_settings_val_arr = array();
			foreach( $fields as $field) {
				if( !empty($field['default'] ) ) {
					$default_settings_val_arr[ $field['name'] ] =  $field['default'];
				}
			}
			
			if( !empty( $default_settings_val_arr ) ) {
				update_option( $section,  $default_settings_val_arr);
			}
		}
	}
	
	function register_deactivation() {
		$sections = $this->get_settings_sections();
		foreach( $sections as $section) {
			delete_option( $section['id'] );
		}
		
		
	}
		
		

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
        add_options_page( esc_html__('Master Password', MAPA_TEXT_DOMAIN), esc_html__('Master Password', MAPA_TEXT_DOMAIN), 'administrator', 'master-password', array($this, 'plugin_page') );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id' => 'mapa_settings',
                'title' => __( 'Master Password Settings', 'wedevs' )
            ),
        );
        return $sections;
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'mapa_settings' => array(              
                array(
                    'name'  => 'mapa_enable_master_password',
                    'label' => __( 'Enable Master Password', 'wedevs' ),
                    'desc'  => __( 'Yes', 'wedevs' ),
                    'type'  => 'checkbox',
					'default' => 'on' 
                ),
				array(
                    'name'  => 'mapa_master_password_is_admin_password',
                    'label' => __( 'Is Mater Password same as Admin Password?', 'wedevs' ),
                    'desc'  => __( 'Yes', 'wedevs' ),
                    'type'  => 'checkbox',
					'default' => 'on' 
                ),
                array(
                    'name'              => 'mapa_master_password',
                    'label'             => __( 'Master Password', 'wedevs' ),
                    'desc'              => __( 'Please set master password here', 'wedevs' ),
                    'type'              => 'text',
                    'default'           => '12345',
                    'sanitize_callback' => 'intval'
                ),
                array(
                    'name'  => 'mapa_can_admin_user_access',
                    'label' => __( 'Can Admin Role User Use Master Password?', 'wedevs' ),
                    'desc'  => __( 'Yes', 'wedevs' ),
                    'type'  => 'checkbox'
                )
            ),
            
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';

       // $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }

        return $pages_options;
    }

}
endif;
new WeDevs_Settings_API_Test();