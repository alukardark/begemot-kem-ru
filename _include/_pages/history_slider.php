<?php
$ib = \GalleriesModel::GetIBElement('history');
?>

<div class="ipage__advanced__slider">
    <?
    foreach ($ib['PROPERTY_PHOTO_VALUE'] as $image):
        $path = \CFile::GetPath($image);
    ?>
        <a href="<?= $path; ?>" class="ipage__advanced__slider-item"
           data-fancybox="ipage-slider"
           style="background-image: url(<?= $path; ?>)"
        >
            <span class="gallery-item-overlay"></span>
        </a>
    <? endforeach; ?>
</div>
