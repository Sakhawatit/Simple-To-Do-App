<?php

// Define a constant for the tasks file (tasks.json)
define("TASKS_FILE", "tasks.json");

// Function to load tasks from the tasks.json file
// Reads the JSON file and returns the decoded array
function loadTasks(): array {
    if (!file_exists(TASKS_FILE)) {
        return []; // Return an empty array if the file does not exist
    }

    $data = file_get_contents(TASKS_FILE); // Read the file contents

    return $data ? json_decode($data, true) : []; // Decode JSON or return an empty array
}

// Load tasks from the tasks.json file
$tasks = loadTasks();

// Function to save tasks to the tasks.json file
// Takes an array of tasks and saves it back to the JSON file
function saveTasks(array $tasks): void {
    file_put_contents(TASKS_FILE, json_encode($tasks, JSON_PRETTY_PRINT)); // Write tasks to file in JSON format
}

// Check if the form has been submitted using a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle task addition (check if task input is provided and valid, then add it to the array and save)
    if (isset($_POST['task']) && !empty(trim($_POST['task']))) {
        $tasks[] = [
            'task' => htmlspecialchars(trim($_POST['task'])), // Sanitize task input
            'done' => false // Set initial status as not done
        ];
        saveTasks($tasks); // Save updated tasks
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
        exit;
    } elseif (isset($_POST['delete'])) {
        // Handle task deletion (remove the specified task from the array and save)
        unset($tasks[$_POST['delete']]); // Remove task by index
        $tasks = array_values($tasks); // Reindex array to maintain order
        saveTasks($tasks); // Save updated tasks
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
        exit;
    } elseif (isset($_POST['toggle'])) {
        // Handle task completion toggle (update the task's status and save)
        $tasks[$_POST['toggle']]['done'] = !$tasks[$_POST['toggle']]['done']; // Toggle done status
        saveTasks($tasks); // Save updated tasks
        header('Location: ' . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
        exit;
    }
}

?>

<!-- UI -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do App</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/milligram/1.4.1/milligram.min.css">
    <style>
        body {
            margin-top: 20px;
        }
        .task-card {
            border: 1px solid #ececec; 
            padding: 20px;
            border-radius: 5px;
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); 
        }
        .task {
            color: #888;
        }
        .task-done {
            text-decoration: line-through;
            color: #888;
        }
        .task-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        ul {
            padding-left: 20px;
        }
        button {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="task-card">
            <h1>To-Do App</h1>

            <!-- Form for adding new tasks -->
            <form method="POST">
                <div class="row">
                    <div class="column column-75">
                        <input type="text" name="task" placeholder="Enter a new task" required>
                    </div>
                    <div class="column column-25">
                        <button type="submit" class="button-primary">Add Task</button>
                    </div>
                </div>
            </form>

            <!-- Task list section -->
            <h2>Task List</h2>
            <ul style="list-style: none; padding: 0;">
                <!-- Check if there are any tasks; if not, display a message -->
                <?php if (empty($tasks)): ?>
                    <li>No tasks yet. Add one above!</li>
                <?php else: ?>
                    <!-- Loop through tasks and display each with toggle and delete options -->
                    <?php foreach ($tasks as $index => $task): ?>
                        <li class="task-item">
                            <!-- Form for toggling task completion -->
                            <form method="POST" style="flex-grow: 1;">
                                <input type="hidden" name="toggle" value="<?= $index ?>">
                                <button type="submit" style="border: none; background: none; cursor: pointer; text-align: left; width: 100%;">
                                    <span class="task <?= $task['done'] ? 'task-done' : '' ?>">
                                        <?= htmlspecialchars($task['task']) ?>
                                    </span>
                                </button>
                            </form>

                            <!-- Form for deleting a task -->
                            <form method="POST">
                                <input type="hidden" name="delete" value="<?= $index ?>">
                                <button type="submit" class="button button-outline" style="margin-left: 10px;">Delete</button>
                            </form>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>

        </div>
    </div>
</body>
</html>
