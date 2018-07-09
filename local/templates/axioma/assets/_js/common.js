var isMobile = false; //initiate as false
// device detection
if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
    || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

var SlickSlidersInit = function () {
    $('.main__sales__wraper').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.one, {infinite: true}));
    $('.main__actions__slider').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.two));
    $('.main__goodsoftheweek__slider').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.four));
    $('.main__bubbles__slider').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.five));
    $('.catalog__detail__gallery').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.oneSimple));
    $('.news__detail__gallery').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.oneSimple));
    $('.actions__detail__gallery').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.oneSimple));
    $('.sales__item__booklet').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.three));
    $('.achievements__sidebar__head').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.fiveSmall, {/*infinite: true*/}));
    $('.achievements__body__item').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.threeBig));
    $('.tenants__item__gallery').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.oneSimple));
    $('.bonuses__item__booklet').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.two));
    $('.ipage__advanced__slider').slick($.extend(true, {}, SLICK_SETTINGS.common, SLICK_SETTINGS.oneSimple, {autoplay: true}));
};

var ScrollTop = function (speed) {
    $("html, body").stop().animate({scrollTop: 0}, speed);
};

var ToTopHandler = function () {
    var speed = 600;
    var button = $('#scrolltop');

    $(window).on('scroll', function () {
        if ($(this).scrollTop() > 200) {
            button.addClass('show');
        } else {
            button.removeClass('show');
        }
    });

    button.on('click', function () {
        ScrollTop(speed);
    });

    var button320 = $('.scrolltop_320');
    button320.on('click', function () {
        ScrollTop(speed);
    });
};

var MobileNavHandler = function () {
    var button = $('.mobnav__toogle');
    var hamburger = button.find('.hamburger');
    var menu = $('.mobnav');

    if (isMobile) {
        button.addClass('ismobile');
    }

    button.on('click', function (e) {
        e.preventDefault();

        hamburger.toggleClass('is-active');
        menu.toggleClass('off');
    });

    var parentButton = $('.mobnav__link__parent');
    parentButton.on('click', function (e) {
        e.preventDefault();

        var sub = $(this).next('.mobnav__sub');
        sub.slideToggle();
        $(this).toggleClass('on');
    });
};

var CookiesWarnHandler = function () {
    var ccWarn = $('#cookieswarn');

    if (!Cookies.get('cookieswarn')) {
        ccWarn.addClass('show');

        $('.cookieswarn-ok').on('click', function (e) {
            e.preventDefault();
            Cookies.set('cookieswarn', true, {expires: 30, path: '/'});
            ccWarn.toggleClass('show');
        });
    }
};

var FancyboxInit = function () {
    $.fancybox.defaults.hash = false;
    $.fancybox.defaults.lang = 'ru';
    $.fancybox.defaults.i18n.ru = {
        CLOSE: 'Закрыть',
        NEXT: 'Следующая',
        PREV: 'Предыдущая',
        ERROR: 'Запрошенный контент не может быть загружен. <br/>Пожалуйста, попробуйте позже.',
        PLAY_START: 'Старт',
        PLAY_STOP: 'Пауза',
        FULL_SCREEN: 'Во весь экран',
        THUMBS: 'Превью',
        SHARE: 'Поделиться'
    };
    $.fancybox.defaults.buttons = [
        'slideShow',
        'fullScreen',
        'thumbs',
        //'share',
        //'download',
        //'zoom',
        'close'
    ];

    $('.fancygallery').attr('data-fancybox', 'fancygallery').fancybox();

    var fancymodalCallButton = $('.fancymodal-call');
    fancymodalCallButton.each(function (index, element) {
        var formTitle = $(element).text();

        var vacancy = $(element).parents('.vacancies__item');
        if (vacancy.length > 0) {
            formTitle += '- ' + vacancy.data('city') + ', ' + vacancy.data('shop') + ', ' + vacancy.data('vacancy');
        }

        var tenants = $(element).parents('.tenants__item');
        if (tenants.length > 0) {
            formTitle += '- ' + tenants.data('address');
        }

        var pageTitle = document.title;
        var pageURL = location.href;

        $(element).fancybox({
            keyboard: false,
            touch: false,
            btnTpl: {
                smallBtn: '<a data-fancybox-close class="fancymodal-close" title="{{CLOSE}}"><i class="ion-ios-close-empty"></i></a>'
            },
            afterLoad: function (instance, slide) {
                instance.$refs.caption.hide();

                slide.$content.find('.feedback_form__title').text(formTitle);
                slide.$content.find('input[name=FORM_TITLE]').val(formTitle);
                slide.$content.find('input[name=CURRENT_PAGE]').val(pageTitle);
                slide.$content.find('input[name=CURRENT_PAGE_URL]').val(pageURL);
            },
            afterShow: function (instance, slide) {
                $('.catalog__detail__gallery').slick('setPosition');
            }
        });
    });
};

var SmoothNavigationHandler = function () {
    var navLink = $('.navlink');

    navLink.on('click', function (event) {
        event.preventDefault();
        var destination = $(this).attr('href');

        $("html, body").stop().animate({scrollTop: $(destination).offset().top - 150}, 600);
    });
};

var ScrollSpyInit = function () {
    $('body').scrollspy({target: '.catalog__sections__navigation', offset: 300});
};

var PreloadOpacityHandler = function () {
    $('.preloadopacity').removeClass('preloadopacity-loading');
};

var BackButtonHandler = function () {
    var button = $('.back__button');
    button.on('click', function (e) {
        e.preventDefault();
        window.history.back();
    });
};

var AosAnimateInit = function () {
    AOS.init({
        offset: 0,
        duration: 300,
        // easing: 'ease-in-sine',
        // delay: 100,
        disable: 'mobile'
    });
};

var WaitMeSettings = {
    effect: 'bounce',
    text: 'Обработка',
    bg: 'rgba(255,255,255,0.7)',
    color: '#c1d835',
    maxSize: '',
    waitTime: '-1',
    textPos: 'vertical',
    fontSize: '',
    source: '',
    onClose: function () {
    }
};

var GlobalCitySelectHandler = function () {
    var select = $('.header__location__dropdown');
    var items = select.find('.dropdown__menu-item');

    items.on('click', function (e) {
        e.preventDefault();

        /* Имитация работы select`a */
        selectDropdownImmitation(this, items);

        var city = $(this).data('city');

        Cookies.set('CURRENT_CITY', city, {expires: 30, path: '/'});

        /* Для страницы "Наши универсамы" */
        $('.shops__cities__item[data-city="' + city + '"]').trigger('click');
    });
};

var selectDropdownImmitation = function (object, items) {
    var content = $(object).find('.dropdown__content').html();
    var button = $(object).parent('.dropdown__menu').prev('.dropdown__button').find('.dropdown__content');
    button.html(content);

    items.removeClass('active');
    $(object).addClass('active');
};

var TooltipInit = function () {
    var tooltip = $('.bstooltip');
    tooltip.on('click', function (e) {
        e.preventDefault();
    });
    tooltip.tooltip({
        //trigger: 'click',
        html: true,
        placement: 'auto'
    });
};

var MiniCollapseInit = function () {
    var button = $('.minicollapse');
    button.on('click', function (e) {
        e.preventDefault();

        if ($(this).hasClass('show')) {
            $(this).next('blockquote').slideUp();
            $(this).removeClass('show');
        } else {
            $(this).next('blockquote').slideDown();
            $(this).addClass('show');
        }
    });
};

var MobilePhoneClassHandler = function () {
    var mobilephone = $('.mobilephone');
    if (isMobile && mobilephone.length > 0) {
        mobilephone.each(function () {
            var text = $(this).html();
            text = text.replace(text, "<a href=\"tel:" + text + "\">" + text + "</a>");
            $(this).html(text);
        });
    }
};

var DISABLE_SCROLL = {
    keys: {37: 1, 38: 1, 39: 1, 40: 1},
    preventDefault: function (e) {
        e = e || window.event;
        if (e.preventDefault)
            e.preventDefault();
        e.returnValue = false;
    },
    preventDefaultForScrollKeys: function (e) {
        if (keys[e.keyCode]) {
            DISABLE_SCROLL.preventDefault(e);
            return false;
        }
    },
    on: function () {
        if (window.addEventListener) // older FF
            window.addEventListener('DOMMouseScroll', DISABLE_SCROLL.preventDefault, false);
        window.onwheel = DISABLE_SCROLL.preventDefault; // modern standard
        window.onmousewheel = document.onmousewheel = DISABLE_SCROLL.preventDefault; // older browsers, IE
        window.ontouchmove = DISABLE_SCROLL.preventDefault; // mobile
        document.onkeydown = DISABLE_SCROLL.preventDefaultForScrollKeys;
    },
    off: function () {
        if (window.removeEventListener)
            window.removeEventListener('DOMMouseScroll', DISABLE_SCROLL.preventDefault, false);
        window.onmousewheel = document.onmousewheel = null;
        window.onwheel = null;
        window.ontouchmove = null;
        document.onkeydown = null;
    }
};

var SLICK_SETTINGS = {
    common: {
        slidesToScroll: 1,
        autoplaySpeed: 5000,
        autoplay: false,
        infinite: false,
        fade: false,
        adaptiveHeight: false,
        prevArrow: '<a title="Назад" class="slick-prev"><i class="icon ion-ios-arrow-left"></i></a>',
        nextArrow: '<a title="Далее" class="slick-next"><i class="icon ion-ios-arrow-right"></i></a>',
        dotsClass: 'slick-dots list-unstyled'
    },
    one: {
        slidesToShow: 1,
        dots: true,
        arrows: true,
        responsive: [
            {
                breakpoint: 578,
                settings: {
                    arrows: false
                }
            }
        ]
    },
    oneSimple: {
        slidesToShow: 1,
        dots: false,
        arrows: true
    },
    two: {
        slidesToShow: 2,
        dots: false,
        arrows: true,
        responsive: [
            {
                breakpoint: 769,
                settings: {
                    arrows: false,
                    dots: true
                }
            },
            {
                breakpoint: 578,
                settings: {
                    arrows: false,
                    dots: true,
                    slidesToShow: 1
                }
            }
        ]
    },
    three: {
        slidesToShow: 3,
        dots: false,
        arrows: true,
        responsive: [
            {
                breakpoint: 993,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 2,
                    arrows: false
                }
            },
            {
                breakpoint: 577,
                settings: {
                    slidesToShow: 1,
                    arrows: false
                }
            }
        ]
    },
    threeBig: {
        slidesToShow: 3,
        dots: false,
        arrows: true,
        responsive: [
            {
                breakpoint: 1401,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 993,
                settings: {
                    slidesToShow: 1
                }
            }
        ]
    },
    four: {
        slidesToShow: 4,
        dots: false,
        arrows: true,
        responsive: [
            {
                breakpoint: 1601,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 1201,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 1,
                    arrows: false,
                    dots: true
                }
            }
        ]
    },
    five: {
        slidesToShow: 5,
        dots: false,
        arrows: true,
        responsive: [
            {
                breakpoint: 1801,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 1601,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 1201,
                settings: {
                    slidesToShow: 2
                }
            },
            {
                breakpoint: 769,
                settings: {
                    slidesToShow: 1,
                    arrows: false,
                    dots: true
                }
            }
        ]
    },
    fiveSmall: {
        slidesToShow: 5,
        dots: false,
        arrows: true,
        responsive: [
            {
                breakpoint: 1601,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 1401,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 993,
                settings: {
                    slidesToShow: 2
                }
            }
        ]
    }
};

jQuery(document).ready(function () {
    FancyboxInit();
    SlickSlidersInit();
    AosAnimateInit();
    TooltipInit();
    MiniCollapseInit();

    SmoothNavigationHandler();
    ToTopHandler();
    MobileNavHandler();
    CookiesWarnHandler();
    BackButtonHandler();
    GlobalCitySelectHandler();
});

jQuery(window).on('load', function () {
    MobilePhoneClassHandler();
    PreloadOpacityHandler();
});