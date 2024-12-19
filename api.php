<?php

date_default_timezone_set('Asia/Taipei');
header('Content-Type: text/plain');

$db = new SQLite3('db.sqlite3');
$db->exec('CREATE TABLE IF NOT EXISTS data (id INTEGER PRIMARY KEY, date DATE, name TEXT, value FLOAT)');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    $result = $db->query('SELECT * FROM data');
    $json = [];
    while ($row = $result->fetchArray()) {
        $name = $row['name'];
        $date = $row['date'];
        $value = $row['value'];
        $json[$name][] = [
            'date' => $date,
            'value' => $value,
        ];
    }
    die(json_encode($json));
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        ($name = @$_POST['name']) &&
        ($value = @$_POST['value']) &&
        ('20201028' === @$_POST['password'])
    ) {
        $statement = $db->prepare('INSERT INTO data VALUES (NULL, :date, :name, :value)');
        $statement->bindValue(':date', date('Y-m-d H:i:s'), SQLITE3_TEXT);
        $statement->bindValue(':name', $name, SQLITE3_TEXT);
        $statement->bindValue(':value', $value, SQLITE3_FLOAT);
        $statement->execute();
        header('Location: .');
        echo "Success\n";
    } else {
        echo "Error\n";
    }
}
