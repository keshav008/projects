<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP - Simple To Do List App</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Include Bootstrap for styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>

    <div class="container mt-5" style='max-width:580px;'>
    <div style='padding:5px;background:#F5F5F5;border:2px solid #E3E3E3;border-radius:4px;'>
        <h5 class="text-left" style='color:blue; margin-top:20px;'>PHP - Simple To Do List App</h5>
        <hr style='border:none; height:2px; background:#E3E3E3;'>

        <!-- Input for new task -->
        <div class="input-group mb-3" style='max-width:290px;margin: 16px 0px 25px 110px !important;'>
            <input type="text" class="form-control" id="taskName" placeholder="Add a new task">
            <div class="input-group-append" style='margin-left:5px;'>
                <button class="btn btn-primary" type="button" id="addTask">Add Task</button>
            </div>
        </div>

        <!-- Task List -->
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Task</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="taskList">
                <!-- Existing tasks will be populated here -->
                @foreach ($tasks as $task)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $task->name }}</td>
                    <td>{{ $task->status ? 'Done' : 'Pending' }}</td>
                    <td>
                        <!-- Edit and Delete buttons -->
                        <button class="btn btn-success btn-sm editTask" data-id="{{ $task->id }}">
                            <i class="fas fa-check"></i> <!-- Font Awesome check icon -->
                        </button>
                        <button class="btn btn-danger btn-sm deleteTask" data-id="{{ $task->id }}">
                            <i class="fas fa-times"></i> <!-- Font Awesome cross icon -->
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-info mt-3" id="showAllTasks">Show All Tasks</button>
        </div>
    </div>
    <script>
    $(document).ready(function () {
        
        // CSRF Token setup for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Add Task functionality
        $('#addTask').on('click', function () {
            let taskName = $('#taskName').val();
            if (taskName.trim()) {
                $.post('/tasks', { name: taskName }, function (response) {
                    location.reload(); // Reload the page to reflect the new task
                }).fail(function (error) {
                    alert(error.responseJSON.message);
                });
            }
        });

        // Mark Task as Complete/Undo
        $('body').on('click', '.editTask', function () {  // Use event delegation
            let taskId = $(this).data('id');
            $.ajax({
                url: `/tasks/${taskId}`,
                type: 'PATCH',
                success: function (response) {
                    location.reload(); // Reload the page to update the task status
                }
            });
        });

        // Delete Task functionality
        $('body').on('click', '.deleteTask', function () {  // Use event delegation
            let taskId = $(this).data('id');
            if (confirm('Are you sure you want to delete this task?')) {
                $.ajax({
                    url: `/tasks/${taskId}`,
                    type: 'DELETE',
                    success: function (response) {
                        location.reload(); // Reload the page to remove the task
                    }
                });
            }
        });

        // Show all tasks
        $('#showAllTasks').on('click', function () {
            $.get('/tasks/show-all', function (tasks) {
                $('#taskList').empty();
                tasks.forEach((task, index) => {
                    $('#taskList').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${task.name}</td>
                            <td>${task.status ? 'Done' : 'Pending'}</td>
                            <td>
                                <button class="btn btn-success btn-sm editTask" data-id="${task.id}">
                                    <i class="fas fa-check"></i> <!-- Font Awesome check icon -->
                                </button>
                                <button class="btn btn-danger btn-sm deleteTask" data-id="${task.id}">
                                    <i class="fas fa-times"></i> <!-- Font Awesome cross icon -->
                                </button>
                            </td>
                        </tr>
                    `);
                });
            });
        });
    });
    </script>
</body>
</html>
