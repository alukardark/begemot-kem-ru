var MAP = {
    mapStyles: [],
    markersArray: [],
    mapContainer: {},
    mapObjects: {},
    currentCity: '',
    currentDistrict: false,
    defaultCenter: {lat: 55.34432630867194, lng: 86.06208801269531},
    icons: {},
    infoWindow: {},
    shopsMap: {}
};

MAP.mapStyles = [
    {
        "featureType": "administrative",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#7f2200"
            },
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "color": "#87ae79"
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#495421"
            }
        ]
    },
    {
        "featureType": "administrative",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "visibility": "on"
            },
            {
                "weight": "1.09"
            }
        ]
    },
    {
        "featureType": "administrative.neighborhood",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "administrative.neighborhood",
        "elementType": "labels",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#bdd156"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "color": "#769E72"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#7B8758"
            }
        ]
    },
    {
        "featureType": "poi",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "color": "#EBF4A4"
            }
        ]
    },
    {
        "featureType": "poi.park",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "simplified"
            },
            {
                "color": "#8dab68"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "geometry.fill",
        "stylers": [
            {
                "visibility": "simplified"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels.text.fill",
        "stylers": [
            {
                "color": "#5B5B3F"
            },
            {
                "weight": "0.01"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "color": "#565656"
            },
            {
                "weight": "0.01"
            },
            {
                "gamma": "2.31"
            }
        ]
    },
    {
        "featureType": "road",
        "elementType": "labels.icon",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#EBF4A4"
            }
        ]
    },
    {
        "featureType": "road.arterial",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "road.local",
        "elementType": "geometry",
        "stylers": [
            {
                "color": "#ffffff"
            },
            {
                "visibility": "on"
            }
        ]
    },
    {
        "featureType": "transit",
        "elementType": "all",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "geometry",
        "stylers": [
            {
                "visibility": "on"
            },
            {
                "color": "#4cb0cb"
            }
        ]
    }
];

var ShopsMapInit = function () {
    MAP.mapContainer = $('#shops_map');
    MAP.mapObjects = $('.shops__control__city__item__control');

    MAP.currentCity = MAP.mapContainer.data('current-city');

    MAP.icons = {
        default: {
            name: 'default',
            url: '/upload/_base/marker-default.png',
            anchor: new google.maps.Point(8, 22)
        },
        active: {
            name: 'active',
            url: '/upload/_base/marker-active.png',
            anchor: new google.maps.Point(14, 38)
        }
    };
    MAP.infoWindow = new google.maps.InfoWindow();

    MAP.shopsMap = new google.maps.Map(MAP.mapContainer[0], {
        center: MAP.defaultCenter,
        clickableIcons: false,
        disableDefaultUI: false,
        disableDoubleClickZoom: false,
        draggable: true,
        scrollwheel: false,
        styles: MAP.mapStyles,
        zoom: 12,
        minZoom: 10,
        maxZoom: 16,
        mapTypeControl: false,
        streetViewControl: false
    });

    var markersBounds = new google.maps.LatLngBounds();
    if (MAP.mapObjects) {
        MAP.mapObjects.each(function (index, object) {
            var markerPosition = new google.maps.LatLng($(object).data('lat'), $(object).data('lng'));
            markersBounds.extend(markerPosition);

            var marker = new google.maps.Marker({
                map: MAP.shopsMap,
                position: markerPosition,
                icon: MAP.icons.default,
                city: $(object).data('city'),
                shop: $(object).data('target'),
                name: $(object).data('name'),
                district: $(object).data('district'),
                address: $(object).data('address')
            });

            MAP.markersArray.push(marker);

            marker.addListener('click', function () {
                if (marker.getIcon().name === 'default') {
                    /* Set icon */
                    DropIconsState();
                    marker.setIcon(MAP.icons.active);

                    /* Show shop card */
                    $(marker.shop).collapse('toggle');
                }
            });
            marker.addListener('mouseover', function () {
                if (marker.getIcon().name === 'default') {
                    var content = '<span class="shops__map__infowindow"><b>' + marker.name + '</b><br>' + marker.address + '</span>';
                    MAP.infoWindow.setContent(content);
                    MAP.infoWindow.open(MAP.shopsMap, marker);
                }
            });
            marker.addListener('mouseout', function () {
                if (marker.getIcon().name === 'default') {
                    MAP.infoWindow.close();
                }
            });
        });

        MAP.shopsMap.addListener('click', function () {
            MAP.infoWindow.close();
        });

        // remove close button from infowindow
        google.maps.event.addListener(MAP.infoWindow, 'domready', function () {
            $('.shops__map__infowindow').parent().parent().parent().next('div').css({'display': 'none'});
        });

        SetActiveMarkers();
    }
};

var SetActiveMarkers = function () {
    var markersBounds = new google.maps.LatLngBounds();
    for (var i = 0; i < MAP.markersArray.length; i++) {
        var marker = MAP.markersArray[i];
        if ((marker.city === MAP.currentCity && MAP.currentDistrict === false) ||
            (marker.city === MAP.currentCity && marker.district === MAP.currentDistrict)) {
            marker.setVisible(true);
            markersBounds.extend(marker.position);
        } else {
            marker.setVisible(false);
        }
    }
    MAP.shopsMap.setCenter(markersBounds.getCenter(), MAP.shopsMap.fitBounds(markersBounds));

    $(window).off();
    $(window).on('resize orientationchange', function () {
        MAP.shopsMap.setCenter(markersBounds.getCenter());
    });
};

var SetActiveItems = function (city, district) {
    var activeCity = $('.shops__control__city[data-city="' + city + '"]');
    var items = activeCity.find('.shops__control__city__item');

    items.removeClass('hidden');
    if (district !== false) {
        items.each(function (index, object) {
            if ($(object).data('district') !== district) {
                $(object).addClass('hidden');
            }
        });
    }
};

var SetCurrentCity = function (city) {
    MAP.currentCity = city;
};

var SetCurrentDistrict = function (district) {
    MAP.currentDistrict = district;
};

var SetCityDistrict = function (district) {
    $('.shops__cities__item.selected').data('district', district);
};

var DropIconsState = function () {
    for (var i = 0; i < MAP.markersArray.length; i++) {
        var marker = MAP.markersArray[i];
        marker.setIcon(MAP.icons.default);
    }
};

var CitySelectHandler = function () {
    var button = $('.shops__cities__item');
    var cityBlocks = '.shops__control__city';

    button.on('click', function (e) {
        e.preventDefault();

        button.removeClass('selected');
        $(cityBlocks).removeClass('show');

        $(this).addClass('selected');

        var city = $(this).data('city');
        $(cityBlocks + '[data-city="' + city + '"]').addClass('show');

        var district = $(this).data('district') ? $(this).data('district') : false;

        SetCurrentCity(city);
        SetCurrentDistrict(district);
        //ResetDistrictDropdowns();
        SetActiveMarkers();
        SetActiveItems(city, district);

        OneShopCheck();
    });
};

var DistrictSelectHandler = function () {
    var districts = $('.shops__control__city__districts');
    var items = districts.find('.dropdown__menu-item');

    items.on('click', function (e) {
        e.preventDefault();

        /* Имитация работы select`a */
        selectDropdownImmitation(this, items);

        var city = MAP.currentCity;
        var district = $(this).data('district') ? $(this).data('district') : false;

        SetCityDistrict(district);
        SetCurrentDistrict(district);
        SetActiveMarkers();
        SetActiveItems(city, district);
    });
};

var ResetDistrictDropdowns = function () {
    var buttons = $('.dropdown__button');
    $('.dropdown__menu-item').removeClass('active');
    buttons.each(function (index, object) {
        var target = $(object).next('.dropdown__menu').first('.dropdown__menu-item');
        target.addClass('active');

        var content = target.find('.dropdown__content').html();
        $(object).find('.dropdown__content').html(content);
    });
};

var ShopSelectHandler = function () {
    var collapsible = $('.shops__control__city__item__content');
    collapsible.on('shown.bs.collapse', function () {
        /* activate current shop marker on map */
        $(this).parent().addClass('active');
        var shop = '#' + $(this).attr('id');
        for (var i = 0; i < MAP.markersArray.length; i++) {
            var marker = MAP.markersArray[i];
            if (marker.shop === shop) {
                DropIconsState();
                marker.setIcon(MAP.icons.active);

                var content = '<span class="shops__map__infowindow"><b>' + marker.name + '</b><br>' + marker.address + '</span>';
                MAP.infoWindow.setContent(content);
                MAP.infoWindow.open(MAP.shopsMap, marker);
            }
        }

        /* fix slick slider */
        $(this).find('.shops__control__city__item__gallery').slick('setPosition');

        /* Scroll to shop card */
        var position = $(this).parents('.shops__control__city__item').position().top;
        var scrollContainer = $(this).parents('.mCustomScrollbar');
        scrollContainer.mCustomScrollbar("scrollTo", position, {scrollInertia: 600});
    });
    collapsible.on('hide.bs.collapse', function () {
        collapsible.parent().removeClass('active');
        DropIconsState();
        MAP.infoWindow.close();
    });
};

var CustomScrollInit = function () {
    var realHeight = $('.shops__map').height().toFixed();
    var area = $('.shops__control__city__wraper');
    area.each(function (index, object) {
        var maxHeight = realHeight;
        if ($(object).prev('.shops__control__city__districts__placeholder').length > 0) {
            maxHeight -= 100;
        }
        $(object).css({'max-height': maxHeight + 'px', 'height': maxHeight + 'px'})
            .mCustomScrollbar({
                theme: 'minimal-dark',
                scrollInertia: 0,
                autoDraggerLength: true,
                scrollButtons: {enable: false},
                mouseWheel: {scrollAmount: 50}
            });
    });

    if (!isMobile) {
        $('.shops__control').hover(
            function () {
                DISABLE_SCROLL.on();
            },
            function () {
                DISABLE_SCROLL.off();
            }
        );
    }
};

var GalleryInit = function (gallery) {
    gallery.slick({
        slidesToScroll: 1,
        autoplaySpeed: 5000,
        autoplay: false,
        infinite: false,
        fade: false,
        adaptiveHeight: false,
        prevArrow: '<a title="Назад" class="slick-prev"><i class="icon ion-ios-arrow-left"></i></a>',
        nextArrow: '<a title="Далее" class="slick-next"><i class="icon ion-ios-arrow-right"></i></a>',
        dotsClass: 'slick-dots list-unstyled',
        slidesToShow: 1,
        dots: false,
        arrows: true
    });
};

var OneShopCheck = function () {
    var accordeon = $('.shops__control__city.show');
    var shops = accordeon.find('.shops__control__city__item');

    if (shops.length === 1) {
        setTimeout(function () {
            shops.find('.shops__control__city__item__content').collapse('show');
        }, 500);
    }
};


jQuery(document).ready(function () {
    CustomScrollInit();
    GalleryInit($('.shops__control__city__item__gallery'));

    if ($("div").is("#shops_map")) {
        ShopsMapInit();
        AOS.refresh();
    }

    CitySelectHandler();
    DistrictSelectHandler();
    ShopSelectHandler();

    OneShopCheck();
});
