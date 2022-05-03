<?php
echo $this->AssetCompress->css('selectize-plugins.css', array(
			'raw' => (bool)Configure::read('debug')
		));
echo $this->AssetCompress->script('selectize-plugin.js', array('raw' => (bool)Configure::read('debug')));