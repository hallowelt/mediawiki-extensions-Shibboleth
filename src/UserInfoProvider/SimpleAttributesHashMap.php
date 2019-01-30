<?php

namespace MediaWiki\Extension\Shibboleth\UserInfoProvider;

use MediaWiki\Extension\Shibboleth\IUserInfoProvider;

class SimpleAttributesHashMap implements IUserInfoProvider {

	/**
	 *
	 * @var array
	 */
	private $map = [];

	/**
	 *
	 * @var \Config
	 */
	private $config = null;

	/**
	 *
	 * @param array $map
	 * @param \Config $config
	 */
	public function __construct( $map, $config ) {
		$this->map = $map;
		$this->config = $config;
	}

	public function getEmail() {

	}

	public function getRealname() {

	}

	public function getUsername() {

	}

}