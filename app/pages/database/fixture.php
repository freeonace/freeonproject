<?php // app/pages/database/fixture.php
require_once __DIR__ . '/../../data/fixtures.php';
require __DIR__ . '/fixture/' . ($args ? 'detail' : 'list') . '.php';