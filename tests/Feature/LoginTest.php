<?php

it('can render admin login page', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});
