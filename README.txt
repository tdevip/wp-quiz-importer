=== Quiz Importer Plugin ===
Contributors: tdevip
Donate link: http://www.tdevip.com/
Tags: import, quiz, questions, MSWord
Requires at least: 3.0.1
Tested up to: 4.8
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a wordpress plugin to import MSWord quiz questions into wordpress quiz providers. An MSWord template is provided to prepare quiz questions. A macro in the word file can be used to create an xml file. This file along with the plugin can be used to import quiz questions into wordpress. Currently WpProQuiz and LearnPress quiz providers are supported and  in future providers such as LearnDash, Watu, Sensei and many more will be supported. Quiz questions can contain equations in LaTex format (which can be done using MathType editor in word). It also accepts HTML tags and images can be placed in questions.

== Description ==

This is a plugin to import MSWord quiz questions into wordpress quiz providers. Using the provided MSWord template prepare quiz questions. Use the macro in the word file to create an XML file. Upload this XML file (using the plugin) and import quiz questions into wordpress quiz provder. Currently WpProQuiz and LearnPress quiz providers are supported and in future providers such as LearnDash, Watu, Sensei and many more will be supported. Various types of quiz questions are supported (see MSWord template for more details). Quiz questions can contain equations in LaTex format (which can be done using MathType editor in MSWord). They may also contain HTML tags and images. The basic features of plugin are:
	1. Quiz questions and answers can be prepared in an MSWord file
	2. Various types of questions (depending on quiz provider) can be imported.
	3. Questions can contain LaTex equations and html tags.
	4. WpProQuiz and LearnPress quiz providers are supported.

The premiun version supports:
	1. Message when correct answer is selected
	2. Message to display when incorrect answer is selected
	3. Hint to disply for each question
	4. Different marks depending on answer selected 
	5. Additional question types
	6. Additional quiz providers

NOTE: Some of the above features are dependent on quiz providers.

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `wp-quiz-importer.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

== Frequently Asked Questions ==

= How to import quiz questions? =

Upload the XML file created using MSWord template provided along with the plugin. From the uploaded file import questions.

== Screenshots ==

1. This screen shot description corresponds to screenshot-1.(png|jpg|jpeg|gif).
2. This is the second screen shot

== Changelog ==
=1.0.0=
1. New plugin created.

== Arbitrary section ==

You may provide arbitrary sections, in the same format as the ones above.  This may be of use for extremely complicated
plugins where more information needs to be conveyed that doesn't fit into the categories of "description" or
"installation."  Arbitrary sections will be shown below the built-in sections outlined above.

== A brief Markdown Example ==
