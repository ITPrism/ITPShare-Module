<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="itp-share-mod<?php echo $params->get('moduleclass_sfx');?>">
    <?php
    echo ItpShareHelper::getFacebookLike($params, $url, $title);
    echo ItpShareHelper::getTwitter($params, $url, $title);
    echo ItpShareHelper::getDigg($params, $url, $title);
    echo ItpShareHelper::getStumbpleUpon($params, $url, $title);
    echo ItpShareHelper::getBuzz($params, $url, $title);
    echo ItpShareHelper::getLinkedIn($params, $url, $title);
    echo ItpShareHelper::getGooglePlusOne($params, $url, $title);
    echo ItpShareHelper::getReTweetMeMe($params, $url, $title);
    echo ItpShareHelper::getYahooBuzz($params, $url, $title);
    echo ItpShareHelper::getFacebookShareMe($params, $url, $title);
    echo ItpShareHelper::getExtraButtons($params, $url, $title);
    ?>
</div>
<div style="clear:both;"></div>