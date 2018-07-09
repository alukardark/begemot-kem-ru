var YearsHandler = function () {
    var slider = $('.achievements__sidebar__head');
    var sliderItem = slider.find('a.navlink');

    var years = $('.achievements__body__item');
    var currentYear = years.first().attr('id');

    var stickyClass = 'sticked';

    sliderItem.on('click', function () {
       var index = $(this).parents('.slick-slide').data('slick-index');
       slider.slick('slickGoTo', index);
    });

    var basePosition = slider.offset().top - 10;
    $(window).on('scroll load resize orientationchange', function () {
        var windowScrollPosition = $(window).scrollTop();
        var currentPosition = basePosition - $(window).scrollTop();

        if (currentPosition < 0 && !slider.hasClass(stickyClass)) {
            slider.addClass(stickyClass);
            // slider.slick('setOption', {centerMode: true}, true);
            slider.slick('setPosition');
        }
        if (currentPosition >= 0 && slider.hasClass(stickyClass)) {
            slider.removeClass(stickyClass);
            // slider.slick('setOption', {centerMode: false}, true);
            slider.slick('setPosition');
        }

        years.each(function (index, object) {
            var yearPosition = $(object).offset().top - windowScrollPosition - 200;
            if (yearPosition > -50 && yearPosition < 50) {
                currentYear = $(object).attr('id');
            }
        });

        var currentYearLink = $('#a-' + currentYear);
        if (!currentYearLink.hasClass('current')) {
            sliderItem.removeClass('current');
            currentYearLink.addClass('current');

            var currentYearIndex = currentYearLink.parents('.slick-slide').data('slick-index');
            slider.slick('slickGoTo', currentYearIndex);
        }
    });
};

jQuery(document).ready(function() {
    YearsHandler();
});