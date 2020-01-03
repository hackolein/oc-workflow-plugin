<?php return [
    'plugin' => [
        'name' => 'Workflow',
        'description' => 'Fügt ein Veröffentlichungszeitpunkt bzw. Offlinezeitpunk und eine Staus zu einer CMS oder Statischen Seiten hinzu.',
    ],
    'form' => [
        'published_at' => 'Veröffentlichungszeitpunkt',
        'published_at_comment' => '',
        'offline_at' => 'Offlinezeitpunk',
        'offline_at_comment' => '',
        'status' => [
            'label' => 'Seiten Status',
            'comment' => '',
            'published' => 'Veröffentlicht',
            'draft' => 'Entwurf',
        ],
    ],
];