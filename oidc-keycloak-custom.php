<?php
/**
 * Plugin Name: OpenID Connect Client Customizations
 * Description: Provides customizations for the OpenID Connect Client plugin.
 *
 * @package  OpenidConnectGeneric_MuPlugin
 *
 * @link     https://github.com/daggerhart/openid-connect-generic
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Modifies the OIDC login button text.
 *
 * @link https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-login-button-text
 *
 * @param string $text The button text.
 *
 * @return string
 */
function oidc_keycloak_login_button_text( $text ) {

	$text = __( 'Login with Keycloak', 'oidc-keycloak-mu-plugin' );

	return $text;

}
add_filter( 'openid-connect-generic-login-button-text', 'oidc_keycloak_login_button_text', 10, 1 );

/**
 * Adds a new setting that allows configuration of the default role assigned
 * to users when no IDP role is provided.
 *
 * @link https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields
 *
 * @param array<mixed> $fields The array of settings fields.
 *
 * @return array<mixed>
 */
function oidc_keycloak_add_default_role_setting( $fields ) {

	// @var WP_Roles $wp_roles_obj
	$wp_roles_obj = wp_roles();
	// @var array<string> $roles
	$roles = $wp_roles_obj->get_names();

	// Setting to specify default user role when no role is provided by the IDP.
	$fields['default_user_role'] = array(
		'title'       => __( 'Default New User Role', 'oidc-keycloak-mu-plugin' ),
		'description' => __( 'Set the default role assigned to users when the IDP doesn\'t provide a role.', 'oidc-keycloak-mu-plugin' ),
		'type'        => 'select',
		'options'     => $roles,
		'section'     => 'user_settings',
	);

	return $fields;

}
add_filter( 'openid-connect-generic-settings-fields', 'oidc_keycloak_add_default_role_setting', 10, 1 );

/**
 * Adds new settings that allows mapping IDP roles to WordPress roles.
 *
 * @link https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields
 *
 * @param array<mixed> $fields The array of settings fields.
 *
 * @return array<mixed>
 */
function oidc_keycloak_role_mapping_setting( $fields ) {

	// @var WP_Roles $wp_roles_obj
	$wp_roles_obj = wp_roles();
	// @var array<string> $roles
	$roles = $wp_roles_obj->get_names();

	foreach ( $roles as $role ) {
		$fields[ 'oidc_idp_' . strtolower( $role ) . '_roles' ] = array(
			'title'       => sprintf( __( 'IDP Role for WordPress %ss', 'oidc-keycloak-mu-plugin' ), $role ),
			'description' => sprintf(
				__( 'Semi-colon(;) separated list of IDP roles to map to the %s WordPress role', 'oidc-keycloak-mu-plugin' ),
				$role
			),
			'type'        => 'text',
			'section'     => 'user_settings',
		);
	}

	return $fields;

}
add_filter( 'openid-connect-generic-settings-fields', 'oidc_keycloak_role_mapping_setting', 10, 1 );

/**
 * Set user role on based on IDP role after authentication.
 *
 * @param WP_User      $user       The authenticated user's WP_User object.
 * @param array<mixed> $user_claim The IDP provided Identity Token user claim array.
 *
 * @return void
 */
function oidc_keycloak_map_user_role( $user, $user_claim ) {

	// @var WP_Roles $wp_roles_obj
	$wp_roles_obj = wp_roles();
	// @var array<string> $roles
	$roles = $wp_roles_obj->get_names();
	// @var array<mixed> $settings
	$settings = get_option( 'openid_connect_generic_settings', array() );

	// Check the user claim for the `user-realm-role` key to lookup the WordPress role for mapping.
	if ( ! empty( $settings ) && ! empty( $user_claim['user-realm-role'] ) ) {
		// @var int $role_count
		$role_count = 0;

		foreach ( $user_claim['user-realm-role'] as $idp_role ) {
			foreach ( $roles as $role_id => $role_name ) {
				if ( ! empty( $settings[ 'oidc_idp_' . strtolower( $role_name ) . '_roles' ] ) ) {
					if ( in_array( $idp_role, explode( ';', $settings[ 'oidc_idp_' . strtolower( $role_name ) . '_roles' ] ) ) ) {
						$user->add_role( $role_id );
						$role_count++;
					}
				}
			}
		}

		if ( intval( $role_count ) == 0 ) {
			$user->set_role( $settings['default_user_role'] );
		}
	}

}
add_action( 'openid-connect-generic-update-user-using-current-claim', 'oidc_keycloak_map_user_role', 10, 2 );
add_action( 'openid-connect-generic-user-create', 'oidc_keycloak_map_user_role', 10, 2 );
