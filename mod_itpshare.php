<?php
/**
 * @package      ITPrism Modules
 * @subpackage   ITPShare
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl-3.0.en.html GNU/GPLv3
 */

// no direct access
defined('_JEXEC') or die;

JLoader::register('ItpShareHelper', __DIR__ . DIRECTORY_SEPARATOR . 'helper.php');

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$doc             = JFactory::getDocument();
/** $doc JDocumentHTML **/

// Loading style.css
if ($params->get('loadCss')) {
    $doc->addStyleSheet('modules/mod_itpshare/style.css');
}

// URL
$url = JUri::getInstance()->toString();

// Filter the URL
$filter = JFilterInput::getInstance();
$url    = $filter->clean($url);

$title = $doc->getTitle();

// Convert the url to short one
if ($params->get('shortener_service')) {
    $url = ItpShareHelper::getShortUrl($url, $params);
}

$title = trim($title);
require JModuleHelper::getLayoutPath('mod_itpshare', $params->get('layout', 'default'));