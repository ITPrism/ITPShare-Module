<?php
/**
 * @package      ITPrism Modules
 * @subpackage   ITPShare
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * ITPShare is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$moduleClassSfx = htmlspecialchars($params->get('moduleclass_sfx'));

// Loading style.css
if($params->get("loadCss")) {
    $doc = JFactory::getDocument();
    $doc->addStyleSheet("modules/mod_itpshare/style.css");
}

// URL
$url    = JURI::getInstance();
$url    = $url->toString();
$title  = $doc->getTitle();

// Title
$title  = htmlentities($title, ENT_QUOTES, "UTF-8");
require JModuleHelper::getLayoutPath('mod_itpshare', $params->get('layout', 'default'));