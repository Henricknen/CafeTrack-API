<?php
class User {
    private $conn; // Conexão com o banco de dados
    private $table = 'users'; // Nome da tabela de usuários no banco de dados

    // Propriedades do usuário
    public $id; // ID do usuário
    public $name; // Nome do usuário
    public $password; // Senha do usuário

    // Construtor: recebe a conexão com o banco de dados
    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para criar um novo usuário
    public function create() {
        // Query SQL para inserir um novo usuário na tabela
        $query = "INSERT INTO " . $this->table . " (name, password) VALUES (:name, :password)";
        $stmt = $this->conn->prepare($query); // Prepara a query para execução

        // Limpa e vincula os parâmetros
        $this->name = htmlspecialchars(strip_tags($this->name)); // Remove tags HTML e caracteres especiais
        $this->password = password_hash($this->password, PASSWORD_BCRYPT); // Cria um hash da senha

        $stmt->bindParam(':name', $this->name); // Vincula o nome ao parâmetro :name
        $stmt->bindParam(':password', $this->password); // Vincula a senha ao parâmetro :password

        // Executa a query e retorna true se for bem-sucedida
        if ($stmt->execute()) {
            return true;
        }
        return false; // Retorna false em caso de falha
    }

    // Método para verificar se um usuário já existe
    public function userExists() {
        // Query SQL para verificar se o nome do usuário já está cadastrado
        $query = "SELECT id FROM " . $this->table . " WHERE name = :name";
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->bindParam(':name', $this->name); // Vincula o nome ao parâmetro :name
        $stmt->execute(); // Executa a query

        // Retorna true se o usuário já existir
        return $stmt->rowCount() > 0;
    }

    // Método para autenticar o usuário (login)
    public function login() {
        // Query SQL para buscar o usuário pelo nome
        $query = "SELECT id, password FROM " . $this->table . " WHERE name = :name";
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->bindParam(':name', $this->name); // Vincula o nome ao parâmetro :name
        $stmt->execute(); // Executa a query

        // Verifica se o usuário foi encontrado
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC); // Obtém os dados do usuário
            // Verifica se a senha está correta
            if (password_verify($this->password, $row['password'])) {
                return $row['id']; // Retorna o ID do usuário se a senha estiver correta
            }
        }
        return false; // Retorna false se o usuário não existir ou a senha estiver incorreta
    }

    // Método para editar um usuário
    public function update() {
        // Query SQL para atualizar o nome do usuário
        $query = "UPDATE " . $this->table . " SET name = :name WHERE id = :id";
        $stmt = $this->conn->prepare($query); // Prepara a query

        // Limpa e vincula os parâmetros
        $this->name = htmlspecialchars(strip_tags($this->name)); // Remove tags HTML e caracteres especiais
        $this->id = htmlspecialchars(strip_tags($this->id)); // Remove tags HTML e caracteres especiais

        $stmt->bindParam(':name', $this->name); // Vincula o nome ao parâmetro :name
        $stmt->bindParam(':id', $this->id); // Vincula o ID ao parâmetro :id

        // Executa a query e retorna true se for bem-sucedida
        return $stmt->execute();
    }

    // Método para remover um usuário
    public function delete() {
        // Query SQL para deletar um usuário pelo ID
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->bindParam(':id', $this->id); // Vincula o ID ao parâmetro :id

        // Executa a query e retorna true se for bem-sucedida
        return $stmt->execute();
    }
}