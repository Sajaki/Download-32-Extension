<?php

/**
*
* @package phpBB Extension - Oxpus Downloads
* @copyright (c) 2002-2020 OXPUS - www.oxpus.net
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace oxpus\dlext\migrations\v700;

class release_7_0_16 extends \phpbb\db\migration\migration
{
	var $dl_ext_version = '7.0.16';

	public function effectively_installed()
	{
		return isset($this->config['dl_ext_version']) && version_compare($this->config['dl_ext_version'], $this->dl_ext_version, '>=');
	}

	static public function depends_on()
	{
		return array('\oxpus\dlext\migrations\v700\release_7_0_15');
	}

	public function update_data()
	{
		return array(
			// Set the current version
			array('config.update', array('dl_ext_version', $this->dl_ext_version)),
		);
	}
}
