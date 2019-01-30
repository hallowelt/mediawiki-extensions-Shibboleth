<?php

use \MediaWiki\Auth\AuthManager;
use MediaWiki\Extension\Shibboleth\IUserInfoProvider;

/**
 * Description of Shibboleth auth class
 *
 * @author northway
 */
class Shibboleth extends PluggableAuth {

    /**
     * Override PluggableAuth authenticate function
     *
     * @param int|NULL $id
     * @param string $username
     * @param string $realname
     * @param string $email
     * @param string $errorMessage
     * @return boolean
     */
    public function authenticate(&$id, &$username, &$realname, &$email, &$errorMessage) {
		$userinfoprovider = $this->makeUserInforProvider();

        $id = null;
        $username = $userinfoprovider->getUsername();
        $realname = $userinfoprovider->getRealname();
        $email = $userinfoprovider->getEmail();

        if (isset($GLOBALS['wgShibboleth_GroupMap'])) {
            $this->checkGroupMap();
        }

        return true;
    }

    /**
     * Logout
     *
     * @param User $user
     * @return boolean
     */
    public function deauthenticate(User &$user) {

        session_destroy();

        header('Location: ' . $this->getLogoutURL());

        return true;
    }

    public function saveExtraAttributes($id) {

    }

    /**
     * Handle user privilages if it has one
     *
     * @param User $user
     */
    public static function populateGroups(User $user) {

        $authManager = AuthManager::singleton();
        $groups = $authManager->getAuthenticationSessionData('shib_attr');

        if (!empty($groups)) {
            $groups_array = explode(";", $groups);

            // Check 'sysop' in LocalSettings.php
            $sysop = $GLOBALS['wgShibboleth_GroupMap']['sysop'];

            if (in_array($sysop, $groups_array)) {
                $user->addGroup('sysop');
            } else {
                $user->removeGroup('sysop');
            }

            // Check 'bureaucrat' in LocalSettings.php
            $bureaucrat = $GLOBALS['wgShibboleth_GroupMap']['bureaucrat'];

            if (in_array($bureaucrat, $groups_array)) {
                $user->addGroup('bureaucrat');
            } else {
                $user->removeGroup('bureaucrat');
            }
        }
    }

    private function checkGroupMap() {

        $attr_name = $GLOBALS['wgShibboleth_GroupMap']['attr_name'];

        if (empty($attr_name)) {
            throw new Exception(wfMessage('wg-empty-groupmap-attr')->plain());
        }

        $groups = filter_input(INPUT_SERVER, $attr_name);

        if (empty($groups)) {
            throw new Exception(wfMessage('shib-attr-empty-groupmap-attr')->plain());
        }

        $authManager = AuthManager::singleton();
        $authManager->setAuthenticationSessionData('shib_attr', $groups);
    }

    private function getLogoutURL() {

        $base_url = $GLOBALS['wgShibboleth_Logout_Base_Url'];

        if (empty($base_url)) {
            throw new Exception(wfMessage('shib-attr-empty-logout-base-url')->plain());
        }

        $target_url = $GLOBALS['wgShibboleth_Logout_Target_Url'];

        if (empty($target_url)) {
            throw new Exception(wfMessage('shib-attr-empty-logout-target-url')->plain());
        }

        $logout_url = $base_url . '/Shibboleth.sso/Logout?return=' . $target_url;

        return $logout_url;
    }

	/**
	 * @return IUserInfoProvider
	 */
	private function makeUserInforProvider() {
		$config = new GlobalVarConfig( 'Shibboleth_' );
		$legacyConfig = new GlobalVarConfig( 'Shib' );

		$multiConfig = new MultiConfig( [
			$config,
			$legacyConfig
		] );

		$factoryCallback = $config->get( 'UserProviderFactoryCallback' );
		if( !is_callable( $factoryCallback ) ) {
			throw new MWException( "Factory for UserInfoProvider not callable!" );
		}

		$userInfoProvider = call_user_func_array( $factoryCallback, [ $multiConfig ] );
		if( $userInfoProvider instanceof IUserInfoProvider ) {
			throw new MWException( "Factory for UserInfoProvider did not return a valid"
					. " IUserInfoProvider object!" );
		}

		return $userInfoProvider;
	}

}
