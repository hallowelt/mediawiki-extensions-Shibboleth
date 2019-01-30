<?php

namespace MediaWiki\Extension\Shibboleth;

interface IUserInfoProvider {
	/**
	 * @return string
	 */
	public function getUsername();

	/**
	 * @return string
	 */
	public function getRealname();

	/**
	 * @return string
	 */
	public function getEmail();
}