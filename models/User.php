<?php
class User {
    private $conn;      // Conexão com o banco de dados
    private $table = 'users';       // Nome da tabela de usuários no banco de dados

    // Propriedades do usuário
    public $id;     // ID do usuário
    public $name;       // Nome do usuário
    public $password;       // Senha do usuário

    public function __construct($db) {      // Construtor: recebe a conexão com o banco de dados
        $this-> conn = $db;
    }

    // Método para criar um novo usuário
    public function create() {        
        $query = "INSERT INTO " . $this-> table . " (name, password) VALUES (:name, :password)";     // Query SQL para inserir um novo usuário na tabela
        $stmt = $this-> conn-> prepare($query);       // Prepara a query para execução

        $this-> name = htmlspecialchars(strip_tags($this-> name));        // Remove tags HTML e caracteres especiais
        $this-> password = password_hash($this-> password, PASSWORD_BCRYPT);      // Cria um hash da senha

        $stmt-> bindParam(':name', $this-> name);     // Vincula o nome ao parâmetro :name
        $stmt-> bindParam(':password', $this-> password);     // Vincula a senha ao parâmetro :password

        if ($stmt-> execute()) {     // Executa a query e retorna true se for bem-sucedida
            return true;
        }
        return false;       // Retorna false em caso de falha
    }

    public function userExists() {      // Verificando se usuário já exite
        $query = "SELECT id FROM " . $this-> table . " WHERE name = :name";
        $stmt = $this-> conn-> prepare($query);
        $stmt-> bindParam(':name', $this-> name);
        $stmt-> execute();

        return $stmt-> rowCount() > 0;
    }

    public function login() {       // Método para autenticar o usuário        
        $query = "SELECT id, password FROM " . $this-> table . " WHERE name = :name";
        $stmt = $this-> conn-> prepare($query);
        $stmt-> bindParam(':name', $this-> name);
        $stmt-> execute();

        if ($stmt-> rowCount() > 0) {        // Verifica se usuário foi encontrado
            $row = $stmt-> fetch(PDO::FETCH_ASSOC);
            if (password_verify($this-> password, $row['password'])) {
                return $row['id'];
            }
        }
        return false;
    }

    public function update() {      // Editar usuário
        $query = "UPDATE " . $this-> table . " SET name = :name WHERE id = :id";
        $stmt = $this-> conn-> prepare($query);

        $this-> name = htmlspecialchars(strip_tags($this-> name));        // Remove tags HTML e caracteres especiais
        $this-> id = htmlspecialchars(strip_tags($this-> id));        // Remove tags HTML e caracteres especiais

        $stmt-> bindParam(':name', $this-> name);     // Vincula o nome ao parâmetro
        $stmt-> bindParam(':id', $this-> id);     // Vincula o ID ao parâmetro

        return $stmt-> execute();
    }

    public function delete() {    // Método para remover um usuário        
        $query = "DELETE FROM " . $this-> table . " WHERE id = :id";
        $stmt = $this-> conn-> prepare($query);
        $stmt-> bindParam(':id', $this-> id);

        return $stmt-> execute();
    }
}