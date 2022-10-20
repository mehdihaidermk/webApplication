<?php
require 'WebApplication.php';
echo (new WebApplication())->addEmployee(
  $_POST['fn'],
  $_POST['ln'],
  $_POST['email'],
  $_POST['comp']
);