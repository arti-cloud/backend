<?php
require_once ('config.php');

class UserController {
    private $connection;

    public function __construct($connection){
        $this->connection = $connection;
    }

    public function getUsers(){
        $result = $this->connection->query("select id, name , email , dob from users");
        $users =[];
        while($row = $result->fetch_assoc()){
            $users[] = $row;
        }
        echo json_encode($users);
    }

    public function getUser($id){
        $searchUser = $this->connection->prepare("select id, name,email,dob from users where id = ?");
        $searchUser->bind_param("i" , $id);
        $searchUser->execute();
        $result = $searchUser->get_result();
        echo json_encode($result->fetch_assoc());
    }

    public function createUser($data){
        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $dob = $data['dob'];

        $create = $this->connection->prepare("insert into users (name,email,password,dob) values(?, ?, ?, ?)");
        $create->bind_param('ssss', $name, $email, $password, $dob);
        if($create->execute()){
            echo json_encode(['success' => true, 'id' => $this->connection->insert_id]);
        }
        else{
            echo json_encode(['success' => false, 'error'=> $this->connection->error]);
        }
    }

    public function updateUser($id, $data)
    {
        $stmt = $this->connection->prepare("UPDATE users SET name=?, email=?, password=?, dob=? WHERE id=?");
        $stmt->bind_param("ssssi", $data['name'], $data['email'], $data['password'], $data['dob'], $id);

        try {
            $stmt->execute();
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
            } else {
                echo json_encode(['error' => 'User not found or no changes made']);
            }
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) { 
                http_response_code(409);
                echo json_encode(['error' => 'Email already exists']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
            }
        }
    }


    public function deleteUser($id){
        $deleteUser = $this->connection->prepare("delete from users where id = ?");
        $deleteUser->bind_param('i', $id);
        if($deleteUser->execute()){
            echo json_encode(['success'=> true]);
        }else{
            echo json_encode(['success'=> false, 'error'=>$deleteUser->error]);
        }
    }
}

?>