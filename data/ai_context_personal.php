<?php

declare(strict_types=1);

/**
 * Допълнителен контекст за AI асистента (одобрени факти + разширено CV / base knowledge).
 * Включва се в includes/ai-chat.php заедно с data/profile.php.
 * Редакция: синхронизирай при нужда и data/base_knowledge.txt / ai_context_personal.md.
 *
 * @var array{bg: string, en: string}
 */
return [
    'bg' => <<<'TXT'
Контакт: Александър Керемидаров · alexander.krist@gmail.com · тел. +359 877750552 · София, България.
Също отговаря на: Алекс, Сашо.

Философия (както е споделено): „За да постигнеш нещо, са нужни упоритост, труд и вяра, че ще успееш.“ Ценности: екипен играч, ориентиран към цели, структуриран, отворен ум, бързо учене, изграждане на доверие, силни отношения, не се отказва.

Образование:
· 2015–2018 — бакалавър по инженерна ИКТ, VIA University College, Дания: Java, HTML/CSS, .NET, SQL, мрежи, Android. Дипломен проект с Laravel — сайт за обяви за продажба на музикални инструменти, регистрация, профил, управление на обяви и потребители.
· 2003–2006 — Datamatiker и бизнес мениджмънт, Vejle Teknik Skole, Дания: Java, бизнес мениджмънт, логистика.
· Допълнително през ИКТ образованието: .NET, Android, разпределени системи, C#, Java структури, архитектура на компютър, мрежи, релационни БД, UML, клиент/сървър Java, responsive web (HTML/CSS/JS), комуникационни умения за инженери.
· Стаж: 2016–2017 Intellia — WordPress (теми, плъгини, миграции, бъгове).

Текуща и скорошна работа (от base knowledge):
· Freelance / разработчик в Pavelandreev.bg — REST API (DeepL, IP локализация), интеграция Paysera, нов дизайн; Laravel, PHP, MySQL, Ajax, JavaScript.
· Bteam (посочено като текущ контекст в базата) — ваучерна/свързана система (виж и портфолио за детайли).
· 2021.06–2023.06 — Looming Tech: Laravel, Orchid admin, REST API (UK училище, UK телефон), MySQL, Ajax, JS; Monizze — белгийски ваучери, Laravel, REST, Vue.js, Docker, рефакторинг, нови функции.
· 2019.11–2021.05 — Shippii Technologies, Junior PHP (Laravel/October CMS): shipping, рефакторинг, функции, бъгове.
· В обобщение са споменати още: Proxiad (според base knowledge).
· 2006–2014 — ProPeople Ltd (сега FFW Agency), office manager.

Умения (обобщение): Laravel PHP backend, Symfony и October CMS, front-end HTML/CSS/JS, Bootstrap и Tailwind, MySQL, Git, Jira, bug fixing, REST API интеграции, имплементация на функции.

Езици: български — роден; английски — добър; руски — добър; датски — второ ниво.

Лични данни (за чата): височина ~1,80 m, тегло ~65 kg, сини очи.

Семейство и дом: живее с приятелката Криси; тя има две деца — Вики и Марти; участва в грижата за тях. Сестра Линда и майка Зоя живеят в Дания. Живял дълго в Дания. Живее в центъра на София, стара сграда с голям двор.

Любимци: куче — кокер шпаньол Жана (на разходка в парка); котка Маги (бяла).

Спорт и гледане: Формула 1, футбол (ПФК Левски), снукър, ски, тенис. Много добър на ски.

Хобита и интереси: кучета, бързи коли, топло време; риболов; любими коли: Mini Cooper, BMW, Golf GTI; рок музика от 80-те и 90-те; любима книга: „Пътеводител на галактическият стопаджия“; харесва и Толкин, Пратчет, Кинг, фентъзи/фантастика/криминале.
Места: лято на Черно море — река Велека, Созопол, Царево.

Любим framework: Laravel.

Интересни факти: гледа Ф1; ходи на мачове на Левски; харесва да ходи в парка с кучето.
TXT,
    'en' => <<<'TXT'
Contact: Aleksander Keremidarov · alexander.krist@gmail.com · Phone +359 877750552 · Sofia, Bulgaria.
Also goes by: Alex, Sasho.

Beliefs (as shared): "To achieve anything requires perseverance, hard work and faith that you will succeed." Team player, goal-oriented, structured, open-minded, quick learner, builds trust and strong relationships, does not give up.

Education:
· 2015–2018 — Bachelor of Engineering in Information and Communication Technology, VIA University College, Denmark: Java, HTML & CSS, .NET, SQL, networks, Android. Bachelor project with Laravel — website for ads for musical instruments, registration, user profiles, ad and user management.
· 2003–2006 — Datamatiker and business management, Vejle Teknik Skole, Denmark: Java, business management, logistics.
· ICT engineering coursework also included: .NET, Android, distributed systems, serious game C#, abstract data structures in Java, computer architecture C, applied Java, networking, relational databases, UML, client/server Java, responsive web design, communication skills for engineers.
· Internship 2016–2017 Intellia — WordPress (themes, plugins, migrations, fixes).

Work experience (base knowledge + CV-style):
· Current: freelance developer at Pavelandreev.bg — REST APIs (DeepL, IP localization), Paysera payment integration, new design implementation; Laravel, PHP, MySQL, Ajax, JavaScript.
· Bteam noted as current in knowledge base (voucher-related work; see portfolio for public details).
· 2021.06–2023.06 — Looming Tech: Laravel, Orchid admin, REST API (UK school, UK phone), MySQL, Ajax, JS; Monizze — Belgian voucher system, Laravel, REST, Vue.js, Docker, refactoring, features, bug fixes.
· 2019.11–2021.05 — Shippii Technologies EOOD, Junior PHP (Laravel/October CMS): shipping system, refactoring, features, bug fixes.
· Summary list also mentions: Proxiad (per base knowledge).
· 2006–2014 — ProPeople Ltd (now FFW Agency), office manager.

Skills summary: Laravel PHP backend, Symfony & October CMS, front-end HTML/CSS/JS, Bootstrap & Tailwind CSS, MySQL, Git, Jira, bug fixing, REST API integration, feature implementation.

Languages: Bulgarian — native; English — good; Russian — good; Danish — second level.

Personal: height about 1.80 m, weight about 65 kg, blue eyes.

Home & family: lives with girlfriend Krisi; she has two children, Viki and Marti; he helps care for them. Sister Linda and mother Zoya live in Denmark. Lived many years in Denmark. Lives in central Sofia in an old building with a large backyard.

Pets: Cocker Spaniel named Jana (walks in the park); white cat Magi.

Sports & watching: Formula 1, football (PFC Levski), snooker, skiing, tennis. Very good at skiing.

Hobbies: dogs, fast cars, hot weather; fishing; favourite cars Mini Cooper, BMW, Golf GTI; rock music from the 80s and 90s; favourite book The Hitchhiker's Guide to the Galaxy; also enjoys Tolkien, Pratchett, King, fantasy/sci-fi/crime.
Places: summer at the Black Sea — Veleka river, Sozopol, Tsarevo.

Favourite framework: Laravel.

Interesting facts: watches F1; supports Levski; likes going to the park with Jana (dog).
TXT,
];
