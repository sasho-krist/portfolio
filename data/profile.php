<?php

declare(strict_types=1);

return [
    'name' => 'Aleksander Keremidarov',
    'title' => 'Full stack Web Developer',
    'tagline' => 'Laravel, WordPress и PHP backend с фокус върху ясна архитектура и реални бизнес процеси.',
    'tagline_en' => 'Laravel, WordPress and PHP backend focused on clear architecture and real business workflows.',
    'location' => 'София, България',
    'location_en' => 'Sofia, Bulgaria',
    'email' => 'alexander.krist@gmail.com',
    'phone' => '+359 877 750 552',
    'github' => 'https://github.com/sashokrist',
    'site_repo_url' => 'https://github.com/sasho-krist/portfolio',
    'linkedin' => '', // пълен URL към профил, напр. https://www.linkedin.com/in/...
    // Насрочване на срещи (Google Календар → Настройки → „Насрочване на срещи“ / appointment schedule).
    // Ако линкът показва „Срещата не бе намерена“, създай ново насрочване и подмени URL-а или задай CALENDAR_URL в .env
    'calendar' => 'https://calendar.app.google/VrS5H47ZJH7uKdNL7',
    'seo_title' => 'Aleksander Keremidarov | PHP & Laravel · Sofia',
    'seo_title_en' => 'Aleksander Keremidarov | PHP & Laravel · Sofia',
    'seo_description' => 'Aleksander Keremidarov — PHP & Laravel full stack разработчик в София. WordPress, REST API, MySQL, React, Next.js. Портфолио и контакт.',
    'seo_description_en' => 'Aleksander Keremidarov — PHP & Laravel full stack developer in Sofia. WordPress, REST API, MySQL, React, Next.js. Portfolio and contact.',
    'seo_keywords' => [
        'Alexander Keremidarov',
        'Aleksander Keremidarov',
        'PHP developer',
        'Laravel developer',
        'WordPress developer',
        'full stack developer',
        'backend developer',
        'API developer',
        'web developer Sofia',
        'web developer Bulgaria',
        'Bulgaria PHP',
        'freelance Laravel developer',
        'REST API',
        'MySQL',
        'PHP 8',
        'Laravel',
        'JavaScript',
        'TypeScript',
        'React',
        'Next.js',
        'Vue.js',
        'Tailwind CSS',
        'Bootstrap',
        'October CMS',
        'Docker',
        'remote developer',
        'EU freelance',
        'hire PHP developer',
    ],
    'seo_same_as' => [
        'https://github.com/sasho-krist/portfolio',
    ],
    'testimonials' => [
        [
            'quote' => 'Александър работи структурирано и комуникира ясно — Laravel модулите и WordPress интеграциите бяха доставени в срок, с внимание към поддръжката.',
            'name' => 'SEO Healthstore',
            'role' => 'Tech Lead',
            'company' => '',
        ],
        [
            'quote' => 'Силен Laravel backend принос към системата за ваучери: ясни API договори, предвидими release-и и бързо реагиране при продукшън приоритети.',
            'name' => 'Bteam',
            'role' => 'Project Manager',
            'company' => '',
        ],
        [
            'quote' => 'Надежден партньор по WordPress и Laravel — разбира бизнес контекста, държи на качеството на кода и комуникацията с екипа.',
            'name' => 'Devrix',
            'role' => 'CEO',
            'company' => '',
        ],
    ],
    'profile' => 'Резултатно ориентиран Full Stack разработчик с 7 години опит в изграждане на мащабируеми Laravel и WordPress платформи. Разработвам RESTful API, сложна бизнес логика и съвременни интерфейси с React и Next.js. Интересувам се от чиста архитектура, производителност и практични решения.',
    'profile_en' => 'Results-driven full stack developer with 7 years of experience building scalable Laravel and WordPress platforms. I ship RESTful APIs, complex domain logic and modern UIs with React and Next.js. I care about clean architecture, performance and pragmatic solutions.',
    'years_experience' => '7',
    'about_bullets' => [
        'Full stack с акцент върху Laravel backend, WordPress и PHP 8.',
        'Силен в REST API, интеграции, опашки и ясна домейн логика.',
        'Търся проекти: Laravel/backend, WordPress, API — freelance, кооперации или фиксирана позиция.',
        'Работя freelance и remote; отворен за екипи в EU timezone.',
    ],
    'about_bullets_en' => [
        'Full stack with a focus on Laravel backend, WordPress and PHP 8.',
        'Strong in REST APIs, integrations, queues and clear domain logic.',
        'Looking for: Laravel/backend, WordPress, API work — freelance, team collaboration or permanent role.',
        'Freelance and remote; open to EU timezone teams.',
    ],
    /** Един ред под tagline: какво търсиш / фокус (hero). */
    'hero_focus' => 'Търся проекти с Laravel, WordPress и API — freelance, екипна работа или фиксирана позиция; remote, EU timezone.',
    'hero_focus_en' => 'Looking for Laravel, WordPress and API work — freelance, collaboration or a permanent role; remote, EU timezone.',
    /** Кратък текст над списъка репота в секция GitHub. */
    'github_section_intro' => 'Публични репота с акцент върху Laravel, WordPress и API. Пълен профил и история на commit-и на GitHub.',
    'github_section_intro_en' => 'Public repos focused on Laravel, WordPress and APIs. Full profile and commit history on GitHub.',
    /**
     * Акцентни репота (като „pinned“). label, url, опционално кратко описание.
     *
     * @var list<array{label: string, url: string, note?: string, demo?: string}>
     */
    'github_repos' => [
        ['label' => 'questionnaire_ai', 'url' => 'https://github.com/sasho-krist/questionnaire_ai', 'note' => 'Laravel 12, OpenAI, анкети', 'demo' => 'https://sasho-dev.com/anketi/questionnaires'],
        ['label' => 'booking-hotel-app', 'url' => 'https://github.com/sashokrist/booking-hotel-app', 'note' => 'PMS sync, опашки'],
        ['label' => 'traffic_tracker', 'url' => 'https://github.com/sashokrist/traffic_tracker', 'note' => 'Laravel, отчети, Swagger'],
        ['label' => 'wp-api-hotel-booking', 'url' => 'https://github.com/sashokrist/wp-api-hotel-booking', 'note' => 'WordPress ↔ Laravel API'],
        ['label' => 'credit-system', 'url' => 'https://github.com/sashokrist/credit-system', 'note' => 'Домейн логика, UI'],
        ['label' => 'portfolio', 'url' => 'https://github.com/sasho-krist/portfolio', 'note' => 'Този сайт'],
    ],
    'github_repos_en' => [
        ['label' => 'questionnaire_ai', 'url' => 'https://github.com/sasho-krist/questionnaire_ai', 'note' => 'Laravel 12, OpenAI, questionnaires', 'demo' => 'https://sasho-dev.com/anketi/questionnaires'],
        ['label' => 'booking-hotel-app', 'url' => 'https://github.com/sashokrist/booking-hotel-app', 'note' => 'PMS sync, queues'],
        ['label' => 'traffic_tracker', 'url' => 'https://github.com/sashokrist/traffic_tracker', 'note' => 'Laravel, reports, Swagger'],
        ['label' => 'wp-api-hotel-booking', 'url' => 'https://github.com/sashokrist/wp-api-hotel-booking', 'note' => 'WordPress ↔ Laravel API'],
        ['label' => 'credit-system', 'url' => 'https://github.com/sashokrist/credit-system', 'note' => 'Domain logic, UI'],
        ['label' => 'portfolio', 'url' => 'https://github.com/sasho-krist/portfolio', 'note' => 'This site'],
    ],
    'resume_url' => 'CV eng.pdf', // английско CV в root — показва се „Свали PDF“ в контактите
    'skills_cards' => [
        'Backend, бази данни и CMS' => 'PHP 8, Laravel (7 години), Laravel Sanctum, JavaScript, Ajax, WordPress (custom плъгини, CPT), REST API, MySQL, MariaDB, PostgreSQL, RabbitMQ и опашки. CMS: WordPress, October CMS, custom Laravel CMS.',
        'Frontend и UI' => 'HTML, CSS, Bootstrap, Tailwind CSS, Blade, JavaScript, React, Next.js, Vue.js (уча се).',
        'Локална среда и deploy' => 'Локална разработка: Windows (XAMPP, WAMP), Linux, WSL. Качване и конфигурация: Nginx, Apache, SSH. Docker при нужда. Интеграции към външни услуги (REST).',
        'Git, проекти и хостинг' => 'Git — клонове, merge/PR, GitHub и Bitbucket. Управление на задачи: Jira, Trello, Monday, ClickUp. Хостинг: cPanel, FTP, SSH, инсталация на CMS, mail и database конфигурация.',
    ],
    'skills_cards_en' => [
        'Backend, databases & CMS' => 'PHP 8, Laravel (7 years), Laravel Sanctum, JavaScript, Ajax, WordPress (custom plugins, CPT), REST API, MySQL, MariaDB, PostgreSQL, RabbitMQ and queues. CMS: WordPress, October CMS, custom Laravel CMS.',
        'Frontend & UI' => 'HTML, CSS, Bootstrap, Tailwind CSS, Blade, JavaScript, React, Next.js, Vue.js (learning).',
        'Local environment & deployment' => 'Local development: Windows (XAMPP, WAMP), Linux, WSL. Deployment and config: Nginx, Apache, SSH. Docker when needed. Integrations with external services (REST).',
        'Git, delivery & hosting' => 'Git — branches, merges/PRs, GitHub and Bitbucket. Task tracking: Jira, Trello, Monday, ClickUp. Hosting: cPanel, FTP, SSH, CMS setup, mail and database configuration.',
    ],
    'services' => [
        'Уеб разработка и разширяване на съществуващи системи',
        'REST API и интеграции с външни услуги',
        'WordPress: плъгини, теми, Custom Post Types',
        'Laravel: модули, миграции, админ табла, опашки и кеширане',
    ],
    'services_en' => [
        'Web development and extending existing systems',
        'REST APIs and integrations with third-party services',
        'WordPress: plugins, themes, custom post types',
        'Laravel: modules, migrations, admin panels, queues and caching',
    ],
    'education' => [
        [
            'period' => '2015 – 2018',
            'degree' => 'Бакалавър по информационни и комуникационни технологии',
            'school' => 'VIA University College, Дания',
            'details' => [
                'Фокус: Java, .NET, SQL, Android, мрежи',
                'Дипломен проект: Laravel платформа за обяви за музикални инструменти',
            ],
        ],
        [
            'period' => '2003 – 2006',
            'degree' => 'Java разработка и бизнес мениджмънт',
            'school' => 'Vejle Teknik Skole, Дания',
            'details' => [
                'Фокус: Java програмиране, логистика и бизнес мениджмънт',
            ],
        ],
    ],
    'experience' => [
        [
            'period' => 'юни 2025 – настояще',
            'role' => 'Full Stack Developer',
            'company' => 'Devrix',
            'desc' => 'Laravel и WordPress backend: нови функции, корекции на бъгове, API интеграции, custom плъгини и теми.',
        ],
        [
            'period' => 'апр. 2025 – юни 2025',
            'role' => 'Freelance',
            'company' => 'ERP — обучения и тестова система',
            'desc' => 'Изграждане от нулата: категории, тестове, въпроси и отговори, права по групи, режими learning/test, UX с collapse/expand и bulk операции. Laravel + Bootstrap.',
        ],
        [
            'period' => 'Freelance',
            'role' => 'Full stack',
            'company' => 'BioMarket ERP',
            'desc' => 'Вътрешна ERP система за веригата BioMarket / HealthStore: Laravel PHP 8.x, MySQL, Blade, интеграции (PRIM), склад, HR, payroll, POS и др. (вътрешен проект).',
        ],
        [
            'period' => 'сеп. 2023 – апр. 2025',
            'role' => 'Full stack Laravel backend',
            'company' => 'Bteam',
            'desc' => 'Система за ваучери: REST API, нови функции, поддръжка, custom WordPress плъгини.',
        ],
        [
            'period' => 'юни 2021 – юни 2023',
            'role' => 'Full stack Laravel',
            'company' => 'Looming Tech',
            'desc' => 'Laravel системи с Orchid Admin, REST API, Vue.js табла, Docker deployment.',
        ],
        [
            'period' => 'ное. 2019 – май 2021',
            'role' => 'Laravel & OctoberCMS backend',
            'company' => 'Shippii Technologies',
            'desc' => 'Архитектура и рефакторинг на shipping система, нови функции, поддръжка.',
        ],
    ],
    'languages' => [
        'Bulgarian' => 'Native',
        'English' => 'Advanced',
        'Russian' => 'Intermediate',
        'Danish' => 'Basic (2nd level)',
    ],
    'interests' => [
        ['label' => 'Кучета', 'label_en' => 'Dogs', 'href' => '#dogs'],
        ['label' => 'fast cars'],
        ['label' => 'F1'],
    ],
    'quick_facts' => [
        'Laravel 10+, WordPress PHP 8+, JavaScript, MySQL/MariaDB',
        'Real-time: Laravel WebSockets, опашки (RabbitMQ)',
        'Frontend: HTML, CSS, Bootstrap, Tailwind, Blade, React, Vue 3 (уча се)',
        'Работен процес: Git (клонове, PR), GitHub; PM: Jira, Trello, Monday, ClickUp · Локално: XAMPP/WAMP, WSL · Deploy: Nginx/Apache; Docker при нужда',
    ],
    'quick_facts_en' => [
        'Laravel 10+, WordPress PHP 8+, JavaScript, MySQL/MariaDB',
        'Real-time: Laravel WebSockets, queues (RabbitMQ)',
        'Frontend: HTML, CSS, Bootstrap, Tailwind, Blade, React, Vue 3 (learning)',
        'Workflow: Git (branches, PRs), GitHub; PM: Jira, Trello, Monday, ClickUp · Local: XAMPP/WAMP, WSL · Deploy: Nginx/Apache; Docker when needed',
    ],
];
