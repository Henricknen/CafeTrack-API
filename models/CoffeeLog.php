<?php
class CoffeeLog {
    private $conn; 
    private $table = 'coffee_logs';

    // Propriedades do registro de café
    public $id; // ID do registro
    public $user_id; // ID do usuário que tomou café
    public $timestamp; // Data e hora do registro

    public function __construct($db) {
        $this->conn = $db;
    }

    // Método para registrar que um usuário tomou café
    public function logCoffee() {
        // Query SQL para inserir um registro de café
        $query = "INSERT INTO " . $this->table . " (user_id) VALUES (:user_id)";
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->bindParam(':user_id', $this->user_id); // Vincula o ID do usuário ao parâmetro :user_id

        // Executa a query e retorna true se for bem-sucedida
        return $stmt->execute();
    }

    // Método para contar quantas vezes um usuário tomou café
    public function getCoffeeCount($user_id) {
        // Query SQL para contar os registros de café de um usuário
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->bindParam(':user_id', $user_id); // Vincula o ID do usuário ao parâmetro :user_id
        $stmt->execute(); // Executa a query

        // Retorna o número de registros
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Método para obter o histórico de café de um usuário por dia
    public function getDailyHistory($user_id) {
        // Query SQL para agrupar os registros de café por dia
        $query = "SELECT DATE(timestamp) as date, COUNT(*) as count
                  FROM " . $this->table . "
                  WHERE user_id = :user_id
                  GROUP BY DATE(timestamp)";
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->bindParam(':user_id', $user_id); // Vincula o ID do usuário ao parâmetro :user_id
        $stmt->execute(); // Executa a query

        // Retorna os resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obter o ranking de usuários que mais tomaram café em um dia específico
    public function getDailyRanking($date) {
        // Query SQL para contar os registros de café por usuário em um dia específico
        $query = "SELECT u.name, COUNT(*) as count
                  FROM " . $this->table . " cl
                  JOIN users u ON cl.user_id = u.id
                  WHERE DATE(cl.timestamp) = :date
                  GROUP BY cl.user_id
                  ORDER BY count DESC";
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->bindParam(':date', $date); // Vincula a data ao parâmetro :date
        $stmt->execute(); // Executa a query

        // Retorna os resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obter o ranking de usuários que mais tomaram café nos últimos X dias
    public function getRankingLastDays($days) {
        // Query SQL para contar os registros de café por usuário nos últimos X dias
        $query = "SELECT u.name, COUNT(*) as count
                  FROM " . $this->table . " cl
                  JOIN users u ON cl.user_id = u.id
                  WHERE cl.timestamp >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                  GROUP BY cl.user_id
                  ORDER BY count DESC";
        $stmt = $this->conn->prepare($query); // Prepara a query
        $stmt->bindParam(':days', $days, PDO::PARAM_INT); // Vincula o número de dias ao parâmetro :days
        $stmt->execute(); // Executa a query

        // Retorna os resultados
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}