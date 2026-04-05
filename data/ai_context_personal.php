<?php

declare(strict_types=1);

/**
 * Допълнителен контекст за AI асистента (лични факти, които собственикът е одобрил за чата).
 * Редактирай тук — prompt-ът в includes/ai-chat.php включва този блок заедно с data/profile.php.
 *
 * @var array{bg: string, en: string}
 */
return [
    'bg' => <<<'TXT'
Семейство и дом: Александър живее с приятелката си Криси. Криси има две деца — Марти и Вики; Александър участва в грижата за тях.
Любими животни: Александър много обича животни. Домашни любимци: черен кокер шпаньол на име Жана; котка на име Маги.
Спорт: привърженик на футболен клуб ПФК Левски (София).
Автомобили: любими марки/модели — BMW, Mini, VW Golf GTI.
Книги и четене: „Пътеводител на галактическият стопаджия“; „Властелинът на пръстените“ и „Хобит“; поредицата „Светът на диска“ на Тери Пратчет; творби на Стивън Кинг; жанрове — фантастика, фентъзи, криминални романи.
Роднини в чужбина: сестра Линда и майка Зоя живеят в Дания.
TXT,
    'en' => <<<'TXT'
Home and family: Aleksander lives with his partner Chrissy. Chrissy has two children — Marti and Vicki; Aleksander helps care for them.
Pets: he loves animals. Pets: black Cocker Spaniel named Jana; cat named Magi.
Sports: supporter of PFC Levski Sofia.
Cars: favourite brands/models — BMW, Mini, VW Golf GTI.
Books and reading: The Hitchhiker’s Guide to the Galaxy; The Lord of the Rings and The Hobbit; Terry Pratchett’s Discworld series; Stephen King’s works; genres — sci-fi & fantasy, crime novels.
Family abroad: sister Linda and mother Zoya live in Denmark.
TXT,
];
