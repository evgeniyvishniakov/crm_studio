<?php

/**
 * Конфигурация названий колонок для импорта товаров
 * 
 * Этот файл содержит все возможные названия колонок на разных языках
 * для системы импорта товаров. Добавление нового языка требует только
 * добавления новых алиасов в соответствующие массивы.
 */

return [
    'name' => [
        'russian' => ['название', 'названіе', 'названиє'],
        'english' => ['name', 'title', 'product_name'],
        'ukrainian' => ['назва', 'назва товару', 'назва продукту'],
        'polish' => ['nazwa', 'nazwa produktu', 'tytuł'],
        'translit' => ['nazvanie', 'nazva', 'nazvaniye']
    ],
    
    'category' => [
        'russian' => ['категория', 'категорія', 'категорија'],
        'english' => ['category', 'product_category', 'cat'],
        'ukrainian' => ['категорія', 'категорія товару'],
        'polish' => ['kategoria', 'kategoria produktu', 'kat'],
        'translit' => ['kategoriia', 'kategoria', 'kategoriya']
    ],
    
    'brand' => [
        'russian' => ['бренд', 'брэнд', 'марка'],
        'english' => ['brand', 'manufacturer', 'make'],
        'ukrainian' => ['бренд', 'марка', 'виробник'],
        'polish' => ['marka', 'producent', 'firma'],
        'translit' => ['brend', 'marka', 'brand']
    ],
    
    'purchase_price' => [
        'russian' => ['оптовая цена', 'оптовая стоимость', 'опт', 'оптовая'],
        'english' => ['purchase_price', 'wholesale_price', 'cost_price', 'wholesale'],
        'ukrainian' => ['оптова ціна', 'оптова вартість', 'опт'],
        'polish' => ['cena hurtowa', 'cena zakupu', 'hurt'],
        'translit' => ['optovaia_cena', 'optovaya_cena', 'optova_tsina']
    ],
    
    'retail_price' => [
        'russian' => ['розничная цена', 'розничная стоимость', 'розница', 'розничная'],
        'english' => ['retail_price', 'selling_price', 'price', 'retail'],
        'ukrainian' => ['рознична ціна', 'рознична вартість', 'розница'],
        'polish' => ['cena detaliczna', 'cena sprzedaży', 'detal'],
        'translit' => ['roznicnaia_cena', 'roznichnaya_cena', 'roznichna_tsina']
    ],
    
    'photo' => [
        'russian' => ['фото', 'изображение', 'картинка', 'фотография', 'ссылка', 'url'],
        'english' => ['photo', 'image', 'picture', 'photo_url', 'image_url', 'url', 'link'],
        'ukrainian' => ['фото', 'зображення', 'картинка', 'посилання'],
        'polish' => ['zdjęcie', 'obraz', 'fotografia', 'link'],
        'translit' => ['foto', 'izobrazhenie', 'kartinka', 'ssylka']
    ]
]; 