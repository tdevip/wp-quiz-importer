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
						<label title='g:i a' class="quiz-import-radio">
							<input type="radio" name="wp_quiz_provider" value="quizmasternext" />
							<span><?php esc_attr_e( 'Quiz and Survey Master', 'wp-quiz-importer' ); ?></span>
						</label>
					</fieldset>
				</td>
			</tr>
		</table>
		<p class="submit">
		 	<input type="submit" class="button-primary" value="<?php _e( 'Import' , 'wp-quiz-importer'); ?>" />
		</p>
	</form>

	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="wp_quiz_note"><?php _e( 'NOTE' , 'wp-quiz-importer'); ?></label></th>
			<td>
				<span name="wp_quiz_note" class="description"><?php echo __( 'Free version of the plugin supports two types (MC and MS) of questions only. Furthermore, it assigns “Default Points” to each correct answer if quiz provider does not support default points option.' , 'wp-quiz-importer'); ?></span>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="wp_quiz_instructions"><?php _e( 'Instructions' , 'wp-quiz-importer'); ?></label></th>
			<td>
				<div name="wp_quiz_instructions" class="quiz-import-instructions">
					<ol>
 						<li>Open the MS word file and enable macros</li>
 						<li>If you want to insert a new category click <strong>Quiz Importer --&gt; New Category</strong> button on the menu. A default “[Category Name]” along with a question template will be inserted. You may change the name of the category if you wish.</li>
 						<li>If you want to insert a new question click <strong>Quiz Importer --&gt; New Question</strong> button on the menu. A new table will be inserted.</li>
 						<li>Enter data into the table in UTF-8 format (plain text) as explained in the example table above.</li>
 						<li>Please note that the text entered outside the table(s) will be ignored. Once data entry (all questions are prepared) is complete, click <strong>Quiz Importer --&gt; Export XML</strong>. This will create an XML file, which can later be imported into wordpress using WP Quiz Provider.</li>
 						<li>Each question must be entered in a separate table and few sample questions are provided below for reference.</li>
 						<li>See word template for complete instructions.</li>
					</ol>
				</div>
			</td>
		</tr>
	</table>
</div>
