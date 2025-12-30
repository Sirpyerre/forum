<?php

test('returns a successful response', function () {
    $response = $this->get(route('forum.index'));

    $response->assertStatus(200);
});
