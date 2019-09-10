<?php

/**
 * Class WP_Mailto_Links_Settings
 *
 * This class contains all of our important settings
 * Here you can configure the whole plugin behavior.
 *
 * @since 3.0.0
 * @package WPMT
 * @author Ironikus <info@ironikus.com>
 */
class WP_Mailto_Links_Settings{

	/**
	 * Our globally used capability
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $admin_cap;

	/**
	 * The main page name
	 *
	 * @var string
	 * @since 3.0.0
	 */
	private $page_name;

	/**
	 * WP_Mailto_Links_Settings constructor.
	 *
	 * We define all of our necessary settings in here.
	 * If you need to do plugin related changes, everything will
	 * be available in this file.
	 */
	function __construct(){
		$this->admin_cap            	= 'manage_options';
		$this->page_name            	= 'wp-mailto-links-option-page';
		$this->page_title           	= WPMT_NAME;
		$this->final_outout_buffer_hook = 'final_output';
		$this->widget_callback_hook 	= 'widget_output';
		$this->template_tags 			= array( 'wpml_filter' => 'template_tag_wpmt_filter', 'wpml_mailto' => 'template_tag_wpml_mailto' );
		$this->settings_key        		= 'wp-mailto-links';
		$this->version_key        		= 'wp-mailto-links-version';
		$this->previous_version        	= null;
		$this->hook_priorities        	= array(
			'buffer_final_output' => 1000,
			'setup_single_filter_hooks' => 100,
			'add_custom_template_tags' => 10,
			'load_frontend_header_styling' => 10,
			'wpmt_dynamic_sidebar_params' => 100,
			'filter_rss' => 100,
			'filter_page' => 100,
			'filter_content' => 100,
			'first_version_init' => 100,
			'version_update' => 100,
		);

		//Regex
		$this->email_regex 				= '([_A-Za-z0-9-]+(\\.[_A-Za-z0-9-]+)*@[A-Za-z0-9-]+(\\.[A-Za-z0-9-]+)*(\\.[A-Za-z]{2,}))';
	
		//Load data
		$this->settings        			= $this->load_settings();
		$this->version        			= $this->load_version();
	}

	/**
	 * ######################
	 * ###
	 * #### MAIN SETTINGS
	 * ###
	 * ######################
	 */

	private function load_settings(){
		$fields = array(

			'protect' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'protect',
				'type'        => 'multi-input',
				'input-type'  => 'radio',
				'title'       => wpmt()->helpers->translate( 'Protect emails', 'wpmt-settings-protect' ),
				'inputs' 	  => array( 
					1 => array(
						'label' => wpmt()->helpers->translate( 'Full-page scan', 'wpmt-settings-protect-label' ),
						'description' => wpmt()->helpers->translate('This will check the whole page against any mails and secures them.', 'wpmt-settings-protect-tip')
					),
					2 => array(
						'label' => wpmt()->helpers->translate( 'Wordpress filters', 'wpmt-settings-protect-label' ),
						'description' => wpmt()->helpers->translate('Secure only mails that occur within WordPress filters.', 'wpmt-settings-protect-tip')
					),
					3 => array(
						'label' => wpmt()->helpers->translate( 'Don\'t do anything.', 'wpmt-settings-protect-label' ),
						'description' => wpmt()->helpers->translate('This turns off the protection for emails. (Not recommended)', 'wpmt-settings-protect-tip')
					),
				 ),
				'required'    => false
			),

			'protect_using' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'protect_using',
				'type'        => 'multi-input',
				'input-type'  => 'radio',
				'title'       => wpmt()->helpers->translate( 'Protect emails using', 'wpmt-settings-protect_using' ),
				'inputs' 	  => array( 
					'with_javascript' => array(
						'label' => wpmt()->helpers->translate( 'automatically the best method (including javascript)', 'wpmt-settings-protect_using-label' )
					),
					'without_javascript' => array(
						'label' => wpmt()->helpers->translate( 'automatically the best method (excluding javascript)', 'wpmt-settings-protect_using-label' ),
					),
					'strong_method' => array(
						'label' => wpmt()->helpers->translate( 'a strong method that replaces all emails with a "*protection text*".', 'wpmt-settings-protect_using-label' ),
						'description' => wpmt()->helpers->translate('You can configure the protection text within the advanced settings.', 'wpmt-settings-protect_using-tip')
					),
					'char_encode' => array(
						'label' => wpmt()->helpers->translate( 'simple HTML character encoding.', 'wpmt-settings-protect_using-label' ),
						'description' => wpmt()->helpers->translate('Offers goot (but not the best) protection, which saves you in most scenarios.', 'wpmt-settings-protect_using-tip')
					),
				 ),
				'required'    => false
			),

			'filter_body' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'filter_body',
				'type'        => 'multi-input',
				'input-type'  => 'checkbox',
				'advanced' 	  => true,
				'title'       => wpmt()->helpers->translate( 'Protect...', 'wpmt-settings-filter_body' ),
				'label'       => wpmt()->helpers->translate( 'Customize what this plugin protects.', 'wpmt-settings-filter_body-label' ),
				'inputs' 	  => array(
					'filter_rss' => array(
						'advanced' 	  => true,
						'label' => wpmt()->helpers->translate( 'RSS feed', 'wpmt-settings-filter_rss-label' ),
						'description' => wpmt()->helpers->translate( 'Activating this option results in protecting the rss feed based on the given protection method.', 'wpmt-settings-filter_rss-tip' )
					),
					'input_strong_protection' => array(
						'advanced' 	  => true,
						'label' => wpmt()->helpers->translate( 'input form email fields using strong protection.', 'wpmt-settings-input_strong_protection-label' ),
						'description' => wpmt()->helpers->translate( 'Warning: this option could conflict with certain form plugins. Test it first. (Requires javascript)', 'wpmt-settings-input_strong_protection-tip' )
					),
					'convert_plain_to_mailto' => array(
						'advanced' 	  => true,
						'label' => wpmt()->helpers->translate( 'plain emails by converting them to mailto links', 'wpmt-settings-convert_plain_to_mailto-label' ),
						'description' => wpmt()->helpers->translate( 'Plain emails will be automatically converted to mailto links where possible. (Requires javascript)', 'wpmt-settings-convert_plain_to_mailto-tip' )
					),
					'protect_shortcode_tags' => array(
						'advanced' 	  => true,
						'label' => wpmt()->helpers->translate( 'shortcode content', 'wpmt-settings-protect_shortcode_tags-label' ),
						'description' => wpmt()->helpers->translate( 'Protect every shortcode content separately. (This may slows down your site)', 'wpmt-settings-protect_shortcode_tags-tip' )
					),
					'filter_hook' => array(
						'advanced' 	  => true,
						'label' => wpmt()->helpers->translate( 'emails from "init" hook', 'wpmt-settings-filter_hook-label' ),
						'description' => wpmt()->helpers->translate( 'Check this option if you want to register the email filters on the "init" hook instead of the "wp" hook.', 'wpmt-settings-filter_hook-tip' )
					),
				 ),
				'required'    => false,
			),

			'exclude_posts' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'exclude_posts',
				'type'        => 'text',
				'advanced' 	  => true,
				'title'       => wpmt()->helpers->translate('Exclude post id\'s from protection', 'wpmt-settings-exclude_posts'),
				'placeholder' => '',
				'required'    => false,
				'description' => wpmt()->helpers->translate('By comma separating post id\'s ( e.g. 123,4535,643), you are able to exclude these posts from the logic protection.', 'wpmt-settings-exclude_posts-tip')
			),

			'protection_text' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'protection_text',
				'type'        => 'text',
				'advanced' 	  => true,
				'title'       => wpmt()->helpers->translate('Set protection text *', 'wpmt-settings-class_name'),
				'placeholder' => '',
				'required'    => false,
				'description' => wpmt()->helpers->translate('This text will be shown for protected emailaddresses.', 'wpmt-settings-class_name-tip')
			),

			'class_name' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'class_name',
				'type'        => 'text',
				'advanced' 	  => true,
				'title'       => wpmt()->helpers->translate('Additional classes', 'wpmt-settings-class_name'),
				'label'       => wpmt()->helpers->translate('Add extra classes to mailto links.', 'wpmt-settings-class_name-label'),
				'placeholder' => '',
				'required'    => false,
				'description' => wpmt()->helpers->translate('Leave blank for none', 'wpmt-settings-class_name-tip')
			),

			'security_check' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'security_check',
				'type'        => 'checkbox',
				'title'       => wpmt()->helpers->translate('Security Check', 'wpmt-settings-security_check'),
				'label'       => wpmt()->helpers->translate('Mark emails on the site as successfully encoded', 'wpmt-settings-security_check-label') . '<i class="dashicons-before dashicons-lock" style="color:green;"></i>',
				'placeholder' => '',
				'required'    => false,
				'description' => wpmt()->helpers->translate('Only visible for admin users. If your emails look broken, simply deactivate this feature.', 'wpmt-settings-security_check-tip')
			),

			'own_admin_menu' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'own_admin_menu',
				'type'        => 'checkbox',
				'advanced' 	  => true,
				'title'       => wpmt()->helpers->translate('Admin Menu', 'wpmt-settings-own_admin_menu'),
				'label'       => wpmt()->helpers->translate('Show this page in the main menu item', 'wpmt-settings-own_admin_menu-label'),
				'placeholder' => '',
				'required'    => false,
				'description' => wpmt()->helpers->translate('Otherwise it will be shown in "Settings"-menu.', 'wpmt-settings-security_check-tip')
			),

			'advanced_settings' => array(
				'fieldset'    => array( 'slug' => 'main', 'label' => 'Label' ),
				'id'          => 'advanced_settings',
				'type'        => 'checkbox',
				'title'       => wpmt()->helpers->translate('Advanced Settings', 'wpmt-settings-advanced_settings'),
				'label'       => wpmt()->helpers->translate('Show advanced settings for more configuration possibilities.', 'wpmt-settings-advanced_settings-label'),
				'placeholder' => '',
				'required'    => false,
				'description' => wpmt()->helpers->translate('Activate the advanced settings in case you want to customize the default logic or you want to troubleshoot the plugin.', 'wpmt-settings-advanced_settings-tip')
			),

		);

		//End Migrate Old Plugin

		$values = get_option( $this->settings_key );

		if( empty( $values ) && ! is_array( $values ) ){
			$values = array(
				'protect' 				=> 1,
				'filter_rss' 			=> 1,
				'protect_using' 		=> 'with_javascript',
				'class_name' 			=> 'mail-link',
				'protection_text' 		=> '*protected email*',
			);

			update_option( $this->settings_key, $values );
		}

		//Bakwards compatibility
		if( ! isset( $values['protect_using'] ) ){
			$values['protect_using'] = 'with_javascript';
		}

		//In case the mailto functiinality was deactivated, we will set it do "Do nothing" as well.
		if( ! isset( $values['protect'] ) || (string) $values['protect'] === '0' ){
			$values['protect'] = 3;
		}
		//Backwards compatibility

		foreach( $fields as $key => $field ){
			if( $field['type'] === 'multi-input' ){
				foreach( $field['inputs'] as $smi_key => $smi_data ){

					if( $field['input-type'] === 'radio' ){
						if( isset( $values[ $key ] ) && (string) $values[ $key ] === (string) $smi_key ){
							$fields[ $key ]['value'] = $values[ $key ];
						}
					} else {
						if( isset( $values[ $smi_key ] ) ){
							$fields[ $key ]['inputs'][ $smi_key ]['value'] = $values[ $smi_key ];
						}
					}
					
				}
			} else {
				if( isset( $values[ $key ] ) ){
					$fields[ $key ]['value'] = $values[ $key ];
				}
			}
		}

		return apply_filters( 'wpmt/settings/fields', $fields );
	}

	/**
	 * ######################
	 * ###
	 * #### VERSIONING
	 * ###
	 * ######################
	 */

	 public function load_version(){

		$current_version = get_option( $this->get_version_key() );

		if( empty( $current_version ) ){
			$current_version = WPMT_VERSION;
			update_option( $this->get_version_key(), $current_version );

			add_action( 'init', array( $this, 'first_version_init' ), $this->get_hook_priorities( 'first_version_init' ) );
		} else {
			if( $current_version !== WPMT_VERSION ){
				$this->previous_version = $current_version;
				$current_version = WPMT_VERSION;
				update_option( $this->get_version_key(), $current_version );

				add_action( 'init', array( $this, 'version_update' ), $this->get_hook_priorities( 'version_update' ) );
			}
		}

		return $current_version;
	 }

	 /**
	  * Fires an action after our settings key was initially set
	  * the very first time.
	  *
	  * @return void
	  */
	 public function first_version_init(){
		 do_action( 'wpmt/settings/first_version_init', WPMT_VERSION );
	 }

	 /**
	  * Fires after the version of the plugin is initially updated
	  *
	  * @return void
	  */
	 public function version_update(){
		 do_action( 'wpmt/settings/version_update', WPMT_VERSION, $this->previous_version );
	 }

	/**
	 * ######################
	 * ###
	 * #### CALLABLE FUNCTIONS
	 * ###
	 * ######################
	 */

	/**
	 * Our admin cap handler function
	 *
	 * This function handles the admin capability throughout
	 * the whole plugin.
	 *
	 * $target - With the target function you can make a more precised filtering
	 * by changing it for specific actions.
	 *
	 * @param string $target - A identifier where the call comes from
	 * @return mixed
	 */
	public function get_admin_cap( $target = 'main' ){
		/**
		 * Customize the globally used capability for this plugin
		 *
		 * This filter is called every time the capability is needed.
		 */
		return apply_filters( 'wpmt/settings/capability', $this->admin_cap, $target );
	}

	/**
	 * Return the page name for our admin page
	 *
	 * @return string - the page name
	 */
	public function get_page_name(){
		/*
		 * Filter the page name based on your needs
		 */
		return apply_filters( 'wpmt/settings/page_name', $this->page_name );
	}

	/**
	 * Return the page title for our admin page
	 *
	 * @return string - the page title
	 */
	public function get_page_title(){
		/*
		 * Filter the page title based on your needs.
		 */
		return apply_filters( 'wpmt/settings/page_title', $this->page_title );
	}

	/**
	 * Return the settings_key
	 *
	 * @return string - the settings key
	 */
	public function get_settings_key(){
		return $this->settings_key;
	}

	/**
	 * Return the version_key
	 *
	 * @return string - the version_key
	 */
	public function get_version_key(){
		return $this->version_key;
	}

	/**
	 * Return the version
	 *
	 * @return string - the version
	 */
	public function get_version(){
		return apply_filters( 'wpmt/settings/get_version', $this->version );
	}

	/**
	 * Return the default template tags
	 *
	 * @return array - the template tags
	 */
	public function get_template_tags(){
		return apply_filters( 'wpmt/settings/get_template_tags', $this->template_tags );
	}

	/**
	 * Return the widget callback hook name
	 *
	 * @return string - the final widget callback hook name
	 */
	public function get_widget_callback_hook(){
		return apply_filters( 'wpmt/settings/widget_callback_hook', $this->widget_callback_hook );
	}

	/**
	 * Return the final output buffer hook name
	 *
	 * @return string - the final output buffer hook name
	 */
	public function get_final_outout_buffer_hook(){
		return apply_filters( 'wpmt/settings/final_outout_buffer_hook', $this->final_outout_buffer_hook );
	}

	/**
     * @link http://www.mkyong.com/regular-expressions/how-to-validate-email-address-with-regular-expression/
     * @param boolean $include
     * @return string
     */
    public function get_email_regex( $include = false ){

        if ($include === true) {
            $return = $this->email_regex;
        } else {
			$return = '/' . $this->email_regex . '/i';
		}

		return apply_filters( 'wpmt/settings/get_email_regex', $return, $include );
    }

	/**
     * Get hook priorities
	 * 
     * @param boolean $single - wether you want to return only a single hook priority or not
     * @return mixed - An array or string of hook priority(-ies)
     */
    public function get_hook_priorities( $single = false ){

		$return = $this->hook_priorities;
		$default = false;
		
		if( $single ){
			if( isset( $this->hook_priorities[ $single ] ) ){
				$return = $this->hook_priorities[ $single ];
			} else {
				$return = 10;
				$default = true;
			}
		}

		return apply_filters( 'wpmt/settings/get_hook_priorities', $return, $default );
    }

	/**
	 * ######################
	 * ###
	 * #### Settings helper
	 * ###
	 * ######################
	 */

	 /**
	  * Get the admin page url
	  *
	  * @return string - The admin page url
	  */
	 public function get_admin_page_url(){

		$url = admin_url( "options-general.php?page=" . $this->get_page_name() );

		 return apply_filters( 'wpmt/settings/get_admin_page_url', $url );
	 }

	 /**
	  * Helper function to reload the settings
	  *
	  * @return array - An array of all available settings
	  */
	 public function reload_settings(){

		$this->settings = $this->load_settings();

		 return $this->settings;
	 }

	/**
	 * Return the default strings that are available
	 * for this plugin.
	 *
	 * @param $slug - the identifier for your specified setting
	 * @param $single - wether you only want to return the value or the whole settings element
	 * @param $group - in case you call a multi-input that contains multiple values (e.g. checkbox), you can set a sub-slug to grab the sub value
	 * @return string - the default string
	 */
	public function get_setting( $slug = '', $single = false, $group = '' ){
		$return = $this->settings;

		if( empty( $slug ) ){
			return $return;
		}

		if( isset( $this->settings[ $slug ] ) || ( ! empty( $group ) && isset( $this->settings[ $group ] ) ) ){
			if( $single ){
				$return = false; // Default false

				//Set default to the main valie if available given with radio buttons)
				if( isset( $this->settings[ $slug ]['value'] ) ){
					$return = $this->settings[ $slug ]['value'];
				}

				if( 
					! empty( $group )
					&& isset( $this->settings[ $group ]['type'] )
					&& $this->settings[ $group ]['type'] === 'multi-input'
					)
				{
					if( isset( $this->settings[ $group ]['inputs'][ $slug ] ) && isset( $this->settings[ $group ]['inputs'][ $slug ]['value'] ) ){
						$return = $this->settings[ $group ]['inputs'][ $slug ]['value'];
					}
				}
				
			} else {

				if( ! empty( $group ) && isset( $this->settings[ $group ] ) ){
					$return = $this->settings[ $group ];
				} else {
					$return = $this->settings[ $slug ];
				}
				
			}
			
		}

		return $return;
	}

}