<?php
require str_replace('api','',__DIR__).'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class WebApplication
{
    private string $tbl_company = "tbl_companies";
    private string $tbl_employee = "tbl_employees";

    public function __construct()
    {
        $this->db = new PDO("mysql:host=localhost;dbname=" . $_ENV['DB_NAME'], $_ENV['DB_USER_NAME'], $_ENV['DB_PASSWORD']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->key = 'bRuD5WYw5wd0rdHR9yLlM6wt2vteuiniQBqE70nAuhU=';
        $this->cipher = "aes-128-gcm";
        $this->iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($this->cipher));
    }
    public function encryptData($data,$encryptOrDecrypt=true): bool|string
    {
        $data = str_replace('.php','',$data);
        $encryption_key = base64_decode($this->key);
        if($encryptOrDecrypt) {
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
            return base64_encode($encrypted . '::' . $iv);
        }else{
            list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);
            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
        }
    }

    public function successResponseFunction(string $returnMessage, array $returnData = []): bool|string
    {
        return json_encode(array("status" => "success", "message" => $returnMessage, "data" => $returnData));
    }

    public function failedResponseFunction(string $returnMessage): bool|string
    {
        return json_encode(array("status" => "failed", "message" => $returnMessage));
    }

    public function addCompany(string $companyName,string $addressLine1,string $addressLine2,string $state,string $city,string $country): bool|string
    {
        $query = $this->db->prepare("insert into " . $this->tbl_company . " (cName,cAddress1,cAddress2,state,city,country) VALUES(:cName,:cAddress1,:cAddress2,:state,:city,:country)");
        $query->bindParam(":cName", $companyName);
        $query->bindParam(":cAddress1", $addressLine1);
        $query->bindParam(":cAddress2", $addressLine2);
        $query->bindParam(":state", $state);
        $query->bindParam(":city", $city);
        $query->bindParam(":country", $country);
        if ($query->execute()) return $this->successResponseFunction('Company Added');
        return $this->failedResponseFunction('Something went wrong');
    }

    public function addEmployee(string $firstName,string $lastName,string $email,string $companyId): bool|string
    {
        $companyId = $this->encryptData($companyId,false);
        $query = $this->db->prepare("insert into " . $this->tbl_employee . " (eFirstName,eLastName,eEmailAddress,eCompany) VALUES(:eFirstName,:eLastName,:eEmailAddress,:eCompany)");
        $query->bindParam(":eFirstName", $firstName);
        $query->bindParam(":eLastName", $lastName);
        $query->bindParam(":eEmailAddress", $email);
        $query->bindParam(":eCompany", $companyId);
        if ($query->execute()) return $this->successResponseFunction('Employee Added');
        return $this->failedResponseFunction('Something went wrong');
    }

    public function updateCompany(string $companyName,string $addressLine1,string $addressLine2,string $state,string $city,string $country,string $id): bool|string
    {
        $id = $this->encryptData($id,false);
        $query = $this->db->prepare("update " . $this->tbl_company . " set cName=:cName,cAddress1=:cAddress1,cAddress2=:cAddress2,state=:state,city=:city,country=:country where id=:id");
        $query->bindParam(":cName", $companyName);
        $query->bindParam(":cAddress1", $addressLine1);
        $query->bindParam(":cAddress2", $addressLine2);
        $query->bindParam(":state", $state);
        $query->bindParam(":city", $city);
        $query->bindParam(":country", $country);
        $query->bindParam(":id", $id);
        if ($query->execute()) return $this->successResponseFunction('Company Added');
        return $this->failedResponseFunction('Something went wrong');
    }

    public function updateEmployee(string $firstName,string $lastName,string $email,string $companyId,string $id): bool|string
    {
        $id = $this->encryptData($id,false);
        $query = $this->db->prepare("update " . $this->tbl_employee . " set eFirstName=:eFirstName,eLastName=:eLastName,eEmailAddress=:eEmailAddress,eCompany=:eCompany where id=:id");
        $query->bindParam(":eFirstName", $firstName);
        $query->bindParam(":eLastName", $lastName);
        $query->bindParam(":eEmailAddress", $email);
        $query->bindParam(":eCompany", $companyId);
        $query->bindParam(":id", $id);
        if ($query->execute()) return $this->successResponseFunction('Employee Added');
        return $this->failedResponseFunction('Something went wrong');
    }

    public function deleteCompany(string $id): bool|string
    {
        $id = $this->encryptData($id,false);
        $this->db->query("delete from " . $this->tbl_company . " where id=" . $id);
        return $this->successResponseFunction('Company Deleted');
    }

    public function deleteEmployee(string $id): bool|string
    {
        $id = $this->encryptData($id,false);
        $this->db->query("delete from " . $this->tbl_employee . " where id=" . $id);
        return $this->successResponseFunction('Employee Deleted');
    }

    public function getAllCompanies(): bool|array{
        return $this->db->query("select * from ".$this->tbl_company)->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllCompaniesWithEmployee(): bool|array
    {
        return $this->db->query("SELECT cName,(SELECT COUNT(*) from tbl_employees where eCompany=tbl_companies.id) as total FROM `tbl_companies`")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllEmployeesWithCompanyName(): bool|array
    {
        return $this->db->query("SELECT eFirstName,eLastName,cName FROM ".$this->tbl_employee." inner join ".$this->tbl_company." on ".$this->tbl_company.".id=eCompany order by eCompany asc")->fetchAll(PDO::FETCH_ASSOC);
    }
}