<?php

use application\core\ErrorCodes;

header('Content-Type: application/json; charset=UTF-8');

echo json_encode(
  array(
    'error' => ['error_code' => ErrorCodes::TOO_MANY_REQUESTS, 'details' => false],
  )
);
