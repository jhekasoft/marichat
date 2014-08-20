<?php
session_start();

//$_SESSION['shopping-list'] = array();

$_SESSION['shopping-list'][] = array(
    'title' => $_POST['title'],
);