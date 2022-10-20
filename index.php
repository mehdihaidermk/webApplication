<?php require 'api/WebApplication.php'; $webApplication = new WebApplication(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Web Application</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>

<div class="container">
    <div class="row" style="margin: 20px 0">
        <div class="col-md-6">
            <form id="addNewCompany">
                <label>Company Name</label>
                <input type="text" class="form-control" name="name" required maxlength="50">
                <label>Address Line 1</label>
                <input type="text" class="form-control" name="addr1" required maxlength="100">
                <label>Address Line 2</label>
                <input type="text" class="form-control" name="addr2" required maxlength="100">
                <label>State</label>
                <input type="text" class="form-control" name="state" required maxlength="20">
                <label>City</label>
                <input type="text" class="form-control" name="city" required maxlength="10">
                <label>Country</label>
                <input type="text" class="form-control" name="country" required maxlength="50">
                <hr>
                <button class="btn btn-success" type="submit">Add</button>
            </form>
        </div>
        <div class="col-md-6">
            <form id="addNewEmployee">
                <label>First Name</label>
                <input type="text" class="form-control" name="fn" required maxlength="20">
                <label>Last Name</label>
                <input type="text" class="form-control" name="ln" required maxlength="20">
                <label>Email</label>
                <input type="email" class="form-control" name="email" required maxlength="50">
                <label>Company</label>
                <select class="form-control" name="comp">
                    <?php foreach ($webApplication->getAllCompanies() as $data): ?>
                    <option value="<?= $webApplication->encryptData($data['id']) ?>"><?= $data['cName'] ?></option>
                    <?php endforeach; ?>
                </select>
                <hr>
                <button class="btn btn-success" type="submit">Add</button>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <table class="table table-bordered table-hover table-responsive table-striped">
                <thead>
                <tr>
                    <th>Company</th>
                    <th>Employees</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($webApplication->getAllCompaniesWithEmployee() as $allCompany) { ?>
                    <tr>
                        <td><?= $allCompany['cName'] ?></td>
                        <td><?= $allCompany['total'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-bordered table-hover table-responsive table-striped">
                <thead>
                <tr>
                    <th>Employee Name</th>
                    <th>Company Name</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($webApplication->getAllEmployeesWithCompanyName() as $allCompany) { ?>
                    <tr>
                        <td><?= $allCompany['eFirstName'].' '.$allCompany['eLastName'] ?></td>
                        <td><?= $allCompany['cName'] ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $('#addNewCompany').on('submit',function (e) {
        e.preventDefault();
        $.ajax({
            url: '<?= $_ENV['URL'] ?>/api/addCompany',
            data: new FormData(e.target),
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(data){
                var response = $.parseJSON(data);
                if(response.status==="success")
                    location.reload();
                else
                    alert(response.message);
            },
        });
    })
    $('#addNewEmployee').on('submit',function (e) {
        e.preventDefault();
        $.ajax({
            url: '<?= $_ENV['URL'] ?>/api/addEmployee',
            data: new FormData(e.target),
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(data){
                var response = $.parseJSON(data);
                if(response.status==="success")
                    location.reload();
                else
                    alert(response.message);
            },
        });
    })

    function deleteEmployee(id){
        var fd = new FormData();
        fd.append('id',id);
        $.ajax({
            url: '<?= $_ENV['URL'] ?>/api/deleteEmployee',
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(data){
                var response = $.parseJSON(data);
                if(response.status==="success")
                    location.reload();
                else
                    alert(response.message);
            },
        });
    }
    function deleteCompany(id){
        var fd = new FormData();
        fd.append('id',id);
        $.ajax({
            url: '<?= $_ENV['URL'] ?>/api/deleteCompany',
            data: fd,
            processData: false,
            contentType: false,
            type: 'POST',
            success: function(data){
                var response = $.parseJSON(data);
                if(response.status==="success")
                    location.reload();
                else
                    alert(response.message);
            },
        });
    }
</script>
</body>
</html>
