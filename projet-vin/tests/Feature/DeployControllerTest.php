<?php

test('deploy endpoint stays hidden when no token is configured', function () {
    config(['deploy.token' => null]);

    $response = $this->postJson(route('deploy.run'), [], ['X-Deploy-Token' => 'anything']);

    $response->assertNotFound();
});

test('deploy endpoint rejects a missing token', function () {
    config(['deploy.token' => 'valid-token']);

    $response = $this->postJson(route('deploy.run'));

    $response->assertNotFound();
});

test('deploy endpoint rejects an invalid token', function () {
    config(['deploy.token' => 'valid-token']);

    $response = $this->postJson(route('deploy.run'), [], ['X-Deploy-Token' => 'wrong-token']);

    $response->assertNotFound();
});
