<div class="vacancies__sidebar__filter__item vacancies__filter__city">
    <div class="dropdown">
        <div class="dropdown__button"
             data-toggle="dropdown"
             role="button" aria-expanded="false"
        >
            <div class="dropdown__content">
                <span class="dropdown__content-text">{{ $arResult['CURRENT_CITY'] }}</span>
            </div>
            <div class="dropdown__button-arrow"><i class="ion-ios-arrow-down"></i></div>
        </div>
        <div class="dropdown-menu dropdown__menu">
            @foreach($arResult['CITIES'] as $city)
                @php
                    $active = $arResult['CURRENT_CITY'] == $city ? 'active' : '';
                @endphp
                <div class="dropdown__menu-item {{ $active }}"
                     data-city="{{ $city != 'Все города' ? $city : '' }}"
                >
                    <div class="dropdown__content">
                        <span class="dropdown__content-text">{{ $city }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="vacancies__sidebar__filter__item vacancies__filter__shop">
    <div class="dropdown">
        <div class="dropdown__button"
             data-toggle="dropdown"
             role="button" aria-expanded="false"
        >
            <div class="dropdown__content">
                <span class="dropdown__content-text">Все магазины</span>
            </div>
            <div class="dropdown__button-arrow"><i class="ion-ios-arrow-down"></i></div>
        </div>
        <div class="dropdown-menu dropdown__menu">
            @foreach($arResult['SHOPS_FILTERED'] as $shop)
                <div class="dropdown__menu-item {{ $shop['FILTER_STRING'] == 'Все магазины' ? 'active' : '' }}"
                     data-shop="{{ $shop != 'Все магазины' ? $shop['PROPERTY_ADDRESS_VALUE'] : '' }}"
                >
                    <div class="dropdown__content">
                        <span class="dropdown__content-text">{{ $shop['FILTER_STRING'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="vacancies__sidebar__filter__item vacancies__filter__vacancy">
    <div class="dropdown">
        <div class="dropdown__button"
             data-toggle="dropdown"
             role="button" aria-expanded="false"
        >
            <div class="dropdown__content">
                <span class="dropdown__content-text">Все вакансии</span>
            </div>
            <div class="dropdown__button-arrow"><i class="ion-ios-arrow-down"></i></div>
        </div>
        <div class="dropdown-menu dropdown__menu">
            @foreach($arResult['VACANCIES'] as $vacancy)
                <div class="dropdown__menu-item {{ $vacancy == 'Все вакансии' ? 'active' : '' }}"
                     data-vacancy="{{ $vacancy != 'Все вакансии' ? $vacancy : '' }}"
                >
                    <div class="dropdown__content">
                        <span class="dropdown__content-text">{{ $vacancy }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>