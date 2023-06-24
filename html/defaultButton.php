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
<div id="button<?= ucwords($id) ?>" class="qlmodal <?= $class ?>">
    <button type="button" class="btn btn-primary" data-bs-toggle="<?= $toggle ?>" data-bs-target="#<?= $id ?>">
        <?= $text ?>
    </button>
</div>
