<?php
/**
 * @package      ITPrism Modules
 * @subpackage   ITPSocialButtons
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor.iliev@itprism.co.uk>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * ITPSocialButtons is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( "_JEXEC" ) or die( "Restricted access" );

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$doc = JFactory::getDocument();

$style = JURI::base() . "modules/mod_itpshare/style.css";
$doc->addStyleSheet($style);

$url    = JURI::getInstance();
$url    = $url->toString();
$title  = $doc->getTitle();

$title  = htmlentities($title, ENT_QUOTES, "UTF-8");

require(JModuleHelper::getLayoutPath('mod_itpshare'));