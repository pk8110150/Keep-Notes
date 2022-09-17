<?php

require 'session.php';

session_destroy();

setcookie('USER_ID',$user_ID,time()-3600,"/");

header('Location: index.php');
?>