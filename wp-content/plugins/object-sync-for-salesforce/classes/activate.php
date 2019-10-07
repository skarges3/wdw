<?php
/**
 * Class file for the Object_Sync_Sf_Activate class.
 *
 * @file
 */

if ( ! class_exists( 'Object_Sync_Salesforce' ) ) {
	die();
}

/**
 * What to do when the plugin is activated
 */
class Object_Sync_Sf_Activate {

	protected $wpdb;
	protected $version;
	protected $slug;
	protected $option_prefix;
	protected $schedulable_classes;
	protected $queue;

	private $action_group_suffix;
	private $user_installed_version;

	/**
	* Constructor which sets up activate hooks
	*
	* @param object $wpdb
	* @param string $version
	* @param string $slug
	* @param string $option_prefix
	* @param array $schedulable_classes
	* @param object $queue
	*
	*/
	public function __construct( $wpdb, $version, $slug, $option_prefix = '', $schedulable_classes = array(), $queue = '' ) {
		$this->wpdb                = $wpdb;
		$this->version             = $version;
		$this->slug                = $slug;
		$this->option_prefix       = isset( $option_prefix ) ? $option_prefix : 'object_sync_for_salesforce_';
		$this->schedulable_classes = $schedulable_classes;
		$this->queue               = $queue;

		$this->action_group_suffix    = '_check_records';
		$this->user_installed_version = get_option( $this->option_prefix . 'db_version', '' );

		$this->add_actions();
	}

	/**
	* Activation hooks
	*/
	private function add_actions() {

		// on initial activation, run these hooks
		register_activation_hook( dirname( __DIR__ ) . '/' . $this->slug . '.php', array( $this, 'php_requirements' ) );
		register_activation_hook( dirname( __DIR__ ) . '/' . $this->slug . '.php', array( $this, 'wordpress_salesforce_tables' ) );
		register_activation_hook( dirname( __DIR__ ) . '/' . $this->slug . '.php', array( $this, 'add_roles_capabilities' ) );

		// this should run when the user is in the admin area to make sure the database gets updated
		add_action( 'admin_init', array( $this, 'wordpress_salesforce_update_db_check' ), 10 );

		// when users upgrade the plugin, run these hooks
		add_action( 'upgrader_process_complete', array( $this, 'check_for_action_scheduler' ), 10, 2 );
	}

	/**
	* Check for the minimum required version of php
	*/
	public function php_requirements() {
		if ( version_compare( PHP_VERSION, '5.6.20', '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( '<strong>This plugin requires PHP Version 5.6.20</strong> <br />Please contact your host to upgrade PHP on your server, and then retry activating the plugin.' );
		}
	}

	/**
	* Create database tables for Salesforce
	* This creates tables for fieldmaps (between types of objects) and object maps (between individual instances of objects).
	* Important requirement for developers: when you update the SQL for either of these tables, also update it in the documentation file located at /docs/troubleshooting-unable-to-create-database-tables.md and make sure that is included in your commit(s).
	*
	*/
	public function wordpress_salesforce_tables() {

		$charset_collate = $this->wpdb->get_charset_collate();

		$field_map_table = $this->wpdb->prefix . 'object_sync_sf_field_map';
		$field_map_sql   = "CREATE TABLE $field_map_table (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			label varchar(64) NOT NULL DEFAULT '',
			name varchar(64) NOT NULL DEFAULT '',
			wordpress_object varchar(128) NOT NULL DEFAULT '',
			salesforce_object varchar(255) NOT NULL DEFAULT '',
			salesforce_record_types_allowed longblob,
			salesforce_record_type_default varchar(255) NOT NULL DEFAULT '',
			fields longtext NOT NULL,
			pull_trigger_field varchar(128) NOT NULL DEFAULT 'LastModifiedDate',
			sync_triggers text NOT NULL,
			push_async tinyint(1) NOT NULL DEFAULT '0',
			push_drafts tinyint(1) NOT NULL DEFAULT '0',
			pull_to_drafts tinyint(1) NOT NULL DEFAULT '0',
			weight tinyint(1) NOT NULL DEFAULT '0',
			version varchar(255) NOT NULL DEFAULT '',
			PRIMARY KEY  (id),
			UNIQUE KEY name (name),
			KEY name_sf_type_wordpress_type (wordpress_object,salesforce_object)
		) ENGINE=InnoDB $charset_collate";

		$object_map_table = $this->wpdb->prefix . 'object_sync_sf_object_map';
		$object_map_sql   = "CREATE TABLE $object_map_table (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			wordpress_id varchar(32) NOT NULL,
			salesforce_id varbinary(32) NOT NULL DEFAULT '',
			wordpress_object varchar(128) NOT NULL DEFAULT '',
			created datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			object_updated datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			last_sync datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			last_sync_action varchar(128) DEFAULT NULL,
			last_sync_status tinyint(1) NOT NULL DEFAULT '0',
			last_sync_message varchar(255) DEFAULT NULL,
			PRIMARY KEY  (id),
			KEY wordpress_object (wordpress_object,wordpress_id),
			KEY salesforce_object (salesforce_id)
		) $charset_collate";

		if ( ! function_exists( 'dbDelta' ) ) {
			if ( ! is_admin() ) {
				return false;
			}
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		}

		// Note: see https://wordpress.stackexchange.com/questions/67345/how-to-implement-wordpress-plugin-update-that-modifies-the-database
		// When we run the dbDelta method below, "it checks if the table exists. What's more, it checks the column types. So if the table doesn't exist, it creates it, if it does, but some column types have changed it updates them, and if a column doesn't exists - it adds it."
		// This does not remove columns if we remove columns, so we'll need to expand beyond this in the future if that happens, although I think the schema is pretty solid now.
		$result_field_map  = dbDelta( $field_map_sql );
		$result_object_map = dbDelta( $object_map_sql );

		$remove_key_version = '1.8.0';
		if ( '' !== $this->user_installed_version && version_compare( $this->user_installed_version, $remove_key_version, '<' ) ) {
			$this->wpdb->query( "ALTER TABLE $object_map_table DROP INDEX salesforce" );
			$this->wpdb->query( "ALTER TABLE $object_map_table DROP INDEX salesforce_wordpress" );
			$result_key = true;
		}

		// store right now as the time for the plugin's activation
		update_option( $this->option_prefix . 'activate_time', current_time( 'timestamp', true ) );

		// utf8mb4 conversion.
		maybe_convert_table_to_utf8mb4( $field_map_table );
		maybe_convert_table_to_utf8mb4( $object_map_table );

		if ( '' === $this->user_installed_version || version_compare( $this->user_installed_version, $this->version, '<' ) ) {
			update_option( $this->option_prefix . 'db_version', $this->version );
		}

		if ( ! isset( $result_key ) && empty( $result_field_map ) && empty( $result_object_map ) ) {
			// No changes, database already exists and is up-to-date
			return;
		}

		return;

	}

	/**
	* Add roles and capabilities
	* This adds the configure_salesforce capability to the admin role
	*
	* It also allows other plugins to add the capability to other roles
	*
	*/
	public function add_roles_capabilities() {

		// by default, only administrators can configure the plugin
		$role = get_role( 'administrator' );
		$role->add_cap( 'configure_salesforce' );

		// hook that allows other roles to configure the plugin as well
		$roles = apply_filters( $this->option_prefix . 'roles_configure_salesforce', null );

		// for each role that we have, give it the configure salesforce capability
		if ( null !== $roles ) {
			foreach ( $roles as $role ) {
				$role = get_role( $role );
				$role->add_cap( 'configure_salesforce' );
			}
		}

	}

	/**
	* Check for database version
	* When the plugin is loaded in the admin, if the database version does not match the current version, perform these methods
	*
	*/
	public function wordpress_salesforce_update_db_check() {

		// user is running a version less than the current one
		if ( version_compare( $this->user_installed_version, $this->version, '<' ) ) {
			$this->wordpress_salesforce_tables();
		} else {
			return true;
		}
	}

	/**
	* Check whether the user has action scheduler tasks when they upgrade
	*
	* @param object $upgrader_object
	* @param array $hook_extra
	*
	* See https://developer.wordpress.org/reference/hooks/upgrader_process_complete/
	*
	*/
	public function check_for_action_scheduler( $upgrader_object, $hook_extra ) {

		// skip if this action isn't this plugin being updated
		if ( 'plugin' !== $hook_extra['type'] && 'update' !== $hook_extra['action'] && $hook_extra['plugin'] !== $this->slug ) {
			return;
		}

		// user is running a version less than 1.4.0
		$action_scheduler_version = '1.4.0';
		$previous_version         = get_transient( $this->option_prefix . 'installed_version' );
		if ( version_compare( $previous_version, $action_scheduler_version, '<' ) ) {
			// delete old options
			delete_option( $this->option_prefix . 'push_schedule_number' );
			delete_option( $this->option_prefix . 'push_schedule_unit' );
			delete_option( $this->option_prefix . 'salesforce_schedule_number' );
			delete_option( $this->option_prefix . 'salesforce_schedule_unit' );
			if ( '' === $this->queue ) {
				delete_transient( $this->option_prefix . 'installed_version' );
				return;
			}
			foreach ( $this->schedulable_classes as $key => $schedule ) {
				$schedule_name     = $key;
				$action_group_name = $schedule_name . $this->action_group_suffix;
				// exit if there is no initializer property on this schedule
				if ( ! isset( $this->schedulable_classes[ $schedule_name ]['initializer'] ) ) {
					continue;
				}
				// create new recurring task for action-scheduler to check for data to pull from Salesforce
				$this->queue->schedule_recurring(
					current_time( 'timestamp', true ), // plugin seems to expect UTC
					$this->queue->get_frequency( $schedule_name, 'seconds' ),
					$this->schedulable_classes[ $schedule_name ]['initializer'],
					array(),
					$action_group_name
				);
			}
			delete_transient( $this->option_prefix . 'installed_version' );
		}
	}

}
