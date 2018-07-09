var VacanciesCityFilterHandler = function () {
    var filter = $('.vacancies__filter__city').find('.dropdown');
    var filterItems = filter.find('.dropdown__menu-item');

    filterItems.on('click', function (e) {
        e.preventDefault();

        /* Имитация работы select`a */
        selectDropdownImmitation(this, filterItems);

        /* Работа "фильтра" */
        var city = $(this).data('city');
        if (city) {
            Cookies.set('VACANCY_FILTER_CITY', city, {expires: 1, path: ''});
        } else {
            Cookies.remove('VACANCY_FILTER_CITY', {path: ''});
        }
        location.reload();
    });
};

var VacanciesFilterHandler = function () {
    var shopsFilter = $('.vacancies__filter__shop').find('.dropdown');
    var vacanciesFilter = $('.vacancies__filter__vacancy').find('.dropdown');
    var shopsFilterButton = shopsFilter.find('.dropdown__button');
    var vacanciesFilterButton = vacanciesFilter.find('.dropdown__button');
    var shopsFilterItems = shopsFilter.find('.dropdown__menu-item');
    var vacanciesFilterItems = vacanciesFilter.find('.dropdown__menu-item');
    var items = $('.vacancies__item');

    shopsFilterItems.on('click', function (e) {
        e.preventDefault();

        /* Имитация работы select`a */
        selectDropdownImmitation(this, shopsFilterItems);

        /* Работа "фильтра" */
        var shop = $(this).data('shop');
        if (shop) {
            items.removeClass('disable').slideDown(400);
            items.not('.vacancies__item[data-shop="' + shop + '"]').addClass('disable').slideUp(400);
            vacanciesFilterButton.addClass('disable').prop('title', 'Выбран магазин');
        } else {
            items.removeClass('disable').slideDown(400);
            vacanciesFilterButton.removeClass('disable').prop('title', '');
        }
        AOS.refresh();
    });

    vacanciesFilterItems.on('click', function (e) {
        e.preventDefault();

        /* Имитация работы select`a */
        selectDropdownImmitation(this, vacanciesFilterItems);

        /* Работа "фильтра" */
        var vacancy = $(this).data('vacancy');
        if (vacancy) {
            items.removeClass('disable').slideDown(400);
            items.not('.vacancies__item[data-vacancy="' + vacancy + '"]').addClass('disable').slideUp(400);
            shopsFilterButton.addClass('disable').prop('title', 'Выбрана вакансия');
        } else {
            items.removeClass('disable').slideDown(400);
            shopsFilterButton.removeClass('disable').prop('title', '');
        }
        AOS.refresh();
    });
};


jQuery(document).ready(function ($) {
    VacanciesCityFilterHandler();
    VacanciesFilterHandler();
});