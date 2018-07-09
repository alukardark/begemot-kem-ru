<div class="footer__socnets">
    <?php foreach (SOCNETS_LIST as $socnet): ?>
        <a href="<?= $socnet['PROPERTY_LINK_VALUE'] ?>" title="<?= $socnet['NAME'] ?>"
           class="footer__socnets__item" rel="nofollow" target="_blank">
            <i class="fa fa-<?= $socnet['PROPERTY_CLASS_VALUE'] ?>"></i>
        </a>
    <?php endforeach ?>
</div>

<div class="footer__copyright">
    <?= date('Y'); ?> ООО «Система универсамов «Бегемот»
</div>