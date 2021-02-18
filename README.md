LockAuthor
==========

Extension prevents users from editing pages they haven't created.
The extension uses blocking strategy, so it requires you to manage
`edit` , `create` permissions granting by yourself.

The common case to use the extension is to grant users with an 
`edit`right, so everyone will be allowed to create new pages,
but the extension will block editing of other pages created by
other users.

```php
# Prevent anonymous editing
$wgGroupPermissions['*']['edit'] = false;
$wgGroupPermissions['*']['createpage'] = false;

# Allow regular users to edit pages
$wgGroupPermissions['user']['edit'] = true;
$wgGroupPermissions['user']['createpage'] = true;

wfLoadExtension( 'LockAuthor' );
// LockAuthor will limit users edit right only to pages
// created by them

# Allow sysop to edit all pages
$wgGroupPermissions['sysop']['editall'] = true;
```

Requirements:
* MediaWiki 1.35+

Configuration:
* `$wgLockAuthorExcludedNamespaces` - array of namespaces to be 
  excluded from checks
* `$wgLockAuthorActions` - array of actions to be checked 
  ( default is `[ 'edit', 'create' ]`)

Rights:
* `editall` - grant this right to a group to allow bypassing the 
  extension restrictions
