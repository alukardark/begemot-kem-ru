var TenantsCityFilterHandler = function () {
    var filter = $('.tenants__head__filter').find('.dropdown');
    var filterItems = filter.find('.dropdown__menu-item');
    var items = $('.tenants__item');
    var sliders = $('.tenants__item__gallery');

    filterItems.on('click', function (e) {
        e.preventDefault();

        /* Имитация работы select`a */
        var content = $(this).find('.dropdown__content').html();
        var button = $(this).parent('.dropdown__menu').prev('.dropdown__button').find('.dropdown__content');
        button.html(content);

        filterItems.removeClass('active');
        $(this).addClass('active');

        /* Работа "фильтра" */
        var city = $(this).data('city');
        if (city) {
            Cookies.set('TENANT_FILTER_CITY', city, {expires: 1, path: ''});
        } else {
            Cookies.remove('TENANT_FILTER_CITY', {path: ''});
        }
        location.reload();
    });
};

jQuery(document).ready(function ($) {
    TenantsCityFilterHandler();
});