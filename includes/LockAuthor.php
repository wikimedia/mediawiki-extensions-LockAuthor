<?php

namespace LockAuthor;

use Config;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\Revision\RevisionLookup;
use Title;
use User;

class LockAuthor {

	private Config $config;
	private PermissionManager $permissionManager;
	private RevisionLookup $revisionLookup;

	public function __construct(
		Config $config,
		PermissionManager $permissionManager,
		RevisionLookup $revisionLookup
	) {
		$this->config = $config;
		$this->permissionManager = $permissionManager;
		$this->revisionLookup = $revisionLookup;
	}

	/**
	 * Checks whenever the user is the original creator of the title
	 *
	 * @param Title $title
	 * @param User $user
	 *
	 * @return bool
	 */
	public function isAuthor( $title, $user ) {
		$rev = $this->revisionLookup->getFirstRevision( $title );

		if ( !$rev ) {
			// this should never happen, but if for any reason the title does not have
			// a first revision we can treat it as non-existing
			return true;
		}

		if ( $user->getName() == $rev->getUser()->getName() ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks whenever the title, action and user are subject
	 * of an intervention
	 *
	 * @param Title $title
	 * @param User $user
	 * @param string $action
	 *
	 * @return bool
	 */
	public function isAllowed( $title, $user, $action ) {
		// Skip excluded namespaces right away
		if ( in_array( $title->getNamespace(), $this->config->get( 'LockAuthorExcludedNamespaces' ) ) ) {
			return true;
		}

		// Skip not related actions
		if ( !in_array( $action, $this->config->get( 'LockAuthorActions' ) ) ) {
			return true;
		}

		// Skip if subject user already has necessary rights
		if ( $this->permissionManager->userHasRight( $user, 'editall' ) ) {
			return true;
		}

		if ( !$title->exists() ) {
			return true;
		}

		if ( $this->isAuthor( $title, $user ) ) {
			return true;
		}

		// Otherwise - block
		return false;
	}

}
