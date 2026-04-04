# Портфолио — Aleksander Keremidarov

Статично PHP портфолио (тъмна/светла тема, галерии, контактна форма с PHPMailer/SMTP през `.env`). По-долу е **вътрешна документация** за проекта BioMarket ERP (Laravel), използвана и на сайта.

**Локално:** `composer install`, копирай `.env.example` → `.env`, задай `MAIL_*` за изпращане на контакти.

---

## BioMarket ERP (internal)

BioMarket ERP е вътрешна уеб система за управление на хора, магазини и складове за веригата BioMarket / HealthStore. Проектът е базиран на Laravel и обединява HR, графици и заплати, обучения, склад и наличности, продуктов каталог, POS продажби, интеграция с PRIM, административни модули и други процеси.

> Забележка: този репозиторий е предназначен за вътрешна употреба и не е публично поддържан продукт.

---

## Основни модули (общ преглед)

- **Dashboard**
  - Централно табло с ключови показатели, бързо търсене и кратки връзки към основните модули.

- **Профил и HR**
  - `profile.*` – личен профил, известия, редакция на данни.
  - `my/shift*`, `schedule.overview` – графици, смени, календар на отсъствията, заявки за отпуска.
  - `documents.index`, `contract.show`, `timeoff.index`, `payroll.index` – документи, договор, отсъствия и фишове за заплати.

- **Payroll и финанси**
  - `payroll.*`, `payroll.salaries.*` – калкулатор на заплати, бонуси, аванси, изплащания по банка.
  - Импорт на payroll PDF файлове и масови изчисления през специални artisan команди.

- **Обучения и онбординг**
  - `training.*` – тестове, резултати, обучения, онбординг модули и секции.
  - `training.admin.*` – админ панел за създаване на уроци, въпроси, отговори и управление на достъпа по позиции.

- **Склад и наличности**
  - `warehouse.*` – индекс, търсене, застояли продукти, warehouse fill‑rate, експорти, маршрут за обхождане на склада.
  - Конзолни команди за синхронизация на наличности и продажби (напр. `prim:sync-availabilities`, `inventory-sales:*`, `product-batches:*`).

- **Продуктов каталог и цени**
  - `product-catalog.*` – продуктов каталог от таблицата `product_catalog_items`: размери, баркод, HS код, опаковки и др.
  - `sales.price-lists.*`, `store.product-prices.*` – ценови листи, цени по магазини и специални ценови условия.

- **Интеграция с PRIM**
  - `prim.sales_orders.*` – фетчване и синхронизация на продажби от PRIM.
  - `prim.availabilities.*` – наличности по складове от PRIM (страница + конзолна команда `prim:sync-availabilities`).

- **Магазини и POS**
  - `branches.*`, `branches.shifts.*` – магазини, смени и капацитет по обекти.
  - `store.*`, `pos-sale` – POS продажби, планограми, касови сметки, вътрешни документи и музика по обекти.

- **Sales funnel / CRM**
  - `sales-funnel.*` – клиенти, записи от посещения, отстъпки, кампании и календар.

- **Администрация**
  - `admin.*` – роли и права, агенти, компании, касови сметки, типове приходи/разходи, достъп по категории, IP whitelist, impersonation.

Структурата на всички рутове може да се разгледа в `routes/web.php` и съответните контролери в `app/Http/Controllers`.

---

## Технологии

- **Backend**: Laravel (PHP 8.x)
- **Frontend**: Blade templates, Bootstrap, custom JS
- **База данни**: MySQL/MariaDB
- **Опашки / фон задачи**: Laravel Queue (`queue:work`), cron jobs
- **Интеграции**: PRIM API (продажби, наличности, продуктов каталог)

---

## Клониране и първоначална инсталация (development)

```bash
git clone git@github.com:BioMarket-HealthStore/biomarketERP.git
cd biomarketERP
```

Инсталиране на PHP зависимостите:

```bash
composer install
```

Създаване на `.env` и app key:

```bash
cp .env.example .env   # под Windows: copy .env.example .env
php artisan key:generate
```

Конфигурирай в `.env`:

- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- достъп до PRIM API, имейл настройки и др. (според вътрешната документация).

Стартиране на миграции и seed-ове (само ако е подходящо за твоята среда):

```bash
php artisan migrate --seed
```

Инсталиране на front-end зависимости и билд:

```bash
npm install
npm run build   # или npm run dev за локална разработка
```

Стартиране на приложението (локално):

```bash
php artisan serve
```

---

## Полезни Artisan команди

Най-често използваните конзолни команди (освен стандартните на Laravel):

- **Продуктов каталог**
  - `php artisan product-catalog:sync-from-inventory`  
    Копира всички уникални SKU от таблица `inventory` в `product_catalog_items`, като добавя **само новите** SKU. Показва progress bar и детайлен репорт.
  - `php artisan product-catalog:sync-from-prim`  
    Взима всички SKU от PRIM API и добавя липсващите в `product_catalog_items`. Работи аналогично на бутона „Синхронизация“ в екрана „Product Catalog“ и показва детайлно резюме (нови продукти, частични данни, грешки).

- **PRIM наличности и продажби**
  - `php artisan prim:sync-availabilities`  
    Синхронизира наличностите по складове/магазини от PRIM в локалната база, с прогрес и статистика.
  - `php artisan inventory-sales:sync-full` и свързаните команди  
    Пълна/частична синхронизация на таблицата `inventory_sales` от различни източници (PRIM, Excel, sales_orders).

- **Warehouse / product batches**
  - `php artisan product-batches:sync` и `php artisan product-batches:sync-by-warehouse`  
    Дърпат и записват партиди (batch данни) по склад и магазин.

- **Cron интеграции (production пример)**
  - На production сървъра има cron задачи, които стартират:
    - `queue:work`
    - скриптове за warehouse sync
    - `sales-orders:import-with-sku-3days`
    - `delivery-rules:recalculate-all`
    - `warehouse:fill-rate-snapshot`
    - `prim:sync-availabilities`
    - `product-batches:sync`
    - `product-catalog:sync-from-prim`

---

## Quality toolkit (code style, static analysis, tests)

Проектът има централизирана команда за качество:

```bash
composer quality
```

Тя прави **две неща последователно** (само върху променени PHP файлове където е приложимо за Pint):

- **Pint** – проверка на стила на променените файлове (`--test`, без автоматично пренаписване).
- **ParaTest** – паралелно пускане на unit/feature тестовете.

**PHPStan** и **security audit** не са част от `composer quality`. Пускай ги отделно при нужда:

```bash
composer analyse      # PHPStan върху променени файлове (app / routes / database)
composer audit        # известни уязвимости в пакетите
```

Ако някоя стъпка от `composer quality` не мине, командата връща **грешка** – оправи проблема преди push.

### Локална инсталация на quality tool‑овете

Всички инструменти се инсталират автоматично през `composer install`:

- `laravel/pint` – за форматиране на PHP.
- `phpstan/phpstan` + `nunomaduro/larastan` – статичен анализ.
- `phpunit/phpunit` + `brianium/paratest` – тестове.

Няма нужда от глобална инсталация, достатъчно е:

```bash
composer install
```

След това можеш да пускаш:

```bash
composer quality    # Pint (променени файлове) + паралелни тестове
composer analyse    # само PHPStan (променени файлове)
composer audit      # само security audit
vendor/bin/pint     # целия проект или подай пътища
php artisan test    # само Laravel тестове (без paratest)
```

### Git hooks и локален workflow

- В `.githooks/pre-commit` има hook, който **е умишлено деактивиран**:

```sh
#!/bin/sh

# Disabled locally – `composer quality` is run manually before commits.
exit 0
```

- Причината е, че под Windows/WSL hook‑ът понякога създава проблеми с `fork`, а quality процесът е по‑бързо и по‑прозрачно да се пуска ръчно.
- Препоръчителен workflow за всеки developer:

```bash
git pull
composer quality   # изчисти всички проблеми до зелено
git status
git commit -m "..."   # без auto-hook, но след ръчен quality
git push
```

На CI (GitHub Actions) за този проект се пускат **Pint** (инкрементално) и **тестовете**; статичен анализ и `composer audit` не са в workflow освен ако не се добавят отново изрично.

---

## Стил на разработка / PR процес

- Основни дългоживеещи branch-ове: обичайно `main` / `production` (според текущата стратегия).
- Функционалните промени се разработват в feature branch-ове и се вливат през Pull Request.
- GitHub Copilot Code Review е активиран:
  - автоматично ревюира PR към default branch и към `production`;
  - може да се използва и ръчно чрез `/copilot review` в коментар.

### Бележка за тестови PR-и

За тестване на Copilot review и CI/CD настройките се препоръчва да се използват кратки PR-и (например само с промени в `README.md`), създадени от отделен branch (напр. `test-copilot-review`), които след успешен тест се затварят или squash-merge-ват.

---

## Security / достъп

Проектът е вътрешен за BioMarket / HealthStore и не е предназначен за публично хостване без допълнителна конфигурация (firewall, HTTPS, rate limiting, одит на ролите и т.н.). При открити уязвимости или проблеми със сигурността, свържи се с вътрешния екип, който поддържа системата.
