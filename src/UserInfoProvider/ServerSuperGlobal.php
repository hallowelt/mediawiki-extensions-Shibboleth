<?php

namespace MediaWiki\Extension\Shibboleth\UserInfoProvider;

use MediaWiki\Extension\Shibboleth\IUserInfoProvider;

class ServerSuperGlobal implements IUserInfoProvider {

	/**
	 *
	 * @param \Config $config
	 * @return \MediaWiki\Extension\Shibboleth\IUserInfoProvider
	 */
	public function factory( $config ) {
		return new static( $config );
	}

	/**
	 * Display name from Shibboleth
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getRealname() {

		// wgShibboleth_DisplayName check in LocalSettings.php
		if (empty($GLOBALS['wgShibboleth_DisplayName'])) {
			throw new Exception(wfMessage('wg-empty-displayname')->plain());
		} else {
			$displayName = $GLOBALS['wgShibboleth_DisplayName'];
		}

		// Real name Shibboleth attribute check
		if (empty(filter_input(INPUT_SERVER, $displayName))) {
			throw new Exception(wfMessage('shib-attr-empty-realname')->plain());
		} else {
			return filter_input(INPUT_SERVER, $displayName);
		}
	}

	/**
	 * Email address from Shibboleth
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getEmail() {

		// wgShibboleth_Email check in LocalSettings.php
		if (empty($GLOBALS['wgShibboleth_Email'])) {
			throw new Exception(wfMessage('wg-empty-email')->plain());
		} else {
			$mail = $GLOBALS['wgShibboleth_Email'];
		}

		// E-mail shibboleth attribute check
		if (empty(filter_input(INPUT_SERVER, $mail))) {
			throw new Exception(wfMessage('shib-attr-empty-email')->plain());
		} else {
			return filter_input(INPUT_SERVER, $mail);
		}
	}

	/**
	 * Username from Shibboleth
	 *
	 * @return string
	 * @throws Exception
	 */
	public function getUsername() {

		// wgShibboleth_Username check in LocalSettings.php
		if (empty($GLOBALS['wgShibboleth_Username'])) {
			throw new Exception(wfMessage('wg-empty-username')->plain());
		} else {
			$user = $GLOBALS['wgShibboleth_Username'];
		}

		// Username shibboleth attribute check
		if (empty(filter_input(INPUT_SERVER, $user))) {
			throw new Exception(wfMessage('shib-attr-empty-username')->plain());
		} else {

			$username = filter_input(INPUT_SERVER, $user);

			// If $username contains '@' replace it with '(AT)'
			if (strpos($username, '@') !== false) {
				$username = str_replace('@', '(AT)', $username);
			}

			// Uppercase the first letter of $username
			return ucfirst($username);
		}
	}
}