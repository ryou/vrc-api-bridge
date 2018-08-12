<?php

setcookie("name", "value", time() + 10000);

header("Access-Control-Allow-Origin: http://localhost:8000");
header("Access-Control-Allow-Credentials: true");
