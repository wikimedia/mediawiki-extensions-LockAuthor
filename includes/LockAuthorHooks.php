<?php

namespace LockAuthor;

use ConfigFactory;
use Exception;
use MediaWiki\Permissions\Hook\GetUserPermissionsErrorsHook;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionLookup;
use MessageSpecifier;
use Title;
use User;

/**
 * For the new page creation when viewing the page:
 *	- onTitleQuickPermissions called with 'edit', then 'create', then with 'edit'...
 * For the new page creation when starting the edit action the page:
 * 	- onUserCan called with 'edit'
 * 	- onTitleQuickPermissions called with 'create'
 * 	- onUserCan called with 'create'
 * For the new page creation when performing the edit action the page:
 * 	- onUserCan called with 'edit', then with 'create'
 *
 * For existing page editing when viewing the page:
 * 	- onTitleQuickPermissions called with 'edit' ...
 * For existing page editing when starting the edit action the page:
 * 	- onTitleQuickPermissions called with 'edit' ...
 * For existing page editing when performing the edit action the page:
 * 	-
 *
 * Class LockAuthorHooks
 * @package LockAuthor
 */
class LockAuthorHooks implements GetUserPermissionsErrorsHook {

	private LockAuthor $lockAuthor;

	public function __construct(
		ConfigFactory $configFactory,
		PermissionManager $permissionManager,
		RevisionLookup $revisionLookup
	) {
		$this->lockAuthor = new LockAuthor(
			$configFactory->makeConfig( 'LockAuthor' ),
			$permissionManager,
			$revisionLookup
		);
	}

	/**
	 * Override allowed actions based on extension config, allowing edit and create actions
	 * to be performed, called from PermissionManager::checkPermissionHooks
	 *
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @param array|string|MessageSpecifier &$result
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function onGetUserPermissionsErrors( $title, $user, $action, &$result ) {
		if ( !$this->lockAuthor->isAllowed( $title, $user, $action ) ) {
			$result = wfMessage( 'badaccess-group0' );
			return false;
		}
	}

}
