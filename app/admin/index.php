<?php
require '../../config/config.php';

requireRole('admin');

logActivity(   
    $pdo,
    $_SESSION['user_id'],
    $_SESSION['user_email'],
    'view_activity_logs',
    'success'
);

// Activity Logs Query#3
$stmt = $pdo->query("
    SELECT *
    FROM activity_logs
    ORDER BY activity_log_created_at DESC
");

$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    </head>
    <body>
        <h1>Welcome, Admin</h1> 
        <a href="../../auth/signout.php">Sign Out</a>
        
        <table border="1">
            <thead>
                <tr>
                    <th>Record_ID</th>
                    <th>User ID</th>
                    <th>User Email</th>
                    <th>Status</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                    <th>Date & Time</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($activities as $activity):?>
                    <tr>
                        <td><?=htmlspecialchars($activity['activity_log_id'])?></td>
                        <td><?=htmlspecialchars($activity['user_id'])?></td>
                        <td><?=htmlspecialchars($activity['user_email'])?></td>
                        <td><?=htmlspecialchars($activity['activity_log_action'])?></td>
                        <td><?=htmlspecialchars($activity['activity_log_status'])?></td>
                        <td><?=htmlspecialchars($activity['activity_log_ip_address'])?></td>
                        <td><?=htmlspecialchars($activity['activity_log_user_agent'])?></td>
                        <td><?=htmlspecialchars($activity['activity_log_created_at'])?></td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script>
    // Function to refresh the page every 5 seconds
    new DataTable('#example', {
        scrollY: '400px',
        autoWidth: false,
    })
</script>
</html>