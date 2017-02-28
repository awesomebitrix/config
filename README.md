# Плагин Конфигуратор (kitrix/config)

Плагин "Конфигуратор" позволяет централизовано хранить все настройки сайта. Все kitrix плагины могут легко использовать открытое API для регистрации своих настроек, групп, страниц и собственных виджетов.

## Установка

```bash
$ composer require kitrix/config
```

## Быстрый старт

### Регистрация поля
_листинг файла acme/test\_plugin/src/TestPlugin.php:_
```php
use Kitrix\Config\ConfRegistry;
use Kitrix\Config\Fields\Textarea;
use Kitrix\Plugins\Plugin;

final class TestPlugin extends Plugin
{
    public function run()
    {
        // создаем новое поле типа Textarea
        // плагины могут создавать свои типы полей
        // Первый параметр тип поля (Textarea, Checkbox, Input, etc..)
        // Второй параметр код по которому можно будет получить значение
        $field = ConfRegistry::makeField(Textarea::class, 'foo', "Опишите kitrix");

        // создаем группу от имени нашего плагина
        $group = ConfRegistry::makeGroup("Пример группы", TestPlugin::class);

        // добавляем в группу созданное поле
        $group->addField($field);

        // регистрируем новую группу
        ConfRegistry::registerGroup($group);
    }
}
```

Теперь переходим в админку и если все сделано правильно, вы увидите свою страницу (group) настроек и новое поле (field).

![Пример поля](http://storage6.static.itmages.com/i/17/0228/h_1488310696_3774602_096da3b071.png)

### Получить значение через API
```php
// получим значения из поля 'foo' созданного в плагине TestPlugin
$formValue = ConfRegistry::getValue(TestPlugin::class, 'foo');
// $formValue = Kitrix made with <3
```

### Установить значение через API
```php
// установим значение в "Пример"
ConfRegistry::setValue(TestPlugin::class, 'foo', "Пример");
```

## Доп. параметры поля (field)

При создании нового поля, вы получаете объект типа "Admin\Field"
До того как поле будет добавлено в группу, вы сможете его расширить и косюмизировать:

_листинг файла acme/test\_plugin/src/TestPlugin.php:_
```php
$field = ConfRegistry::makeField(Textarea::class, 'foo', "Опишите kitrix")
    
    // установим значение "по умолчанию"
    ->setDefaultValue("Это значение будет установлено только один раз")
    
    // добавим заголовок
    ->setTitle("Название для поля")
    
    // поле будет выключено
    ->setDisabled(true)
    
    // + справка по использованию
    ->setHelpText("Тут можно разместить <b>справочную</b> информацию");
```

![Пример 2](http://storage6.static.itmages.com/i/17/0228/h_1488311431_3902765_141d507537.png)

для поля типа "**Select**" вы должны задать массив с опциями (**ключ => значение**):

_листинг файла acme/test\_plugin/src/TestPlugin.php:_
```php
$mySelect = ConfRegistry::makeField(Select::class, 'choose', "Любимый цвет")
    ->setOptions([
        "red" => "Красный",
        "green" => "Зеленый",
        "blue" => "Синий"
    ]);
```


## Типы полей
По умолчанию доступны следующие типы полей:
* Fields/Textarea
* Fields/Input
* Fields/Select
* Fields/Checkbox

### Создать свой тип поля

Перейдите в папку src вашего плагина, создайте там в любом месте файл
**MyField.php** (вместо MyField укажите тип вашего поля)

Kitrix не навязывает вам структуру плагина, однако требует чтобы ваши
namespace'ы в точности совпадали с локальным расположением файла.

#### Для примера мы создадим ColorPicker:

_листинг файла acme/test\_plugin/src/Fields/ColorPicker.php:_
```php
namespace Acme\TestPlugin\Fields;
use Kitrix\Config\Admin\FieldType;

class ColorPicker extends FieldType
{

}
```

Если вы создадите новое поле на основе вашего типа ColorPicker, то по умолчанию она будет работать как обычный text input. Но это уже что-то.

_листинг файла acme/test\_plugin/src/TestPlugin.php:_
```php
$colorPicker = ConfRegistry::makeField(ColorPicker::class, 'color', "Цвет фона");
$group->addField($colorPicker);
```

![Пример 3](http://storage3.static.itmages.com/i/17/0228/h_1488312454_1339012_e642a12df2.png)

Теперь нужно определить как поле будет хранить цвет в базе данных. Для этого мы должны создать в классе **ColorPicker** два новых метода:

_листинг файла acme/test\_plugin/src/Fields/ColorPicker.php:_
```php
class ColorPicker extends FieldType
{
    /**
     * Конвертируем значение из поля в базу данных
     * @param $value
     * @return mixed
     */
    public function serialize($value)
    {
        // код перевода данных в формат БД
        return $value;
    }

    /**
     * Конвертируем значение из базы в поле
     * @param $value
     * @return mixed
     */
    public function unserialize($value)
    {
        // код перевода данных из формата БД в формат поля
        return $value;
    }
}
```

База данных может хранить только простые скалярные типы данных, поле же может использовать любые сложные данные для отображения. Таким образом нам нужно перевести сложные данные в простые. 

В нашем случае виджет будет отображать 3 цвета RGB а база будет хранить их в виде строки. 

```php
// Исходные данные с которыми работает поле:
[
    "R" => "Целочисленное от 0 до 255"
    "G" => "Целочисленное от 0 до 255"
    "B" => "Целочисленное от 0 до 255"
]

// как мы это хотим хранить в базе данных
// 1. вариант hex строка
"#ff00ff" 
 // 2. вариант json строка
"{r:255,g:0,b:255}"
// 3. вариант сериализованная строка
"a:3:{s:1:\"r\";i:255;s:1:\"g\";i:0;s:1:\"b\";i:255;}" 
```

#### Подготовка данных (serialize)
Для простоты примера мы просто воспользуемся стандартной функцией **serialize()** для конвертации данных в формат строки и **unserialize()** для конвертации данных обратно в массив.


_листинг файла acme/test\_plugin/src/Fields/ColorPicker.php:_
```php
public function serialize($value) {
    return serialize($value);
}

public function unserialize($value) {
    return unserialize($value);
}
```

Теперь нужно разместить html код поля, для этого следует использовать функцию **renderWidget()**:

_листинг файла acme/test\_plugin/src/Fields/ColorPicker.php:_
```php
public function renderWidget($value, $vars)
{
    // $value - тут будут уже подготовленные для работы данные
    // $vars - сюда придут основные данные поля, такие как name, id, title и т.п.
    // к примеру мы можем получить id поля так:
    $id = $vars[FieldRepresentation::ATTR_ID];

    ob_start();
    ?>
        <input type="hidden" id="<?=$id?>">
        <b>This is my field html markup</b>
    <?
    return ob_get_clean();
}
```

Наше поле теперь выглядит так:
![Пример 4](http://storage2.static.itmages.com/i/17/0228/h_1488313910_6034579_69bd00b330.png)

Какие значения доступны в поле **$vars**?
```php
// атрибуты для input
FieldRepresentation::ATTR_ID => // id
FieldRepresentation::ATTR_NAME => // name
FieldRepresentation::ATTR_TITLE => // title

// доп. параметры
FieldRepresentation::ATTR_HELP => // html текст подсказки
FieldRepresentation::ATTR_DISABLED => // выключен? (true/false)
FieldRepresentation::ATTR_HIDDEN => // скрыт? (true/false)
FieldRepresentation::ATTR_READ_ONLY => // только чтение? (true/false)
FieldRepresentation::ATTR_VALUE => // значение (подготовленное)
FieldRepresentation::ATTR_VALUE_ORIGINAL => // значение из БД (исходное)
FieldRepresentation::ATTR_OPTIONS => // массив с опциями (ключ => значение)

// уже собранная строка
FieldRepresentation::ATTR_ATTRIBUTES_LINE => // включает в себя все стандартные параметры для поля. пример: (id=".das32.." name="..ew32.." title="my title" disabled)
```

В данном примере мы воспользуемся **HTML типом color**. Оно получает и отдает строку в виде HEX, что уже подходит для хранения в базе данных. Таким образом мы уберем функцию **serialize()** и **unserialize()** из нашего класса.

#### Конечный код будет выглядеть так:

_листинг файла acme/test\_plugin/src/Fields/ColorPicker.php:_
```php
namespace Acme\TestPlugin\Fields;

use Kitrix\Config\Admin\FieldRepresentation;
use Kitrix\Config\Admin\FieldType;

class ColorPicker extends FieldType
{
    public function renderWidget($value, $vars)
    {
        ob_start();
        ?>
        <input
            <?=$vars[FieldRepresentation::ATTR_ATTRIBUTES_LINE]?>
            type="color"
            value="<?=$value?>"
        >
        <?
        return ob_get_clean();
    }
}
```

_листинг файла acme/test\_plugin/src/TestPlugin.php:_
```php
$colorPicker = ConfRegistry::makeField(ColorPicker::class, 'color', "Цвет фона")

    // по умолчанию цвет будет красным
    ->setDefaultValue("#ff0000");
    
$group->addField($colorPicker);
```

#### Результат

![Пример 5](http://storage6.static.itmages.com/i/17/0228/h_1488315037_4480445_8001568b69.png)

_Сгенерированная html разметка_
```html
<input id="ktrx_field_cef57741e1834044632fcb301d06414219bf44ef" name="cef57741e1834044632fcb301d06414219bf44ef" title="Цвет фона" type="color" value="#ff0000">
```

