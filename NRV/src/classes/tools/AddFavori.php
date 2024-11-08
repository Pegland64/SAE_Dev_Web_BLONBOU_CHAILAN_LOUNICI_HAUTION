<?php

namespace nrv\net\tools;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['spectacle_id'])) {

    $spectacleId = intval($_POST['spectacle_id']);
    setcookie("spectacle_id_$spectacleId", $spectacleId, time() + (7 * 24 * 60 * 60), "/");
}
?>