<?php

test('old preview URLs lead to the current frontend', function (string $old, string $destination) {
    $this->get('/design-preview/'.$old)->assertRedirect($destination);
})->with([['home', '/'], ['jobs', '/jobs'], ['skills', '/skills'], ['employers', '/employers'], ['about', '/about'], ['register', '/register'], ['forgot', '/forgot-password']]);
