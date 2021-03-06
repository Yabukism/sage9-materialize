<?php

namespace OTGS\Toolset\Types\Wordpress\Option\Associations;

use OTGS\Toolset\Types\Wordpress\Option\AOption;

/**
 * Class ImportAvailable
 * @package OTGS\Toolset\Types\Wordpress\Option\Associations
 *
 * @since 3.0
 */
class ImportAvailable extends AOption {

	/**
	 * @return string
	 */
	public function getKey() {
		return '_toolset_associations_import_is_available';
	}

	/**
	 * Only true|false
	 *
	 * @param bool $value
	 * @param bool $autoload
	 */
	public function updateOption( $value = true, $autoload = true ) {
		update_option( $this->getKey(), (bool) $value, $autoload );
	}
}
