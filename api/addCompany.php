<?php
require 'WebApplication.php';
echo (new WebApplication())->addCompany(
  $_POST['name'],
  $_POST['addr1'],
  $_POST['addr2'],
  $_POST['state'],
  $_POST['city'],
  $_POST['country']
);