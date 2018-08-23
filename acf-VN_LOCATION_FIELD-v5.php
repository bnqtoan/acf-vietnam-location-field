<?php

class acf_field_VN_LOCATION_FIELD extends acf_field {
	var $settings, $defaults;

	public function __construct() {
		$this->name     = 'VN_LOCATION_FIELD';
		$this->label    = __( 'VN Location' );
		$this->category = __( "Basic", 'acf' ); // Basic, Content, Choice, etc

		$this->defaults = array(
			"country_name" => '',
			"city_name"    => '',
			"state_name"   => 0,
			"country_id"   => 0,
			"city_id"      => 0,
			"state_id"     => '',
		);

		parent::__construct();

		$this->settings = array(
			'path'    => apply_filters( 'acf/helpers/get_path', __FILE__ ),
			'dir'     => plugin_dir_url( __FILE__ ),
			'version' => '1.0.1'
		);
	}

	public function create_options( $field ) {
		$key = $field['name'];
	}

	public function render_field( $field ) {

		$field['value'] = isset( $field['value'] ) ? $field['value'] : '';
		if ( $field['value'] ) {
			$_tmp           = explode( ',', $field['value'] );
			$field['value'] = [
				'province_id' => $_tmp[0],
				'district_id' => $_tmp[1]
			];
		}

		$province_id = ( isset( $field['value']['province_id'] ) ) ? $field['value']['province_id'] : '01';
		$district_id = ( isset( $field['value']['district_id'] ) ) ? $field['value']['district_id'] : 0;
		$ward_id     = ( isset( $field['value']['ward_id'] ) ) ? $field['value']['ward_id'] : 0;

		$key = $field['name'];

		global $wpdb;

		$provinces = self::_acf_get_provinces();
		$cities    = self::_acf_get_districts( $province_id );
		// Only applies when United States is selected as a country
		$states = array();
		?>

		<?php $country_field = $field['name'] . '[province_id]'; ?>
        <ul class="country-selector-list">
            <li id="field-<?php echo $key; ?>[province_id]">
                <div class="field-inner">
	                <?php _vnl_province_list( $province_id, $country_field ); ?>
                </div>
            </li>
            <li id="field-<?php echo $key; ?>[district_id]">
                <div class="css3-loader" style="display:none;">
                    <div class="css3-spinner"></div>
                </div>
                <div class="field-inner">
					<?php $city_field = $field['name'] . '[district_id]'; ?>
					<?php _vnl_district_list( $province_id, $district_id, $city_field ); ?>
                </div>
            </li>
        </ul>

		<?php
	}

	public function input_admin_enqueue_scripts() {
		wp_register_script( 'acf-input-country', $this->settings['dir'] . 'js/input.js', array( 'acf-input' ), $this->settings['version'] );
		wp_register_script( 'acf-input-chosen', $this->settings['dir'] . 'js/chosen.jquery.min.js', array( 'jquery' ), $this->settings['version'] );
		wp_register_style( 'acf-input-country', $this->settings['dir'] . 'css/input.css', array( 'acf-input' ), $this->settings['version'] );
		wp_register_style( 'acf-input-chosen', $this->settings['dir'] . 'css/chosen.min.css', array(), $this->settings['version'] );

		wp_localize_script( 'acf-input-country', "acfCountry", array(
			"ajaxurl" => admin_url( "admin-ajax.php" ),
		) );


		// scripts
		wp_enqueue_script( array(
			'acf-input-country',
			'acf-input-chosen',
		) );

		// styles
		wp_enqueue_style( array(
			'acf-input-country',
			'acf-input-chosen',
		) );
	}

	public function update_value( $value, $post_id, $field ) {
		//$value['province_name'] = $this->_acf_get_province( $value['country_id'] );
		//$value['district_name']    = $this->_acf_get_district( $value['district_id'] );
		$value = implode( ',', [ $value['province_id'], $value['district_id'] ] );

		return $value;
	}

	public function format_value( $value, $post_id, $field ) {
		$old_values = $value;
		if (!is_string($old_values)) $old_values = '';
		$old_values = explode(',', $old_values);

		$_values['province_id'] = $old_values[0];
		$_values['district_id'] = $old_values[1];

		$value      = array();

		$value['district_name']    = $this->_acf_get_district( $_values['district_id'] );
		$value['province_name'] = $this->_acf_get_province( $_values['province_id'] );
		return $value;
	}

	/**
	 * Get Countries
	 *
	 * Get all countries from the database
	 *
	 */
	static public function _acf_get_provinces() {
		global $wpdb;
		$countries_db = $wpdb->get_results( "SELECT DISTINCT * FROM " . $wpdb->prefix . "tinhthanhpho ORDER BY name ASC" );

		$countries = array();

		foreach ( $countries_db AS $country ) {
			if ( trim( $country->name ) == '' ) {
				continue;
			}
			$countries[ $country->matp ] = $country->name;
		}

		return $countries;
	}

	/**
	 * Get Country
	 *
	 * Get a particular country from the database
	 *
	 */
	public function _acf_get_province( $country_id ) {
		global $wpdb;
		$country = $wpdb->get_row( "SELECT DISTINCT * FROM " . $wpdb->prefix . "tinhthanhpho WHERE matp = '" . $country_id . "'" );

		if ( $country ) {
			return $country->name;
		} else {
			return false;
		}
	}

	/**
	 * Get Cities
	 *
	 * Get all cities for a particular country
	 *
	 */
	static public function _acf_get_districts( $province_id ) {
		global $wpdb;
		$cities_db = $wpdb->get_results( "SELECT DISTINCT * FROM " . $wpdb->prefix . "quanhuyen WHERE matp='" . $province_id . "' ORDER BY name ASC" );

		$cities = array();

		foreach ( $cities_db AS $city ) {
			if ( trim( $city->name ) == '' ) {
				continue;
			}
			$cities[ $city->maqh ] = $city->name;
		}

		return $cities;
	}

	/**
	 * Get City
	 *
	 * Get a particular city based on its ID
	 *
	 */
	public function _acf_get_district( $city_id ) {
		global $wpdb;
		$city = $wpdb->get_row( "SELECT DISTINCT * FROM " . $wpdb->prefix . "quanhuyen WHERE maqh = '" . $city_id . "'" );

		if ( $city ) {
			return $city->name;
		} else {
			return false;
		}
	}

	/**
	 * Get State
	 *
	 * Get a particular state based on its ID
	 *
	 */
	public function _acf_get_state( $state_id ) {
		global $wpdb;
		$state = $wpdb->get_row( "SELECT DISTINCT * FROM " . $wpdb->prefix . "states WHERE id = '" . $state_id . "'" );

		if ( $state ) {
			return $state->state;
		} else {
			return false;
		}
	}
}

add_action( 'wp_ajax_get_province_districts', 'get_province_districts' );
function get_province_districts() {
	global $wpdb;

	$province_id = (int) trim( $_POST['provinceId'] );

	$cities_db = $wpdb->get_results( "SELECT DISTINCT * FROM " . $wpdb->prefix . "quanhuyen WHERE matp='" . $province_id . "' ORDER BY name ASC" );
	$cities    = array();

	if ( $cities_db ) {
		foreach ( $cities_db AS $city ) {
			$cities[ $city->maqh ] = $city->name;
		}
	}

	echo json_encode( $cities );

	die();
}

add_action( "wp_ajax_get_us_states", "get_us_states" );
function get_us_states() {
	global $wpdb;

	$states_db = $wpdb->get_results( "SELECT DISTINCT * FROM " . $wpdb->prefix . "states ORDER BY state ASC" );
	$states    = array();

	if ( $states_db ) {
		foreach ( $states_db AS $state ) {
			$states[ $state->id ] = $state->state;
		}
	}

	echo json_encode( $states );

	die();
}

// Create our v4 field
new acf_field_VN_LOCATION_FIELD();
