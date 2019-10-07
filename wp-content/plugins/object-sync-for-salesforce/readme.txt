=== Object Sync for Salesforce ===
Contributors: minnpost, inn_nerds, jonathanstegall, benlk, rclations, harmoney
Donate link: https://www.minnpost.com/support/?campaign=7010G0000012fXGQAY
Tags: salesforce, sync, crm
Requires at least: 4.6
Tested up to: 5.2
Stable tag: 1.8.12
Requires PHP: 5.6.20
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Object Sync for Salesforce maps and syncs data between Salesforce objects and WordPress objects. It is based on the [Drupal Salesforce Suite](https://github.com/thinkshout/salesforce) (version 7.x-3.x-dev), but works in a very WordPress way.

== Description ==

This plugin maps and syncs data between Salesforce objects and WordPress content types. For any supported WordPress content types (e.g. post, page, user, or any custom content type in your installation), you can assign Salesforce objects that will be created / updated / deleted when the data in WordPress is saved, and the WordPress objects can be created / updated / deleted when the data in Salesforce is saved.

Both of these directions act upon any matching data that is created after the plugin is installed. For each such assignment, you choose which WordPress and Salesforce fields should be mapped to one another.

This plugin also includes developer hooks that allow for additional plugins to modify what data the plugin is working with, or what happens upon specific events.

== Installation ==

### WordPress

#### Prerequisites

To install the plugin in WordPress, your PHP environment needs the following:

1. At least version 5.6.20.
2. SSL support (this is required to connect to Salesforce).
3. A domain where WordPress is successfully running. For purposes of this documentation, we'll assume that you are using `https://<your site>`. You would use `https://www.example.com` instead, if your site was `www.example.com`.

#### Activate the plugin

In the Plugins list in WordPress, activate the plugin and find the settings link (you can also find this plugin's settings in the main Settings list in WordPress, under the Salesforce menu item once it is activated).

The plugin's settings URL is `https://<your site>/wp-admin/options-general.php?page=object-sync-salesforce-admin`.

### Salesforce

#### Prerequisites

You'll need to have access to a Salesforce developer account. This should come with Enterprise Edition, Unlimited Edition, or Performance Edition. Developers can register for a free Developer Edition account at [https://developer.salesforce.com/signup](https://developer.salesforce.com/signup).

We recommend using a Sandbox to set up this plugin first before running it in production.

For purposes of this documentation, we'll assume that your name, as defined in Salesforce, is Your Name. This is what you see at the top right of the browser window, when you are logged in.

#### Create an App

1. In Salesforce, go to `Your Name > Setup`. Then on the left sidebar, under `App Setup`, click `Create > Apps`. In the **Connected Apps** section of this page, click New to create a new app.
2. Enable OAuth Settings
3. Set the callback URL to: `https://<your site>/wp-admin/options-general.php?page=object-sync-salesforce-admin&tab=authorize` (must use HTTPS).
4. Select at least "Perform requests on your behalf at any time" for OAuth Scope as well as the appropriate other scopes for your application. Many setups will also need to select "Access and manage your data (api)" as one of these scopes.

#### Get the values for WordPress

After you save these settings, click Continue and you'll see the values for your new app. For WordPress, you'll need these values:

1. Consumer Key (in the screenshot, this value says "valuefromsalesforce")
2. Consumer Secret (you'll have to click "Click to reveal" to get this value)

### Connect the plugin to Salesforce

#### Settings

Go to the Settings tab for the plugin. It is the default URL that opens when you click Salesforce in the main Settings menu. Enter the values based on your Salesforce environment.

1. Consumer Key: (your value from above)
2. Consumer Secret: (your value from above)
3. Callback URL: `https://<your site>/wp-admin/options-general.php?page=object-sync-salesforce-admin&tab=authorize`
4. Login Base URL: For most Salesforce environments, you can use `https://test.salesforce.com` for sandbox, and `https://login.salesforce.com` for production.
5. Authorize URL Path: The plugin starts with a default of `/services/oauth2/authorize`. You should generally not have to change this.
6. Token URL Path: The plugin starts with a default of `/services/oauth2/token`. You should generally not have to change this.
7. Salesforce API Version: You should generally use the latest version your install has access to. This plugin starts with 42.0, but once it is authenticated the text field will be replaced with a dropdown of your available versions from which you can choose.
8. Limit Salesforce Objects: These allow you to indicate whether Salesforce should relate to objects that can't be triggered or updated via the API. Generally it's a good idea to have these boxes checked to avoid errors.
9. Salesforce Field Display Value: When mapping Salesforce fields, you can choose whether the plugin will display a field's Field Label (possibly a more user friendly value) or the API Name (which is always unique). Neither choice changes how the plugin functions on the back end, but making a choice can sometimes make the mapping choices easier to find.
10. Pull Throttle (seconds): This plugin starts with 5 seconds, but you can change it based on your server's needs.
11. Debug mode: This won't do anything until after the plugin has been authorized, but once it has you can use it to see more information about what the API is doing. **Don't check this in a production environment.**

Save the settings. If the values required are set, you'll see a message that says "Salesforce needs to be authorized to connect to this website. Use the Authorize tab to connect." You can use that link for the next steps.

##### Using constants for settings

You can set several of the above values as constants in your `wp-config.php` file. The plugin will always use a constant ahead of a database setting.

Supported constant names are:

1. OBJECT_SYNC_SF_SALESFORCE_CONSUMER_KEY
2. OBJECT_SYNC_SF_SALESFORCE_CONSUMER_SECRET
3. OBJECT_SYNC_SF_SALESFORCE_CALLBACK_URL
4. OBJECT_SYNC_SF_SALESFORCE_LOGIN_BASE_URL
5. OBJECT_SYNC_SF_SALESFORCE_AUTHORIZE_URL_PATH
6. OBJECT_SYNC_SF_SALESFORCE_TOKEN_URL_PATH
7. OBJECT_SYNC_SF_SALESFORCE_API_VERSION

Set them in `wp-config.php` like this:

`define('OBJECT_SYNC_SF_SALESFORCE_CONSUMER_KEY', 'valuefromsalesforce');`
`define('OBJECT_SYNC_SF_SALESFORCE_CONSUMER_SECRET', 'valuefromsalesforce');`
`define('OBJECT_SYNC_SF_SALESFORCE_CALLBACK_URL', 'https://<your site>/wp-admin/options-general.php?page=object-sync-salesforce-admin&tab=authorize');`
`define('OBJECT_SYNC_SF_SALESFORCE_LOGIN_BASE_URL', 'https://test.salesforce.com');`
`define('OBJECT_SYNC_SF_SALESFORCE_API_VERSION', '40.0');`
`define('OBJECT_SYNC_SF_SALESFORCE_AUTHORIZE_URL_PATH', '/services/oauth2/authorize');`
`define('OBJECT_SYNC_SF_SALESFORCE_TOKEN_URL_PATH', '/services/oauth2/token');`

For any value that is already defined in `wp-config.php`, the Settings screen will display "Defined in wp-config.php" in place of the text field.

#### Authorize

Go to the Authorize tab in the plugin's settings. If it is not yet authorized, you'll see a message that says "Salesforce needs to be authorized to connect to this website. Use the Authorize tab to connect."

The authorize tab will force you to be logged in using HTTPS, if you weren't already.

Steps:

1. Click the Connect to Salesforce button to authenticate WordPress with your Salesforce installation.
2. You may have to log into Salesforce.
3. Salesforce will ask you to allow access to the app (in these instructions, the name is WordPress Example), and will show you what permissions it needs.
4. Click Allow.
5. You'll be redirected back to WordPress. Don't do anything until you see a message that says "Salesforce is successfully authenticated."
6. The tab will display a "Disconnect from Salesforce" button which you can click at any time, and will also show a bit of basic information about your Salesforce environment (the available API versions and a basic table of Contacts.)
7. If you'd like to use a different Salesforce API version, go back to the Settings tab and pick your desired version from the dropdown.

### More documentation

There is extensive documentation of the plugin, including its developer hooks, [on GitHub](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/docs/readme.md). You can find a detailed [initial setup instruction](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/docs/initial-setup.md) document there as well.

== Frequently Asked Questions ==

### Mapping custom fields

WordPress stores metadata as key/value pairs in its database. Many plugins and themes use this method to store custom field data. Object Sync for Salesforce supports mapping these fields (many other plugins use non-standard methods, and this plugin may or may not support them).

There's a [helpful spreadsheet](https://docs.google.com/spreadsheets/d/1mSqienVYxLopTFGLPK0lGCJst2knKzXDtLQRgwjeBN8/edit#gid=3) (we are not affiliated with it, we just think it's useful) comparing various options for custom fields you can review. If the plugin you wish to use uses Meta-based Storage (listed in the spreadsheet), you should be able to use it with Object Sync for Salesforce, but how well they work together will vary. Plugins with full meta compatibility (also listed in the spreadsheet) may work the best, but you don't have to restrict yourself to those.

Object Sync for Salesforce, however, cannot see meta fields before the field has at least one value in the database. For example, if you have a "testfield" on your user object, it won’t be in the fieldmap options until there is at least one user that has a value for the field.

If you load Object Sync for Salesforce and then store data for a new meta field after this load, make sure you click the "Clear the plugin cache" link on the Fieldmaps tab.

### Using with ACF (Advanced Custom Fields)

Object Sync for Salesforce does not and will not "officially" support ACF because you don't have to use ACF to use WordPress or to use Salesforce. However, they are **generally** usable together.

Things to know:

1. See the answer above about custom fields. Any ACF field must have at least one value in the database before Object Sync for Salesforce can map it.
2. When you try to map an ACF field, you'll see one that has an underscore in front of it, and one that does not. This is because ACF uses both for its own purposes. As long as you map the ACF field that **does not** have the underscore in front of it, you should be able to get data to and from Salesforce. For example, you could map a `test_field` to a `Contact_description` field. The fieldmap screen will show a `_test_field` in the dropdown, but you should be able to safely ignore that, and only map `test_field`.

While we will not include code that only runs for ACF in this plugin, we would happily point to any add-on plugin that uses Object Sync for Salesforce hooks to build a more comprehensive integration with ACF for all users who install this plugin while they're running ACF.

### Using with WooCommerce

Object Sync for Salesforce doesn't have, and will not have, intentional support for WooCommerce. It kind of supports it, to the extent that WooCommerce uses WordPress' default ways of creating objects and data. WooCommerce is very complicated, and on top of that it often deviates from those default WordPress methods, and it's certainly possible that this plugin won't support it when it does.

This doesn't mean you can't use them together, but it does mean this plugin is not intentionally built for that purpose. Because WooCommerce is not a requirement to use WordPress, or to use Salesforce, it will never be built directly into Object Sync for Salesforce.

Object Sync for Salesforce does have abundant developer hooks, and WooCommerce has its own API, and it would be possible to build an add-on plugin to provide full support by integrating these (we would happily point to it for all users who install this plugin while they're running WooCommerce).

### Troubleshooting connection and authorization issues

If you are having trouble connecting the plugin to Salesforce, there are several ways to troubleshoot. Always check your PHP error logs first. More information may be available in [the plugin documentation](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/docs/readme.md).

**Missing Authorize tab**

If you load the plugin's Settings screen and you do not see an Authorize tab, this means there are required fields missing from your Settings tab. You must have (at least) accurate values for Consumer Key, Consumer Secret, Callback URL, Login Base URL, Authorize URL Path, Token URL Path, and Salesforce API Version.

**Error: invalid_client_id**

It can take a few minutes for a new app to be fully set up in Salesforce. If you get a `error=invalid_client_id&error_description=client%20identifier%20invalid` URL when you try to authorize with WordPress during the installation, wait a few minutes and then try again.

This error can also happen if the Salesforce Consumer Key is entered incorrectly in the plugin settings.

**Error: redirect_uri_mismatch**

This error usually means the Callback URL in the plugin settings does not match the Callback URL for the app in Salesforce. Typically, the URL is something like this: https://yoursite/wp-admin/options-general.php?page=object-sync-salesforce-admin&tab=authorize.

**Error(0)**

This error comes from Salesforce but the plugin is not able to detect it before the page loads. Usually it comes from one of these things:

1. The connection is down
2. The SSL is incorrect
3. The login base URL is incorrect

**Error: 400**

Sometimes Salesforce returns an unhelpful 400 error (perhaps with a `grant type not supported` message). 400 errors from Salesforce mean that the request couldn't be understood. This can happen if the Login base URL setting is using your instance name (ex https://clientname.lightning.force.com) rather than the more generic https://test.salesforce.com for sandboxes and https://login.salesforce.com for production instances. Salesforce will handle redirecting the plugin to the proper instance; you should always be able to use the generic URLs.

**Error: 401**

Sometimes Salesforce returns a 401 error. This means the session ID or OAuth token has expired. This can mean that you've already tried to authorize, but it failed, or that too much time has passed. Try to disconnect and reconnect the plugin. Also, make sure your Salesforce app has the proper permissions: "Access and manage your data (api)" and "Perform requests on your behalf at any time (refresh_token, offline_access)".

**Plugin redirects after logging in, but does not finish activating**

If the plugin allows you to authorize in Salesforce, but does not finish activating in WordPress, consider these possible issues:

1. Insufficient app permissions in Salesforce. Make sure the app's permissions are at least "Perform requests on your behalf at any time" for OAuth Scope as well as the appropriate other scopes for your application. Many setups will also need to select "Access and manage your data (api)" as one of these scopes. If you change permissions, give Salesforce a few minutes before trying to connect again.
2. The plugin may have been unable to create its required database tables. If you think this may be the case, refer to [this document](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/docs/troubleshooting-unable-to-create-database-tables.md) for the necessary SQL.
3. Mismatched settings between the plugin and the expected values in Salesforce.

### Troubleshooting object maps

If you are successfully authenticated with Salesforce, but you are unable to create object maps, there are several ways to troubleshoot. Always check your PHP error logs first. More information may be available in the [troubleshooting](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/docs/troubleshooting.md) section of the plugin's documentation.

**There are no Salesforce objects in the dropdown**

When there are no values in the list of Salesforce objects, this means the plugin can’t access any of the objects in your Salesforce. There are three likely causes for this:

- You need to change the OAuth scope on the app you created in Salesforce. For most uses with this plugin, you’ll want to use "Perform requests on your behalf at any time" and "Access and manage your data (api)."" If you do change these, you’ll need to wait several minutes before trying again, as Salesforce is rather slow on this.
- Your Salesforce objects might not be accessible to the Salesforce user who has authenticated with WordPress via this plugin.
- The Salesforce objects might have other restrictive permissions.

### Troubleshooting fieldmaps

If you are successfully authenticated with Salesforce, but you have a fieldmap that is not passing data, there are several ways to troubleshoot. Always check your PHP error logs first. More information may be available in the [troubleshooting](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/docs/troubleshooting.md) section of the plugin's documentation.

**Plugin configuration**

- Remember to clear the plugin cache on the Fieldmaps screen.
- If you are not able to push data to Salesforce, try with asynchronous checked, and without. This will tell you if your issue is related to the plugin's cron jobs.
- To inspect your cron jobs, use the [WP Crontrol](https://wordpress.org/plugins/wp-crontrol/) plugin. Make sure the Salesforce push and/or pull jobs are running as you expect them to, and make sure to check the Schedule screen to make sure the jobs are scheduled as they should be.

**Plugin logs**

- Make sure to use the Log Settings screen to configure logs. Once enabled, they are added to a custom post type called Logs in the WordPress menu.
- If the plugin tries to create or update data, but WordPress or Salesforce encounter errors, the plugin will always try to create a log entry. If you see entries, review the title and content of each.

**Plugin mapping errors**

- If the plugin fails in the middle of creating a map between two objects, a row may be created on the Mapping Errors screen. If it is a push error, it will tell you the WordPress object ID it was trying to map. If it is a pull error, it will tell you the Salesforce ID. **You should not leave these entries.**

### Reporting bugs, feature suggestions, and other feedback

If you'd like to suggest a feature, or if you think you've encountered a bug, you can [create an issue](https://github.com/minnpost/object-sync-for-salesforce/issues) on our GitHub repository. We actively add our own issues to the list, and comment on their progress.

### Contributing to plugin development

We welcome contributions to this project from other developers. See our [contributing guidelines](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/contributing.md).

### Plugin documentation

There is extensive documentation of this plugin, including its developer hooks, [on GitHub](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/docs/readme.md).

### Getting support using this plugin

We make an effort to answer support requests in the [WordPress plugin forum](https://wordpress.org/support/plugin/object-sync-for-salesforce/). Please do not send them by email.

While MinnPost's nonprofit newsroom does welcome [donations](https://www.minnpost.com/support/?campaign=7010G0000012fXGQAY) to support our work, this plugin does not have a paid version.

### Finding other options to sync Salesforce and WordPress

This plugin can be relatively complicated, and sometimes other plugins can effectively integrate Salesforce and WordPress, especially if there are more limited, specific requirements. If one of these can meet those requirements, use it. We're happy to link to additional choices here, as well.

- [WordPress-to-Lead for Salesforce CRM](https://appexchange.salesforce.com/listingDetail?listingId=a0N30000003GxgkEAC) can be installed through the Salesforce AppExchange. It allows you to run a contact form which users on your WordPress site can submit, and the results are added to Salesforce as a Lead object.
- [Brilliant Web-to-Lead for Salesforce](https://wordpress.org/plugins/salesforce-wordpress-to-lead/) can be installed through the WordPress plugin directory. This is rather similar to the first option, but is a bit more customizable. By customizable, you can select the fields in WordPress and theme it in your WordPress theme.
- [Gravity Forms Salesforce Add-on](https://wordpress.org/plugins/gravity-forms-salesforce/) can be installed through the WordPress plugin directory. It is quite powerful, as it can send form submissions from your WordPress site to Salesforce as whatever object you need. It's important to mention that this works for any form created with the [Gravity Forms](http://www.gravityforms.com/) plugin. It's also important to mention that this does not sync data back from Salesforce into Wordpress.
- **Third party integration apps** such as [Zapier](https://zapier.com/) are subscription-based, paid ways to integrate different systems, and they offer differing amounts of customizability. They will usually sync in both directions, so in this case from WordPress to Salesforce and vice versa. The only limitations of something like this are the cost over time, and the possible vulnerability of basing an integration on a third party that could, at some point, go away.
- [Visualforce](https://developer.salesforce.com/page/An_Introduction_to_Visualforce) If you are or have a Salesforce developer, you can build MVC based applications that integrate with Salesforce. It would be possible to build a system that uses, for example, the [WordPress REST API](https://developer.wordpress.org/rest-api/) to send and receive data to and from WordPress. This could be, in many ways, the flip side of what our plugin here does, but the complexity would be the same if the scope was the same.
- **Build other integrations in WordPress** this plugin focuses on the Salesforce REST API, as it covers the integration needs we have. Salesforce also has many other developer options: the SOAP API (we hope to incorporate this into Object Sync for Salesforce at some point), the Bulk API, and the Metadata API. Developers could extend this plugin to integrate with one of these. We would welcome any pull requests!

== Changelog ==

* 1.8.12 (2019-09-19)
    * Bug fix: fix the `object_sync_for_salesforce_pull_query_modify` filter to prevent SOQL query properties from being added multiple times.
    * Developers: clean up Travis config to stop its builds from failing.

* 1.8.11 (2019-09-17)
    * Bug fix: fix Ajax call for pushing data to Salesforce, and allow it to return JSON instead of echoing it. Thanks to WordPress user @graces25 for the report and the fix.
    * Maintenance: use the log entry status variable in the title of all the log entries.
    * Maintenance: when a fieldmap doesn't allow an operation, log it as a notice instead of an error.
    * Developers: add the WordPress object type to the object_sync_for_salesforce_wordpress_object_data filter, allowing developers to alter WordPress data by the current object type. Thanks to GitHub user @DonnyVO for this work.

* 1.8.10 (2019-09-12)
    * Bug fix: Prevent PHP errors when API requests on empty Salesforce objects also return errors
    * Bug fix: Fix authorize settings tab to prevent errors when there are no Salesforce Contacts (#303)
    * Bug fix: Fix activation issue with $query object that occurred in some environments. Thanks to @yetanotherse for the fix.
    * Maintenance: update some multiline function calls to match current WPCS standards.

* 1.8.9 (2019-09-03)
    * Feature: Provide an optional setting for how many log entries to clear at a time.
    * Bug fix: Unify push success hook parameters so they match the (until now, inconsistently incorrect) documentation.
    * Bug fix: Prevent duplicate admin display when multiple plugins try to filter the admin for logs.

* 1.8.8 (2019-08-26)
    * Feature: Make plugin logs sortable and filterable by type value.
    * Feature: Add Salesforce info block to user profiles that have the `configure_salesforce` capability. Thanks to @ddoddsr for the report and the fix.
    * Bug fix: Fix log scheduling so it resets the schedule when the log settings change.
    * Bug fix: Fix log slug generation so it stops worrying about unique slugs every time.
    * Maintenance: Fix docs links in All Developer Hooks. Thanks to @ddoddsr for the fix.

* 1.8.7 (2019-07-22)
    * Bug fix: Stop the plugin from breaking when SOAP is missing from the server. Thanks to GitHub user @Ethanb00 for the report.
    * Fix issues with pull success method/docs. Thanks to @mistermarco for the report and the fix.
    * Maintenance: Improve documentation in various places, notably for object map issues caused by Salesforce permissions.
    * Maintenance: Update Gulp to 4.x.

* 1.8.6 (2019-05-13)
    * Feature: Add object type parameter to `push_update_params_modify` developer hook.
    * Maintenance: Centralize documentation of SQL table structure.
    * Maintenance: Replace the various calls to create/update metadata with just one for easier management.
    * Maintenance: Update supported WordPress version to 5.2 and PHP version to 5.6.20 to match the new minimum for WordPress. 
    * Developers: Update ActionScheduler to 2.2.5.

* 1.8.5 (2019-03-30)
    * Bug fix: This fixes a possible issue in which the plugin would fail to realize that its database version was up to date.

* 1.8.4 (2019-03-27)
    * Bug fix: Dates used by The Event Calendar plugin were not correctly saved. This could cause TEC events not to appear on the calendar. Thanks to @vajeshrathor for reporting this issue. The fix also improves overall date formatting within this plugin.
    * Bug fix: The edit profile screen for users mapped to Salesforce could incorrectly be changed to a blank value, which did not delete the map. This release adds a delete link for this purpose.
    * Bug fix: When doing error logging for metadata, the plugin was not always correctly checking for success, and could have false positives for errors.

* 1.8.3 (2019-03-17)
    * Bug fix: In what seemed to be rare cases, manual push or pull requests from the WordPress admin were failing to actually save any data.

* 1.8.2 (2019-03-16)
    * Bug fix: 1.8.0 accidentally disabled a developer filter. This re-enables it.

* 1.8.1 (2019-03-16)
    * Bug fix: 1.8.0 introduced a possible issue with checking for updated records.

* 1.8.0 (2019-03-15)
    * New: we can now map multiple WordPress objects to the same Salesforce object. Each WordPress record will update the Salesforce record, and the Salesforce record can update each WordPress record, all according to the fieldmap. Thanks to WordPress forum user @joecanas1 for the initial report some time ago.
    * New: if Soap is enabled in the plugin settings, it is capable of responding to merge events. For example, if two Contacts are merged in Salesforce and a user is mapped to the old one, it will become mapped to the new one.
    * New: WordPress 5.1 changed its new user notification parameters. Thanks to GitHub user @synthetiv for the fix.
    * New: we've added a few additional debug log entries.
    * New: there is better error checking on saving metadata for users, posts, and comments.
    * Bug fix: 1.7.0 introduced an activation issue that this release fixes. There is also improved database versioning that occurs upon activation.
    * Bug fix: a bug existed in fieldmaps that had a Salesforce field like a record ID, which cannot be pushed to Salesforce but can be pulled from Salesforce, but the sync was failing. Thanks to WordPress forum user @walexparadis for the report on this.
    * Developers: we've updated our included version of ActionScheduler to 2.2.1.
    * Developers: the `load_by_wordpress` and `load_by_salesforce` methods on the `salesforce_mapping` class have been deprecated in favor of `load_all_by_wordpress` and `load_all_by_salesforce`, though the deprecated methods have not been removed.

See our [full changelog](https://github.com/MinnPost/object-sync-for-salesforce/blob/master/changelog.md) on GitHub or in changelog.txt in the installed plugin.
