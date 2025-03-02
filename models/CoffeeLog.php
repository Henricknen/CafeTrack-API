<?php
class CoffeeLog {
    private $conn; 
    private $table = 'coffee_logs';

    public $id;
    public $user_id;
    public $timestamp;

    public function __construct($db) {
        $this-> conn = $db;
    }

    public function logCoffee() {
        $query = "INSERT INTO " . $this-> table . " (user_id) VALUES (:user_id)";
        $stmt = $this-> conn-> prepare($query);
        $stmt-> bindParam(':user_id', $this-> user_id);

        return $stmt-> execute();
    }

    public function getCoffeeCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this-> table . " WHERE user_id = :user_id";
        $stmt = $this-> conn-> prepare($query);
        $stmt-> bindParam(':user_id', $user_id);
        $stmt-> execute();

        $row = $stmt-> fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    public function getDailyHistory($user_id) {
        $query = "SELECT DATE(timestamp) as date, COUNT(*) as count
                  FROM " . $this-> table . "
                  WHERE user_id = :user_id
                        GROUP BY DATE(timestamp)";
        $stmt = $this-> conn-> prepare($query);
        $stmt-> bindParam(':user_id', $user_id);
        $stmt-> execute();

        return $stmt-> fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDailyRanking($date) {
        $query = "SELECT u.name, COUNT(*) as count
                  FROM " . $this-> table . " cl
                  JOIN users u ON cl.user_id = u.id
                  WHERE DATE(cl.timestamp) = :date
                  GROUP BY cl.user_id
                  ORDER BY count DESC";
        $stmt = $this-> conn-> prepare($query);
        $stmt-> bindParam(':date', $date);
        $stmt-> execute();

        return $stmt-> fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRankingLastDays($days) {
        $query = "SELECT u.name, COUNT(*) as count
                  FROM " . $this-> table . " cl
                  JOIN users u ON cl.user_id = u.id
                  WHERE cl.timestamp >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
                  GROUP BY cl.user_id
                  ORDER BY count DESC";
        $stmt = $this-> conn-> prepare($query);
        $stmt-> bindParam(':days', $days, PDO::PARAM_INT);
        $stmt-> execute();

        return $stmt-> fetchAll(PDO::FETCH_ASSOC);
    }
}