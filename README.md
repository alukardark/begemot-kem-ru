1C-Bitrix CMS
======
[Сеть Универсамов Бегемот.](http://www.begemot-kem.ru)
-----

>Используется шаблонизатор `Blade`  
[Документация на русском](https://laravel.ru/docs/v5/blade)  
[Особенности работы в 1С-Битрикс](https://github.com/arrilot/bitrix-blade)  
Если нет желания пользоваться Blade работаем в стандартных `template.php`

>Используется пакет `Bitrix-models`  
[https://github.com/arrilot/bitrix-models](https://github.com/arrilot/bitrix-models)

После клонирования репозитория необходимо установить зависимости  
В корне `$ yarn install`  
И в папке `"/local/composer"` `$ composer install`  

`config.rb` написан для работы с PHPStorm.  
Для компиляции SASS из командной строки необходимо из путей 
`images_dir`, `javascripts_dir` и `fonts_dir` убрать `"/local/templates/axioma"`

REST API
----
реализовано с помощью модуля  
[https://github.com/ArtamonovDenis/artamonov.api](https://github.com/ArtamonovDenis/artamonov.api)  
Но брать оригинальный модуль от автора нельзя потому, что:  
— Модуль отвязан от необходимости создания контроллеров непосредственно в папке модуля.  
Теперь достаточно просто зарегистрировать класс в произвольном месте, 
но обязательно в неймспейсе `Artamonov\Api\Controllers` и до инициализации самого API.  
— Модуль адаптирован для работы с языками со строгой типизацией на стороне клиента -  
объект ответа теперь одинаковый и в случае корректного ответа, и в случае ошибки.  


Методы REST API
----
Весь API публичный, доступен по `/api/#class#/#method#`  
Классы и их методы:  
`infoscreen`  
(`getlist`) - вернет данные для инфостраниц  

`bonus`  
(`getlist`) - вернет данные бонусных товаров  

`sale`  
(`getlist`) - вернет данные акционных товаров  

`shops`  
(`getlist`) - вернет данные о магазинах  
(`getcities`) - вернет список городов в которых есть магазины  

`star`  
(`send`) - принимает поля формы в виде GET запроса 'name', 'phone', 'star', 'comment'  
Первые три обязательны.  
Если обязательный(-ые) параметр отсутствует или пустой метод вернет массив алиасов этих полей.

Another Bitrix Features
----
[https://marketplace.1c-bitrix.ru/solutions/ws.projectsettings/](https://marketplace.1c-bitrix.ru/solutions/ws.projectsettings/)  
[https://github.com/arrilot/bitrix-blade](https://github.com/arrilot/bitrix-blade)  
[https://github.com/arrilot/bitrix-hermitage](https://github.com/arrilot/bitrix-hermitage)  
[https://github.com/arrilot/bitrix-models](https://github.com/arrilot/bitrix-models)  
[https://github.com/arrilot/bitrix-cacher](https://github.com/arrilot/bitrix-cacher)



