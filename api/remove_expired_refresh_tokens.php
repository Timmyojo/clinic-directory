<?php

$refreshTokenModel = new Model\RefreshTokenModel($database);

$deleted_id = $refreshTokenModel->deleteExpired();

echo json_encode(["deleted_id" => $deleted_id]);
die();