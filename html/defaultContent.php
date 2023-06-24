<?php
/**
 * @package		plg_content_qlmodal
 * @copyright	Copyright (C) 2023 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

//no direct access
defined('_JEXEC') or die ('Restricted Access');
/** @var array $data */
/** @var string $id */
/** @var string $layout */
/** @var string $text */
/** @var string $title */
/** @var string $type */
/** @var string $content */
/** @var string $class */
/** @var string $toggle */
/** @var array $attributes */
?>
<div class="modal fade" id="<?= $id ?>" class="qlmodal <?= $class ?>" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="<?= $id ?>" aria-hidden="true">
    <div class="<?= $toggle ?>-dialog">
        <div class="<?= $toggle ?>-content">
            <div class="<?= $toggle ?>-header">
                <?php if('modal' === $toggle):?>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <?php endif;?>
                <?php if(!empty($title)):?>
                    <h4 class="<?= $class ?>-title"><?= $title ?></h4>
                <?php endif;?>
            </div>

            <div class="<?= $toggle ?>-body">
                <?= $content ?>
            </div>
            <?php if('modal' === $toggle):?>
                <div class="<?= $toggle ?>-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= JText::_('PLG_CONTENT_QLMODAL_CLOSE') ?></button>
                </div>
            <?php endif;?>
        </div>
    </div>
    <?php if('collapse' === $toggle):?>
        <div id="buttonClose<?= $id ?>" class="qlmodal">
            <button class="btn btn-primary" type="button" data-toggle="<?= $toggle ?>" data-target="#<?= $id ?>" <?php echo $attributes[$data['id']]['aria'];?>>
                <?= JText::_('PLG_CONTENT_QLMODAL_CLOSE') ?>
            </button>
        </div>
    <?php endif;?>
</div>
