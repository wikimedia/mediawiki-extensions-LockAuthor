<?php

namespace LockAuthor;

use Exception;
use MediaWiki\MediaWikiServices;
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
class LockAuthorHooks {

	/**
	 * Override allowed actions based on extension config, allowing edit and create actions
	 * to be performed, called from PermissionManager::checkPermissionHooks
	 *
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 * @param bool &$result
	 *
	 * @return bool
	 * @throws Exception
	 */
	public static function onUserCan( $title, $user, $action, &$result ) {
		if ( !LockAuthor::getInstance()->isAllowed( $title, $user, $action ) ) {
			$result = false;
			return false;
		}
	}

}
