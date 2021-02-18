<?php

use LockAuthor\LockAuthor;

/**
 * Class LockAuthorTest
 * @group Database
 */
class LockAuthorTest extends MediaWikiLangTestCase {

	/**
	 * @var LockAuthor
	 */
	private $la;

	public function setUp(): void {
		$this->setMwGlobals(
			[
				'wgLockAuthorExcludedNamespaces' => [
					NS_FILE
				],
				'wgLockAuthorActions' => [
					'edit',
					'create'
				]
			]
		);
		$this->setGroupPermissions( '*', 'edit', false );
		$this->setGroupPermissions( '*', 'createpage', false );

		$this->setGroupPermissions( 'user', 'edit', true );
		$this->setGroupPermissions( 'user', 'createpage', true );

		$this->setGroupPermissions( 'editor', 'edit', true );
		$this->setGroupPermissions( 'editor', 'createpage', true );
		$this->setGroupPermissions( 'editor', 'editall', true );
		$this->la = LockAuthor::getInstance();
	}

	public function testIsAuthor() {
		$user = $this->getTestUser()->getUser();
		$title = $this->getExistingTestPage( 'TestLockAuthorExisting1' )->getTitle();
		$result = $this->la->isAuthor( $title, $user );
		$this->assertEquals( false, $result );
		$result = $this->la->isAuthor( $title, $this->getTestSysop()->getUser() );
		$this->assertEquals( true, $result );
	}

	public function testIsAllowed() {
		$user = $this->getTestUser()->getUser();
		// Existing is not allowed for non creator
		$title = $this->getExistingTestPage( 'TestLockAuthorExisting1' )->getTitle();
		$result = $this->la->isAllowed( $title, $user, 'edit' );
		$this->assertEquals( false, $result );
		// Non-existing is allowed
		$title = $this->getNonexistingTestPage( 'TestLockAuthorNonExisting1' )->getTitle();
		$result = $this->la->isAllowed( $title, $user, 'edit' );
		$this->assertEquals( true, $result );
		// Existing is allowed for  creator
		$user = $this->getTestSysop()->getUser();
		$title = $this->getExistingTestPage( 'TestLockAuthorExisting2' )->getTitle();
		$result = $this->la->isAllowed( $title, $user, 'edit' );
		$this->assertEquals( true, $result );
		// Editor is allowed to edit all
		$user = $this->getTestUser( 'editor' )->getUser();
		$title = $this->getExistingTestPage( 'TestLockAuthorExisting2' )->getTitle();
		$result = $this->la->isAllowed( $title, $user, 'edit' );
		$this->assertEquals( true, $result );
		// Excluded namespaces
		$user = $this->getTestUser()->getUser();
		$title = $this->getExistingTestPage( Title::newFromText( 'TestFile', NS_FILE ) )->getTitle();
		$result = $this->la->isAllowed( $title, $user, 'edit' );
		$this->assertEquals( true, $result );
		// Not related actions
		$user = $this->getTestUser()->getUser();
		$title = $this->getExistingTestPage( 'TestLockAuthorExisting1' )->getTitle();
		$result = $this->la->isAllowed( $title, $user, 'read' );
		$this->assertEquals( true, $result );
	}

}
