# [OpenID Connect Keycloak SSO Integration](../home.md)

# Namespace: \
### Functions
<a name="method_oidc_keycloak_add_default_role_setting" class="anchor"></a>
####  oidc_keycloak_add_default_role_setting() : \array&lt;mixed&gt;

```
 oidc_keycloak_add_default_role_setting(\array&lt;mixed&gt;  $fields) : \array&lt;mixed&gt;
```

**Summary**

Adds a new setting that allows configuration of the default role assigned
to users when no IDP role is provided.

**Details:**
* File: [oidc-keycloak-custom.php](../files/oidc-keycloak-custom.md)
* See Also:
 * [https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields](https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields)
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>\array<mixed></code> | $fields  | The array of settings fields. |

**Returns:** \array<mixed>

##### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| package |  | OpenidConnectGeneric_MuPlugin |

<a name="method_oidc_keycloak_add_login_button_text_setting" class="anchor"></a>
####  oidc_keycloak_add_login_button_text_setting() : \array&lt;mixed&gt;

```
 oidc_keycloak_add_login_button_text_setting(\array&lt;mixed&gt;  $fields) : \array&lt;mixed&gt;
```

**Summary**

Adds a new setting that allows an Administrator to set the button text from
the plugin settings screen.

**Details:**
* File: [oidc-keycloak-custom.php](../files/oidc-keycloak-custom.md)
* See Also:
 * [https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields](https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields)
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>\array<mixed></code> | $fields  | The array of settings fields. |

**Returns:** \array<mixed>

##### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| package |  | OpenidConnectGeneric_MuPlugin |

<a name="method_oidc_keycloak_add_require_idp_role_setting" class="anchor"></a>
####  oidc_keycloak_add_require_idp_role_setting() : \array&lt;mixed&gt;

```
 oidc_keycloak_add_require_idp_role_setting(\array&lt;mixed&gt;  $fields) : \array&lt;mixed&gt;
```

**Summary**

Setting to indicate whether an IDP role mapping is required for user creation.

**Details:**
* File: [oidc-keycloak-custom.php](../files/oidc-keycloak-custom.md)
* See Also:
 * [https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields](https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields)
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>\array<mixed></code> | $fields  | The array of settings fields. |

**Returns:** \array<mixed>

##### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| package |  | OpenidConnectGeneric_MuPlugin |

<a name="method_oidc_keycloak_login_button_text" class="anchor"></a>
####  oidc_keycloak_login_button_text() : string

```
 oidc_keycloak_login_button_text(string  $text) : string
```

**Summary**

Modifies the OIDC login button text.

**Details:**
* File: [oidc-keycloak-custom.php](../files/oidc-keycloak-custom.md)
* See Also:
 * [https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-login-button-text](https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-login-button-text)
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>string</code> | $text  | The button text. |

**Returns:** string

##### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| package |  | OpenidConnectGeneric_MuPlugin |

<a name="method_oidc_keycloak_map_user_role" class="anchor"></a>
####  oidc_keycloak_map_user_role() : void

```
 oidc_keycloak_map_user_role(\WP_User  $user, \array&lt;mixed&gt;  $user_claim) : void
```

**Summary**

Set user role on based on IDP role after authentication.

**Details:**
* File: [oidc-keycloak-custom.php](../files/oidc-keycloak-custom.md)
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>\WP_User</code> | $user  | The authenticated user's WP_User object. |
| <code>\array<mixed></code> | $user_claim  | The IDP provided Identity Token user claim array. |

**Returns:** void

##### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| package |  | OpenidConnectGeneric_MuPlugin |

<a name="method_oidc_keycloak_role_mapping_setting" class="anchor"></a>
####  oidc_keycloak_role_mapping_setting() : \array&lt;mixed&gt;

```
 oidc_keycloak_role_mapping_setting(\array&lt;mixed&gt;  $fields) : \array&lt;mixed&gt;
```

**Summary**

Adds new settings that allows mapping IDP roles to WordPress roles.

**Details:**
* File: [oidc-keycloak-custom.php](../files/oidc-keycloak-custom.md)
* See Also:
 * [https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields](https://github.com/daggerhart/openid-connect-generic#openid-connect-generic-settings-fields)
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>\array<mixed></code> | $fields  | The array of settings fields. |

**Returns:** \array<mixed>

##### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| package |  | OpenidConnectGeneric_MuPlugin |

<a name="method_oidc_keycloak_user_creation_test" class="anchor"></a>
####  oidc_keycloak_user_creation_test() : boolean

```
 oidc_keycloak_user_creation_test(boolean  $result, \array&lt;mixed&gt;  $user_claim) : boolean
```

**Summary**

Determine whether user should be created using plugin settings & IDP identity.

**Details:**
* File: [oidc-keycloak-custom.php](../files/oidc-keycloak-custom.md)
##### Parameters:
| Type | Name | Description |
| ---- | ---- | ----------- |
| <code>boolean</code> | $result  | The plugin user creation test flag. |
| <code>\array<mixed></code> | $user_claim  | The authenticated user's IDP Identity Token user claim. |

**Returns:** boolean

##### Tags
| Tag | Version | Description |
| --- | ------- | ----------- |
| package |  | OpenidConnectGeneric_MuPlugin |


---


### Reports
* [Errors - 0](../reports/errors.md)
* [Markers - 0](../reports/markers.md)
* [Deprecated - 0](../reports/deprecated.md)

---

This document was automatically generated from source code comments on 2020-08-24 using [phpDocumentor](http://www.phpdoc.org/) and [fr3nch13/phpdoc-markdown](https://github.com/fr3nch13/phpdoc-markdown)
