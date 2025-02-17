<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Пакет русской локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'Службы',
    '{description}' => 'Управление службами (сервисами), как ключевыми компонентами фреймворка',
    '{permissions}' => [
        'any'  => ['Полный доступ', 'Управление службами (сервисами)']
    ],

    // Grid: панель инструментов
    'Remove services from startup' => 'Удаление всех служб из автозагрузки',
    'Do you really want to remove all services from startup?' => 'Все действительно желаете удалить все службы из автозагрузки?',
    // Grid: столбцы
    'Name' => 'Имя',
    'Class' => 'Класс',
    'The service is in the startup for Frontend' => 'Служба находится в автозагрузке Frontend',
    'The service is in the startup for Backend' => 'Служба находится в автозагрузке Backend',
    'The service was created in your last request' => 'Служба была задействована в последнем запросе',
    'The service has its own configurator' => 'Служба имеет свой конфигуратор',
    // Grid: сообщения / заголовки
    'Add to startup' => 'Добавление в автозагрузку',
    'Remove from startup' => 'Удаление из автозагрузки',
    // Grid: сообщения / текст
    'Service "{0}" successfully added to startup for Frontend, but remove for Backend' => 'Служба "<b>{0}</b>" успешно добавлена в автозагрузку для Frontend, но изъята для Backend.',
    'Service "{0}" successfully added to startup for Backend, but remove for Frontend' => 'Служба "<b>{0}</b>" успешно добавлена в автозагрузку для Backend, но изъята для Frontend.',
    'Service "{0}" successfully added to startup for Frontend and Backend' => 'Служба "<b>{0}</b>" успешно добавлена в автозагрузку для Frontend и Backend.',
    'Service "{0}" successfully removed from startup' => 'Служба "<b>{0}</b>" успешно удалена из автозагрузки.',
    'removing service {0} from startup' => 'изъятие службы "<b>{0}</b>" из автозагрузки',
    'adding service {0} to startup' => 'добавление службы "<b>{0}</b>" в автозагрузку',
    'All services removed from startup' => 'Все службы удалены из автозагрузки',
    // Grid: сообщения / ошибки
    'Unknown service name' => 'Неизвестное имя службы',
];
