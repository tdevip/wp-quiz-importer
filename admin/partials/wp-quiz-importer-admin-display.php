<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.tdevip.com/
 * @since      1.0.0
 *
 * @package    Wp_Quiz_Importer
 * @subpackage Wp_Quiz_Importer/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h2><?php _e( 'Import Quiz Questions:' , 'wp-quiz-importer'); ?></h2>
	<form method="post" action="" enctype="multipart/form-data">
		<?php wp_nonce_field( 'wp-quiz-importer-page_import', '_wpnonce-wp-quiz-importer-page_import' ); ?>
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="wp_quiz_file"><?php _e( 'Load File' , 'wp-quiz-importer'); ?></label></th>
				<td>
					<input type="file" name="wp_quiz_file" value="" class="quiz-import-file" /><br />
					<span class="description"><?php echo __( 'This file must have been created using MSWord template supplied.' , 'wp-quiz-importer'); ?></span>
				</td>
			</tr>
			<tr class="form-table">
				<th scope="row"><label for="wp_quiz_provider"><?php _e( 'Quiz provider' , 'wp-quiz-importer'); ?></label></th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>input type="radio"</span></legend>
						<label title='g:i a' class="quiz-import-radio">
							<input type="radio" name="wp_quiz_provider" value="wpproquiz" checked="checked" />
							<span><?php esc_attr_e( 'Wp-Pro-Quiz', 'wp-quiz-importer' ); ?></span>
						</label>
						<label title='g:i a' class="quiz-import-radio">
							<input type="radio" name="wp_quiz_provider" value="learnpress" />
							<span><?php esc_attr_e( 'LearnPress', 'wp-quiz-importer' ); ?></span>
						</label>
						<label title='g:i a' class="quiz-import-radio">
							<input type="radio" name="wp_quiz_provider" value="learndash" />
							<span><?php esc_attr_e( 'LearnDash', 'wp-quiz-importer' ); ?></span>
						</label>
					</fieldset>
				</td>
			</tr>
		</table>
		<p class="submit">
		 	<input type="submit" class="button-primary" value="<?php _e( 'Import' , 'wp-quiz-importer'); ?>" />
		</p>
	</form>
</div>
