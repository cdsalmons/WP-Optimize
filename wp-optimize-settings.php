<?php
# --------------------------------------- #
# prevent file from being accessed directly
# --------------------------------------- #
if ( ! defined( 'WPINC' ) ) {
	die;
}

$GLOBALS['wpo_auto_options'] = get_option('wp-optimize-auto');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	check_admin_referer( 'wpo_settings' );

    // …
	if (isset($_POST["enable-schedule"])) {
		update_option( OPTION_NAME_SCHEDULE, 'true' );
		wpo_cron_deactivate();

			/* if (!wp_next_scheduled('wpo_cron_event2')) {
				wp_schedule_event(time(), 'wpo_weekly', 'wpo_cron_event2');
				add_filter('cron_schedules', 'wpo_cron_update_sched');

			}	 */
			if (isset($_POST["schedule_type"])) {
				$schedule_type = $_POST['schedule_type'];
				update_option( OPTION_NAME_SCHEDULE_TYPE, $schedule_type );

			} else {
				update_option( OPTION_NAME_SCHEDULE_TYPE, 'wpo_weekly' );
			}
			wpo_cron_activate();
            add_action('wpo_cron_event2', 'wpo_cron_action');
            //wpo_debugLog('We are at setting page form submission and reached wpo_cron_activate()');
		} else {
		update_option( OPTION_NAME_SCHEDULE, 'false' );
		update_option( OPTION_NAME_SCHEDULE_TYPE, 'wpo_weekly' );
		wpo_cron_deactivate();

		}



	if (isset($_POST["enable-retention"])) {
		$retention_period = $_POST['retention-period'];
		update_option( OPTION_NAME_RETENTION_ENABLED, 'true' );
		update_option( OPTION_NAME_RETENTION_PERIOD, $retention_period );

	} else {
		update_option( OPTION_NAME_RETENTION_ENABLED, 'false' );
	}

	if (isset($_POST["enable-admin-bar"])) {
		update_option( OPTION_NAME_ENABLE_ADMIN_MENU, 'true' );
	} else {
		update_option( OPTION_NAME_ENABLE_ADMIN_MENU, 'false' );
	}
	if (isset($_POST["enable-email"])) {
		update_option( OPTION_NAME_ENABLE_EMAIL, 'true' );
	} else {
		update_option( OPTION_NAME_ENABLE_EMAIL, 'false' );
	}
	if (isset($_POST["enable-email-address"])) {
		update_option( OPTION_NAME_ENABLE_EMAIL_ADDRESS, wp_unslash( $_POST["enable-email-address"] ) );
	} else {
		update_option( OPTION_NAME_ENABLE_EMAIL_ADDRESS, get_bloginfo ( 'admin_email' ) );
	}

        
    if( isset($_POST['wp-optimize-settings']) ) {
    	$new_options = $_POST['wp-optimize-auto'];
    	$bool_opts = array( 'revisions', 'drafts', 'spams', 'unapproved', 'transient', 'postmeta', 'tags', 'optimize' );

        foreach($bool_opts as $key) {
    		$new_options[$key] = !empty( $new_options[$key] ) ? 'true' : 'false';
    	}
    	update_option( 'wp-optimize-auto', $new_options);

        $wpo_auto_options = get_option('wp-optimize-auto');

    }

	if( isset($_POST['wp-optimize-disable-enable-trackbacks']) ) {

		if ($_POST['wp-optimize-disable-enable-trackbacks'] == "0") {
			wpo_disableLinkbacks('trackbacks');
		echo '<div id="message" class="updated fade">';
		echo '<strong>'._e('Trackbacks disabled on all current and previously published posts','wp-optimize').'</strong></div>';
		}

		if ($_POST['wp-optimize-disable-enable-trackbacks'] == "1") {
			wpo_enableLinkbacks('trackbacks');
		echo '<div id="message" class="updated fade">';
		echo '<strong>'._e('Trackbacks enabled on all current and previously published posts','wp-optimize').'</strong></div>';
		}
	} //end if isset

	if( isset($_POST['wp-optimize-disable-enable-comments']) ) {

		if ($_POST['wp-optimize-disable-enable-comments'] == "0") {
			wpo_disableLinkbacks('comments');
		echo '<div id="message" class="updated fade">';
		echo '<strong>'._e('Comments disabled on all current and previously published posts','wp-optimize').'</strong></div>';
		}

		if ($_POST['wp-optimize-disable-enable-comments'] == "1") {
			wpo_enableLinkbacks('comments');
		echo '<div id="message" class="updated fade">';
		echo '<strong>'._e('Comments enabled on all current and previously published posts','wp-optimize').'</strong></div>';
		}
	} //endif isset


	echo '<div id="message" class="updated fade">';
    echo '<strong>'._e('Settings updated','wp-optimize').'</strong></div>';



}



?>

<div class="wpo_section wpo_group">
<form action="#" method="post" enctype="multipart/form-data" name="settings_form" id="settings_form">
<?php wp_nonce_field( 'wpo_settings' ); ?>

<div class="wpo_col wpo_span_1_of_3">
		<div class="postbox">
			<div class="inside">
				<h3><?php _e('General Settings', 'wp-optimize'); ?></h3>
				   <p>
				   <input name="enable-retention" id="enable-retention" type="checkbox" value ="true" <?php echo get_option(OPTION_NAME_RETENTION_ENABLED) == 'true' ? 'checked="checked"':''; ?> />
				   <?php
				   echo '<label>';
				   printf(__('Keep last %s weeks data', 'wp-optimize'),
					'<select id="retention-period" name="retention-period">
						<option value="'.esc_attr( get_option(OPTION_NAME_RETENTION_PERIOD, '2') ).'">'.esc_html( get_option(OPTION_NAME_RETENTION_PERIOD,'2') ).'</option>
						<option value="2">2</option>
						<option value="4">4</option>
						<option value="6">6</option>
						<option value="8">8</option>
						<option value="10">10</option>
					</select>'
					);
				   echo '</label>';
				   ?>
				   <br />
				   <small><?php
							_e('This option will retain the last selected weeks data and remove any garbage data before that period. This will also affect Auto Clean-up process', 'wp-optimize');
							?>
					</small>
					</p>

			<p>
			<label>
				<input name="enable-admin-bar" id="enable-admin-bar" type="checkbox" value ="true" <?php echo get_option(OPTION_NAME_ENABLE_ADMIN_MENU, 'false') == 'true' ? 'checked="checked"':''; ?> />
				<?php
				_e('Enable admin bar link', 'wp-optimize');
				echo ' ';
				echo '<a href="?page=WP-Optimize&tab=wp_optimize_settings">';
				_e('(Click here to refresh)', 'wp-optimize');
				echo '</a>'
				?>
			</label>
				<br />
				<small><?php
						_e('This option will put WP-Optimize link on the top admin bar (default is off). Requires page refresh.', 'wp-optimize');
						?>
				</small>
			</p>
				<h3><?php _e('Trackback/Comments Actions', 'wp-optimize'); ?></h3>
			<p>
				<?php _e('Disable/Enable Trackbacks', 'wp-optimize'); ?><br />
				<select id="wp-optimize-disable-enable-trackbacks" name="wp-optimize-disable-enable-trackbacks">
					<option value="-1"><?php _e('SELECT', 'wp-optimize'); ?></option>
					<option value="0"><?php _e('Disable', 'wp-optimize'); ?></option>
					<option value="1"><?php _e('Enable', 'wp-optimize'); ?></option>
				</select>
				<br /><br />
				<small>
				<?php
				_e('This will disable/enable Trackbacks on all your current and previously published posts', 'wp-optimize');
				?>
				</small>
			</p>

			<p>
				<?php _e('Disable/Enable Comments', 'wp-optimize'); ?><br />
				<select id="wp-optimize-disable-enable-comments" name="wp-optimize-disable-enable-comments">
					<option value="-1"><?php _e('SELECT', 'wp-optimize'); ?></option>
					<option value="0"><?php _e('Disable', 'wp-optimize'); ?></option>
					<option value="1"><?php _e('Enable', 'wp-optimize'); ?></option>
				</select>
				<br /><br />
				<small>
				<?php
				_e('This will disable/enable Comments on all your current and previously published posts', 'wp-optimize');
				?>
				</small>
			</p>

			<p>
			<a href="https://wordpress.org/plugins/youtube-embed-plus/" target="_blank">
			<img src="<?php echo WPO_PLUGIN_URL ;?>embedplus.png" style="float: left;margin-right: 7px;"> <?php _e('Optimize YouTube embedding with this free plugin on WordPress.org', 'wp-optimize'); ?> &raquo;</a>
	</p>

			<p>
			<input class="button-primary" type="submit" name="wp-optimize-settings1" value="<?php _e('SAVE SETTINGS', 'wp-optimize'); ?>" />
			</p>
			</div>
		</div>
</div>


<div class="wpo_col wpo_span_1_of_3">
	<div class="postbox">
		<div class="inside">
			<h3><?php _e('Auto Clean-up Settings', 'wp-optimize'); $wpo_auto_options = get_option('wp-optimize-auto');?></h3>
			<p>
			<input name="enable-schedule" id="enable-schedule" type="checkbox" value ="true" <?php echo get_option(OPTION_NAME_SCHEDULE) == 'true' ? 'checked="checked"':''; ?> />
				<?php _e('Enable scheduled clean-up and optimization (Beta feature!)', 'wp-optimize'); ?>
				<br /><br />
				<?php _e('Select schedule type (default is Weekly)', 'wp-optimize'); ?><br />
				<select id="schedule_type" name="schedule_type">
					<option value="<?php echo esc_attr( get_option(OPTION_NAME_SCHEDULE_TYPE, 'wpo_weekly') ); ?>">
					<?php
					$last_schedule = get_option(OPTION_NAME_SCHEDULE_TYPE,'wpo_weekly');
					switch ($last_schedule) {
						case "wpo_daily":
							_e('Everyday', 'wp-optimize');
							break;

						case "wpo_weekly":
							_e('Every week', 'wp-optimize');
							break;

						case "wpo_otherweekly":
							_e('Every other week (every 14 days)', 'wp-optimize');
							break;

						case "wpo_monthly":
							_e('Every month (every 31 days)', 'wp-optimize');
							break;

						default:
							_e('Every week', 'wp-optimize');
							break;
					}
					?>

					</option>
					<option value="wpo_daily"><?php _e('Everyday', 'wp-optimize'); ?></option>
					<option value="wpo_weekly"><?php _e('Every week', 'wp-optimize'); ?></option>
					<option value="wpo_otherweekly"><?php _e('Every other week (every 14 days)', 'wp-optimize'); ?></option>
					<option value="wpo_monthly"><?php _e('Every month (every 31 days)', 'wp-optimize'); ?></option>
				</select>
				<br /><br />
				<small><?php
						_e('Automatic cleanup will perform the following:', 'wp-optimize');
						echo '<br/>';
						_e('Remove revisions, auto drafts, posts/comments in trash, transient options. After that it will optimize the db.', 'wp-optimize');
						?>
						</small>
			</p>

   <p>
   <?php
	_e('These options will only work if the automatic clean-up schedule has been enabled','wp-optimize');
	?>
	</p>

	<p>
   <input name="wp-optimize-auto[revisions]" id="wp-optimize-auto[revisions]" type="checkbox" value="true" <?php echo $wpo_auto_options['revisions'] == 'true' ? 'checked="checked"':''; ?> /> <?php _e('Remove auto revisions', 'wp-optimize'); ?>
   </p>

   <p>
   <input name="wp-optimize-auto[drafts]" id="wp-optimize-auto[drafts]" type="checkbox" value="true" <?php echo $wpo_auto_options['drafts'] == 'true' ? 'checked="checked"':''; ?> /> <?php _e('Remove auto drafts', 'wp-optimize'); ?>
   </p>

   <p>
   <input name="wp-optimize-auto[spams]" id="wp-optimize-auto[spams]" type="checkbox" value="true" <?php echo $wpo_auto_options['spams'] == 'true' ? 'checked="checked"':''; ?> /> <?php _e('Remove spam comments', 'wp-optimize'); ?>
   </p>

   <p>
   <input name="wp-optimize-auto[unapproved]" id="wp-optimize-auto[unapproved]" type="checkbox" value="true" <?php echo $wpo_auto_options['unapproved'] == 'true' ? 'checked="checked"':''; ?> /> <?php _e('Remove unapproved comments', 'wp-optimize'); ?>
   </p>

   <p>
   <span style="color: red;">
   <input name="wp-optimize-auto[transient]" id="wp-optimize-auto[transient]" type="checkbox" value="true" <?php echo $wpo_auto_options['transient'] == 'true' ? 'checked="checked"':''; ?> /> <?php _e('Remove transient options', 'wp-optimize'); ?>
   </p>


   <!--   <input name="wp-optimize-auto[postmeta]" id="wp-optimize-auto[postmeta]" type="checkbox" value="true" <?php echo $wpo_auto_options['postmeta'] == 'true' ? 'checked="checked"':''; ?> /> <?php _e('Remove orphaned post meta', 'wp-optimize'); ?>
   <br /><br />-->

   <!--
   <input name="wp-optimize-auto[tags]" id="wp-optimize-auto[tags]" type="checkbox" value="true" <?php echo $wpo_auto_options['tags'] == 'true' ? 'checked="checked"':''; ?> /> <?php _e('Remove unused tags', 'wp-optimize'); ?>
   </span>
   <br /><br />
	-->

	<p>
   <input name="wp-optimize-auto[optimize]" id="wp-optimize-auto[optimize]" type="checkbox" value="true" <?php echo $wpo_auto_options['optimize'] == 'true' ? 'checked="checked"':''; ?> /> <b><?php _e('Optimize database', 'wp-optimize'); ?></b>
	</p>

    <?php
//        if (WPO_TABLE_TYPE == 'InnoDB'){
            echo '<p>';
            _e('If you have InnoDB tables. They will not be optimized!', 'wp-optimize');
            echo '</p>';
//            }
    ?>

    <p>
    <label>
            <input name="enable-email" id="enable-email" type="checkbox" value ="true" <?php echo get_option(OPTION_NAME_ENABLE_EMAIL, 'false') == 'true' ? 'checked="checked"':''; ?> />
            <?php
            _e('Enable email notification', 'wp-optimize');
            ?>
    </label>
    </p>
    <p>
    <label for="enable-email-address">
            <?php
            _e('Send email to', 'wp-optimize');
            ?>
        <input name="enable-email-address" id="enable-email-address" type="text" value ="<?php echo esc_attr( get_option( OPTION_NAME_ENABLE_EMAIL_ADDRESS, get_bloginfo ( 'admin_email' ) ) ); ?>" />
    </label>
    </p>	
    <p>
	<input class="button-primary" type="submit" name="wp-optimize-settings" value="<?php _e('SAVE AUTO CLEAN-UP SETTINGS', 'wp-optimize'); ?>" />
	</p>

		</div>
	</div>

</div>

<input type="hidden" name="action" value="save_redirect" />

</form>
