<?php

namespace LockAuthor;

use MediaWiki\MediaWikiServices;
use Title;
use User;

class LockAuthor {

	/** @var LockAuthor|null */
	private static $instance = null;

	/**
	 * @return LockAuthor
	 */
	public static function getInstance() {
		if( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
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
		$rev = MediaWikiServices::getInstance()->getRevisionLookup()->getFirstRevision( $title );

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
		$config = MediaWikiServices::getInstance()->getConfigFactory()->makeConfig('LockAuthor');

		// Skip excluded namespaces right away
		if( in_array( $title->getNamespace(), $config->get( 'LockAuthorExcludedNamespaces' ) ) ) {
			return true;
		}

		// Skip not related actions
		if( !in_array( $action, $config->get( 'LockAuthorActions' ) ) ) {
			return true;
		}

		// Skip if subject user already has necessary rights
		if( MediaWikiServices::getInstance()->getPermissionManager()->userHasRight( $user, 'editall' ) ) {
			return true;
		}

		if ( !$title->exists() ) {
			return true;
		}

		if ( LockAuthor::getInstance()->isAuthor( $title, $user ) ) {
			return true;
		}

		// Otherwise - block
		return false;
	}

}
