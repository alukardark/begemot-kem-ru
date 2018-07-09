<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $isMainPage, $curSection;
use Axi\Helpers as Axi;
?>
    <?php if (!$isMainPage): ?>
                </div> <!--end of .wraper_global_inner -->
            </div> <!--end of .wraper_global_outer -->
        </div> <!--end of .ipage -->
    <?php endif; ?>

    </div> <!--end of .site-content -->

    <div id="site-footer" class="site-footer">
        <? Axi::GF("00_footer"); ?>
    </div>

    <? Axi::GF("99_misc"); ?>

</div> <!--end of .site-wraper -->


</body>

</html>