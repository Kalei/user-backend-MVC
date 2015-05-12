<?php

if (isset($_SESSION['id_user'])) {
     include 'view/info-compte.php';
} else {
     include 'view/identification.php';
}