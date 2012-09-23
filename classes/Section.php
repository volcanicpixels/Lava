<?php
/**
 * Pages
 *
 * @package Lava
 * @subpackage Pages
 * @author Daniel Chatfield
 *
 * @since 1.0.0
 */
class Lava_Section extends Lava_Base
{
	protected $_special_sections = array(
		'themes' => 'themes.php',
		'tools' => 'tools.php',
		'management' => 'tools.php',
		'options' => 'options-general.php',
		'plugins' => 'plugins.php',
		'users' => 'users.php',
		'dashboard' => 'index.php',
		'posts' => 'edit.php',
		'media' => 'upload.php',
		'links' => 'link-manager.php',
		'pages' => 'edit.php?post_type=page',
		'comments' => 'edit-comments.php'
	);
	public $_page_controller;
	public $_section_id;
	public $_section_title;
	public $_default_page = null;
	public $_is_special = false;

	function _construct( $page_controller, $section_id, $section_title ) {
		$this->_page_controller = $page_controller;
		$this->_section_id = $section_id;
		$this->_section_title = $section_title;

		if( array_key_exists( $section_id, $this->_special_sections ) ) {
			$this->_is_special = true;
		} else {
			$this->_add_action( 'admin_menu', '_register_section', 2 );
		}
	}

	function _get_section_slug() {
		if( $this->_is_special ) {
			return $this->_special_sections[ $this->_section_id ];
		} elseif( $this->_default_page != null ) {
			return $this->_page_controller->_get_page_( $this->_default_page )->_get_page_slug();
		} else {
			return $this->_section_id;
		}
	}

	function _get_section_slug_fragment() {
		if( $this->_is_special ) {
			return $this->_namespace( $this->_section_id );
		} else {
			return $this->_section_id;
		}
	}

	function _set_default_page( $page_id, $overwrite = true ) {
		if( $overwrite or $this->_default_page == null ) {
			$this->_default_page = $page_id;
		}
	}

	function _register_section() {
			$section_slug = $this->_get_section_slug();
			$section_title = $this->_section_title;
			$page_title = $this->_page_controller->_get_page( $this->_default_page )->_get_page_title();

			add_menu_page(
				$page_title,
				$section_title,
				'manage_options',
				$section_slug,
				array( $this, '_blank' )
			);
	}
}

?>