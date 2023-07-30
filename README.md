![PHP version](https://img.shields.io/badge/php-8.2-777bb3.svg?logo=php)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
![tests](https://github.com/qurum/testassignment-futuregroup/actions/workflows/tests.yml/badge.svg)

## Тестовое задание на позицию PHP-разработчика для Future group.

PHP 8.2, контейнер - PHP DI, маршрутизация - Slim 4, валидатор - Respect Validation, миграции - Phinx.  
Главный файл - [back/main.php](back/main.php), конфигурация контейнера в [back/container.php](back/container.php).  
БД, для простоты, SQLite.  

## Локальная установка
``./install.sh`` скопирует конфигурационные файлы и выставит права на директорию с базами данных.  

## Тестирование
Для тестов используется Codeception 5.  
``./test.sh`` запустит тесты в докере.  

[back/tests/Api/NotebookCest.php](back/tests/Api/NotebookCest.php) - API-тесты,
[back/tests/Unit/NotebookSQLiteRepositoryCest.php](back/tests/Unit/NotebookSQLiteRepositoryCest.php) - юнит.  
Тесты запускаются в отдельном наборе контейнеров, API-тесты - внутри виртуальной сети.  

## Запуск
После ``docker-compose up`` API будет доступно на localhost:8000, сваггер на localhost:8001.  
Postman v2.1 лежит в [docs/Notebooks.postman_collection.json](docs/Notebooks.postman_collection.json)  

## GitHub Actions
Запуск тестов по push.  
Результаты тут: https://github.com/qurum/testassignment-futuregroup/actions/workflows/tests.yml  
