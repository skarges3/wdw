<?php
/**
 * Created by IntelliJ IDEA.
 * User: joe
 * Date: 9/8/13
 * Time: 5:44 PM
 * To change this template use File | Settings | File Templates.
 */
$banner = new CustomPostType('banner', array('name' => 'Banners', 'singular_label' => 'Banner'));
$banner->addThumbnailSupport();
$banner->addSection('default', 'Cool Fields', 'side', 'normal');
$banner->addField('style', 'checkboxes',null,'Style', array('options'=>array(
    array('value'=>'light', 'label'=>'Theme Light'),
    array('value'=>'dark', 'label'=>'Theme Dark')
)));
$banner->addField('link', 'link', null, 'Link');
$banner->addField('video', 'video', null, 'Video');