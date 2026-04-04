# Портфолио — Aleksander Keremidarov

Личен уеб сайт: представяне, избрани проекти, галерии и контактна форма. PHP без framework, данни в `data/`, стилове и скриптове в `assets/`, изпращане на имейл през **PHPMailer** и SMTP от `.env`.

**Репозиторий:** [github.com/sasho-krist/portfolio](https://github.com/sasho-krist/portfolio)

## Възможности

- Светла и тъмна тема (запомняне в браузъра)
- Секция **Проекти** с изглед таблица / карти
- Галерия към кейса **BioMarket ERP** и галерия `images/dogs`
- **Контакти** — форма с CSRF; SMTP през променливите `MAIL_*`; при неуспех опционален fallback към PHP `mail()`
- Данни за профил и проекти от `data/profile.php` и `data/projects.php`

## Изисквания

- PHP **8.1+** (препоръчително 8.2 / 8.3)
- [Composer](https://getcomposer.org/) — за `vendor/` и PHPMailer

## Локална инсталация

```bash
git clone git@github.com:sasho-krist/portfolio.git
cd portfolio
composer install
```

Копирай настройките за пощата:

```bash
cp .env.example .env
```

Под Windows (напр. WAMP): `copy .env.example .env`

Попълни SMTP полетата в `.env` — описани са в [.env.example](.env.example). Стартирай сайта през виртуален хост към root папката на проекта или през вградения PHP сървър от тази папка.

## Структура

| Път | Назначение |
| --- | --- |
| `index.php` | Главна страница |
| `contact-send.php` | Обработка на контактната форма |
| `data/profile.php`, `data/projects.php` | Текстове, линкове, проекти |
| `includes/` | Шапка, футър, зареждане на `.env`, SMTP |
| `assets/css`, `assets/js` | Стилове и поведение (тема, галерии, изгледи) |
| `images/` | Снимки за портфолиото и ERP секцията |
| `docs/BIOMARKET-ERP.md` | Пълна вътрешна документация за Laravel кейса BioMarket ERP |

## Кейс: BioMarket ERP

Модулите, технологиите, Artisan командите и вътрешният workflow на ERP системата са описани в **[docs/BIOMARKET-ERP.md](docs/BIOMARKET-ERP.md)**. На сайта същият markdown се показва в секцията за проекта.

---

Поверителност: не качвай `.env` в git — репозиторият вече го игнорира чрез `.gitignore`.
