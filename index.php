<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Include Composer autoload file

// Connect to MongoDB
try {
    $client = new MongoDB\Client("mongodb://localhost:27017");

    // Select a database and collection
    $db = $client->todo_db;
    $collection = $db->tasks;

    // Handle form submissions
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $task = ['task' => $_POST['task'], 'status' => 'incomplete'];
                    $collection->insertOne($task);
                    break;
                case 'update':
                    $id = new MongoDB\BSON\ObjectId($_POST['id']);
                    $collection->updateOne(
                        ['_id' => $id],
                        ['$set' => ['task' => $_POST['task'], 'status' => $_POST['status']]]
                    );
                    break;
                case 'delete':
                    $id = new MongoDB\BSON\ObjectId($_POST['id']);
                    $collection->deleteOne(['_id' => $id]);
                    break;
            }
        }
    }

    // Fetch all tasks
    $tasks = $collection->find();

} catch (MongoDB\Driver\Exception\Exception $e) {
    die("Failed to connect to MongoDB: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ToDo App</title>
</head>
<body>
    <h1>ToDo App</h1>

    <form method="post" action="">
        <input type="hidden" name="action" value="add">
        <input type="text" name="task" placeholder="New Task">
        <button type="submit">Add Task</button>
    </form>

    <h2>Tasks</h2>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <form method="post" action="" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $task['_id']; ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="text" name="task" value="<?php echo htmlspecialchars($task['task'], ENT_QUOTES); ?>">
                    <select name="status">
                        <option value="incomplete" <?php echo $task['status'] == 'incomplete' ? 'selected' : ''; ?>>Incomplete</option>
                        <option value="complete" <?php echo $task['status'] == 'complete' ? 'selected' : ''; ?>>Complete</option>
                    </select>
                    <button type="submit">Update</button>
                </form>
                <form method="post" action="" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $task['_id']; ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit">Delete</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
