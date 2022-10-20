<?php
require 'WebApplication.php';
(new WebApplication())->deleteEmployee($_POST['id']);