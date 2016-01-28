<?php
/**
 * AccessDenied - LGPL 3.0 licensed
 * Copyright (C) 2016  James Montalvo
 *
 * @file
 * @ingroup Extensions
 * @defgroup AccessDenied
 *
 * @license http://www.gnu.org/licenses/lgpl.html LGPL (GNU Lesser General Public License)
 * @copyright (C) 2016, James Montalvo
 * @author James Montalvo
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( "This is a MediaWiki extension, and must be run from within MediaWiki.\n" );
}

$GLOBALS['wgExtensionCredits']['other'][] = array(
	'path' => __FILE__,
	'name' => 'AccessDenied',
	'version' => '0.1',
	'author' => 'James Montalvo',
	// 'url' => 'https://www.mediawiki.org/wiki/Extension:SimpleSamlAuth',
	'license-name' => 'LGPL-3.0+',
	'descriptionmsg' => 'ext-accessdenied-desc'
);

$GLOBALS['wgHooks']['UserLoadAfterLoadFromSession'][] = function( User $user ) {

	global $wgRequest, $egAccessDeniedViewerGroup, $egAccessDeniedDenialPage, $egAccessDeniedDenialNS;

	// For a few special pages, don't do anything.
	$title = $wgRequest->getVal( 'title' );

	// if set, only members of group $egAccessDeniedViewerGroup will be allowed to view wiki
	if ( $egAccessDeniedViewerGroup ) {

		if ( ! $egAccessDeniedDenialPage ) {
			$egAccessDeniedDenialPage = "Access_Denied";
		}

		if ( ! isset( $egAccessDeniedDenialNS ) ) {
			$egAccessDeniedDenialNS = NS_PROJECT;
		}

		$accessDeniedTitle = Title::makeName( $egAccessDeniedDenialNS, $egAccessDeniedDenialPage );
		$accessDeniedTitleTalk = $accessDeniedTitle->getTalkPage();
		$userInGroup = in_array( $egAccessDeniedViewerGroup, $user->getEffectiveGroups(true) );

		// Only users in group $egAccessDeniedViewerGroup may enter the entirety of the wiki.
		// Non-members of the group are able to view the "access denied" page (and its talk page),
		// and will be redirected to "access denied" page if they attempt to view other pages.
		if ( ! $userInGroup && ! ($title == $accessDeniedTitle || $title == $accessDeniedTitleTalk ) ) {

			// redirect user to "access denied" page
			$wgRequest->setVal("title", Title::makeName( $egAccessDeniedDenialNS, $egAccessDeniedDenialPage) );
		}

	}

	return true;
};