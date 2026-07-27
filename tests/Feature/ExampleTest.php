<?php

test('home redirects unauthenticated guests to register', function () {
    $response = $this->get(route('home'));

    $response->assertRedirect('/register');
});
