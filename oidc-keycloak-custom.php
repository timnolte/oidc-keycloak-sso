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

	// @var array<mixed> $settings
	$settings = get_option( 'openid_connect_generic_settings', array() );

	$text = ( ! empty( $settings['oidc_login_button_text'] ) ) ? strval( $settings['oidc_login_button_text'] ) : __( 'Login with Keycloak', 'oidc-keycloak-mu-plugin' );

	return $text;

}
add_filter( 'openid-connect-generic-login-button-text', 'oidc_keycloak_login_button_text', 10, 1 );

/**
 * Adds a new setting that allows an Administrator to set the button text from
 * the plugin settings screen.
 *
 * @link https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields
 *
 * @param array<mixed> $fields The array of settings fields.
 *
 * @return array<mixed>
 */
function oidc_keycloak_add_login_button_text_setting( $fields ) {

	// @var array<mixed> $field_array
	$field_array = array(
		'oidc_login_button_text' => array(
			'title'       => __( 'Login Button Text', 'oidc-keycloak-mu-plugin' ),
			'description' => __( 'Set the login button label text.', 'oidc-keycloak-mu-plugin' ),
			'type'        => 'text',
			'section'     => 'client_settings',
		),
	);

	// Prepend the field array with the new field to push it to the top of the settings screen.
	return $field_array + $fields;

}
add_filter( 'openid-connect-generic-settings-fields', 'oidc_keycloak_add_login_button_text_setting', 10, 1 );

/**
 * Setting to indicate whether an IDP role mapping is required for user creation.
 *
 * @link https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields
 *
 * @param array<mixed> $fields The array of settings fields.
 *
 * @return array<mixed>
 */
function oidc_keycloak_add_require_idp_role_setting( $fields ) {

	$fields['require_idp_user_role'] = array(
		'title'       => __( 'Valid IDP User Role Required', 'oidc-keycloak-mu-plugin' ),
		'description' => __( 'When enabled, this will prevent users from being created if they don\'t have a valid mapped IDP to WordPress role.', 'oidc-keycloak-mu-plugin' ),
		'type'        => 'checkbox',
		'section'     => 'user_settings',
	);

	return $fields;

}
add_filter( 'openid-connect-generic-settings-fields', 'oidc_keycloak_add_require_idp_role_setting', 10, 1 );

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
	// Prepend a blank role as the default.
	array_unshift( $roles, '-- None --' );

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

	// Mapping setting for each role
	foreach ( $roles as $role ) {
		$roleName = strtolower($role);
		$fields[ 'oidc_idp_' . $roleName . '_roles' ] = array(
			'title'       => sprintf( __( 'IDP Role for WordPress %ss', 'oidc-keycloak-mu-plugin' ), $role ),
			'description' => sprintf(
				__( 'Semi-colon(;) separated list of IDP roles to map to the %s WordPress role. By default, will search the IDP role in claim \'user-realm-role\'', 'oidc-keycloak-mu-plugin' ),
				$role
			),
			'example'     => "'wp-$roleName' if user token has 'wp-$roleName' in the default claim. 'resource_access.\$client_id.roles.$roleName' if you created a role '$roleName' for the associated client in Keycloak.",
			'type'        => 'text',
			'section'     => 'user_settings',
		);
	}

	return $fields;

}
add_filter( 'openid-connect-generic-settings-fields', 'oidc_keycloak_role_mapping_setting', 10, 1 );

/**
 * Determine whether user should be created using plugin settings & IDP identity.
 *
 * @param bool         $result     The plugin user creation test flag.
 * @param array<mixed> $user_claim The authenticated user's IDP Identity Token user claim.
 *
 * @return bool
 */
function oidc_keycloak_user_creation_test( $result, $user_claim ) {

	// @var array<mixed> $settings
	$settings = get_option( 'openid_connect_generic_settings', array() );

	// If the custom IDP role requirement setting is enabled validate user claim.
	if ( ! empty( $settings['require_idp_user_role'] ) && boolval( $settings['require_idp_user_role'] ) ) {
		// The default is to not create an account unless a mapping is found.
		$result = false;
		// @var WP_Roles $wp_roles_obj
		$wp_roles_obj = wp_roles();
		// @var array<string> $roles
		$roles = $wp_roles_obj->get_names();

		// Check the user claim for the `user-realm-role` key to lookup the WordPress role mapping.
		if ( ! empty( $settings ) && ! empty( $user_claim['user-realm-role'] ) ) {
			foreach ( $user_claim['user-realm-role'] as $idp_role ) {
				foreach ( $roles as $role_id => $role_name ) {
					if ( ! empty( $settings[ 'oidc_idp_' . strtolower( $role_name ) . '_roles' ] ) ) {
						if ( in_array( $idp_role, explode( ';', $settings[ 'oidc_idp_' . strtolower( $role_name ) . '_roles' ] ) ) ) {
							$result = true;
						}
					}
				}
			}
		}
	}

	return $result;

}
add_filter( 'openid-connect-generic-user-creation-test', 'oidc_keycloak_user_creation_test', 10, 2 );

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
	// @var array<string> $idp_roles
	$idp_roles = [];

	if ( ! empty( $settings ) )
	{
		// @var int $role_count
		$role_count = 0;

		// Loop on roles existing in Wordpress
		foreach ( $roles as $role_id => $role_name )
		{
			// Separated multiples values given
			$settingsRoles = explode( ';', $settings[ 'oidc_idp_' . strtolower( $role_name ) . '_roles' ] );

			// Loop on roles provided in settings
			foreach ( $settingsRoles as $settingRole )
			{
				error_log("[INFO] Evaluating setting '$settingRole' for role $role_name !");

				// Nested value
				if( str_contains($settingRole, '.') )
				{
					if( str_contains($settingRole, "\$client_id") )
					{
						// Support getting client_id dynamically
						//
						// e.g. if 'client_id' is 'wp-test' and
						// 		setting is 'resource_access.$client_id.roles.editor'
						// 		treat it as 'resource_access.wp-test.roles.editor'
						//
						$settingRole = str_replace("\$client_id", $settings['client_id'], $settingRole);
					}

					// Multi-level claim
					// > Each part of the string is a level
					// > Last part of the string is the value
					//
					// e.g: 'realm_access.roles.admin' will search for 'admin'
					//		in 'user_claim: { realm_access: { roles: [ -> here <- ] } }'
					//
					$claim_levels = explode('.', $settingRole);
					$claim_levels_count = count($claim_levels) - 1;

					// Initialize varibles (= top-level of the token)
					$claim_index = 0;
					$claim_source = $user_claim;

					do
					{
						$claim_name = $claim_levels[$claim_index];

						// Search for the claim at that sublevel of the token
						if( array_key_exists($claim_name, $claim_source) )
						{
							// Update variables for next loop (= next sub-level of the token)
							$claim_source = $claim_source[$claim_name];
							$claim_index++;
						}
						else
						{
							// Log error, empty variables and exit loop
							error_log("[WARN] Unable to find claim '$claim_name' inside \$user_claim !");
							$claim_source = [];
							break;
						}
					}
					while ($claim_index < $claim_levels_count);

					// Get values from the nested and/or custom claim
					$idp_roles = $claim_source;
					$idp_claim = $claim_levels[$claim_index];
				}
				else
				{
					$claim_name = 'user-realm-role'; // default plugin value
					$claim_source = $user_claim;

					if( array_key_exists($claim_name, $claim_source) )
					{
						// Get values from the default claim
						$idp_roles = $claim_source[$claim_name];
						$idp_claim = $settingRole;
					}
					else
					{
						// Log error, empty variables and exit loop
						error_log("[WARN] Unable to find claim '$claim_name' inside \$user_claim !");
					}
				}

				// Search for the value (from setting) inside the claim roles
				if ( ! empty( $idp_roles ) && in_array( $idp_claim, $idp_roles ) )
				{
					error_log("[INFO] Found value '$idp_claim' in claim '$claim_name' inside \$user_claim !");
					error_log("[INFO] Adding role $role_name to that user !");

					$user->add_role( $role_id );
					$role_count++;
				}
			}

			error_log("---"); // separate line
		}

		// Add default role if no other role was found/added
		if ( intval( $role_count ) == 0 && ! empty( $settings['default_user_role'] ) )
		{
			if ( boolval( $settings['default_user_role'] ) )
			{
				error_log("[INFO] Adding default role " . $settings['default_user_role'] . " to that user !");

				$user->set_role( $settings['default_user_role'] );
			}
		}
	}

}
add_action( 'openid-connect-generic-update-user-using-current-claim', 'oidc_keycloak_map_user_role', 10, 2 );
add_action( 'openid-connect-generic-user-create', 'oidc_keycloak_map_user_role', 10, 2 );
