<?php

namespace MediaWiki\Extension\Shibboleth\UserInfoProvider;

class GetAllHeaders extends SimpleAttributesHashMap {

	/**
	 *
	 * @param \Config $config
	 * @return \MediaWiki\Extension\Shibboleth\IUserInfoProvider
	 */
	public function factory( $config ) {
		$headers = [];
		if( PHP_SAPI !== 'cli' ) {
			$headers = getallheaders();
		}

		return new static( $headers, $config );
	}

}
