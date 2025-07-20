<?php
require_once ('config.php');

class UserController {
    private $connection;

    public function __construct($connection){
        $this->connection = $connection;
    }

    // get all users
    public function getUsers(){
        $result = $this->connection->query("select id, name , email , dob from users");
        $users =[];
        while($row = $result->fetch_assoc()){
            $users[] = $row;
        }
        echo json_encode($users);
    }

    //get particular user by id
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

    public function updateUser($id, $data){
        $name = $data['name'];
        $email = $data['email'];
        $dob = $data['dob'];
        $update = $this->connection->prepare("update users set name=  ?, email = ?, dob = ? where id= ?");
        $update->bind_param('sssi' , $name, $email, $dob, $id);
        if($update->execute){
            echo json_encode(['success'=>true]);
        }else{
            echo json_encode(['success'=>false, 'error'=>$update->error]);
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