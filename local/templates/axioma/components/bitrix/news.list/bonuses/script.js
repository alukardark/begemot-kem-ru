var BonusesAddinfoHandler = function () {
    var button = $('.bonuses__item__addtrigger');

    button.on('click', function (e) {
        e.preventDefault();

        var addinfo = $('#' + $(this).data('addinfo'));

        if (addinfo.hasClass('off')) {
            $(this).addClass('up');
            addinfo.slideDown().removeClass('off');
        } else {
            $(this).removeClass('up');
            addinfo.slideUp().addClass('off');
        }
    });
};

jQuery(document).ready(function ($) {
    BonusesAddinfoHandler();
});