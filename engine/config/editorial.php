<?php

return [
    'readability' => [
        'max_sentence_length' => 20, // max average words per sentence
        'max_passive_voice_percentage' => 15,
        'average_reading_speed_wpm' => 200, // words per minute
    ],
    'phrases' => [
        'max_repetition_percentage' => 20, // If a phrase is used more than 20% of the time, flag it
    ]
];
