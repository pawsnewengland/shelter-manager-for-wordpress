<?php

/**
 * Plugin Name: Shelter Manager for WordPress
 * Plugin URI: https://github.com/pawsnewengland/shelter-manager-for-wordpress
 * Description: Shelther Manager integration with WordPress.
 * Version: 1.1.1
 * Author: Chris Ferdinandi
 * Author URI: http://gomakethings.com
 * License: MIT
 */

function sm_get_pet_list($atts) {

	// Extract shortcode values
	extract(shortcode_atts(array(
		'shelter_id' => '',
		'username' => '',
		'password' => ''
	), $atts));

	// End if variables not set
	if ( empty($shelter_id) || empty($username) || empty($password) ) { return; }

	// Get data from server
	$url = 'http://sheltermanager.com/asm/service?account=' . $shelter_id . '&method=xml_adoptable_animals&username=' . $username . '&password=' . $password;
	$xml = @simplexml_load_file( $url );
	$data = json_decode(json_encode($xml), true);

	// Sort alphabetically
	if ( !function_exists( 'asm_sort_by_name' ) ) {
		function asm_sort_by_name($a, $b) {
			return strcmp( $a['animalname'], $b['animalname'] );
		}
	}
	usort($data[row], 'asm_sort_by_name');

	// Create select elements
	$options = '';
	foreach ( $data[row] as $pet ) {
		$options .= '<option value="' . $pet[animalname] . '">' . $pet[animalname] . ' (' . $pet[sheltercode] . ')</option>';
	}

	return $options;

}
add_shortcode('sm_get_pet_list', 'sm_get_pet_list');
