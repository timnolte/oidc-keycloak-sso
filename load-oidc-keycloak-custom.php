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

require_once __DIR__ . './oidc-keycloak-custom/oidc-keycloak-custom.php';
