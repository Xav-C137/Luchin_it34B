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

// User Sessions Query #4
$stmt = $pdo->query("
    SELECT
        s.session_id,
        s.user_id,
        u.user_username,
        u.user_email,
        u.user_role,
        s.session_start,
        s.session_end,
        COALESCE(
            s.session_duration,
            TIMESTAMPDIFF(SECOND, s.session_start, NOW())
        ) AS duration_seconds
    FROM user_sessions s
    LEFT JOIN users u ON u.user_id = s.user_id
    ORDER BY s.session_start DESC
    LIMIT 500
");

$sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatDuration($seconds): string {
    $seconds = (int) $seconds;
    return sprintf('%02d:%02d:%02d',
        intdiv($seconds, 3600),
        intdiv($seconds % 3600, 60),
        $seconds % 60
    );
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
</head>
<body class="container py-4">
 
    <h1>Welcome, Admin</h1>
    <a href="../../auth/signout.php">Sign Out</a>
 
    <ul class="nav nav-tabs my-3" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-logs" type="button">Activity logs</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-sessions" type="button">User sessions</button>
        </li>
    </ul>
 
    <div class="tab-content">
 
        <!-- Activity logs -->
        <div class="tab-pane fade show active" id="tab-logs">
            <div class="table-responsive">
                <table id="logsTable" class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Record ID</th>
                            <th>User ID</th>
                            <th>User Email</th>
                            <th>Action</th>
                            <th>Status</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Date &amp; Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($activities as $activity): ?>
                        <tr>
                            <td><?= htmlspecialchars($activity['activity_log_id']) ?></td>
                            <td><?= htmlspecialchars($activity['user_id'] ?? '') ?></td>
                            <td><?= htmlspecialchars($activity['user_email'] ?? '') ?></td>
                            <td><?= htmlspecialchars($activity['activity_log_action']) ?></td>
                            <td><?= htmlspecialchars($activity['activity_log_status']) ?></td>
                            <td><?= htmlspecialchars($activity['activity_log_ip_address'] ?? '') ?></td>
                            <td><?= htmlspecialchars($activity['activity_log_user_agent'] ?? '') ?></td>
                            <td><?= htmlspecialchars($activity['activity_log_created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
 
        <!-- User sessions -->
        <div class="tab-pane fade" id="tab-sessions">
            <div class="table-responsive">
                <table id="sessionsTable" class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Started</th>
                            <th>Ended</th>
                            <th>Duration</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sessions as $s): ?>
                        <?php $open = $s['session_end'] === null; ?>
                        <tr>
                            <td><?= htmlspecialchars($s['session_id']) ?></td>
                            <td>
                                <?= htmlspecialchars($s['user_username'] ?? 'Deleted user') ?><br>
                                <small class="text-muted"><?= htmlspecialchars($s['user_email'] ?? '') ?></small>
                            </td>
                            <td><?= htmlspecialchars($s['user_role'] ?? '') ?></td>
                            <td><?= htmlspecialchars($s['session_start']) ?></td>
                            <td><?= $open ? '—' : htmlspecialchars($s['session_end']) ?></td>
                            <td><?= formatDuration($s['duration_seconds']) ?></td>
                            <td>
                                <span class="badge <?= $open ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                    <?= $open ? 'Active' : 'Ended' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
 
    </div>
 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
    <script>
        const logsTable     = new DataTable('#logsTable',     { pageLength: 25, order: [] });
        const sessionsTable = new DataTable('#sessionsTable', { pageLength: 25, order: [] });
 
        // Re-fit columns when a tab becomes visible
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn => {
            btn.addEventListener('shown.bs.tab', () => {
                logsTable.columns.adjust();
                sessionsTable.columns.adjust();
            });
        });
    </script>
</body>
</html>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-logs">
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
            </div>

            <div class="tab-pane fade" id="tab-sessions">
            <table id="sessionsTable" class="table table-striped">
          <thead>
              <tr>
                  <th>Session ID</th><th>User</th><th>Role</th>
                  <th>Started</th><th>Ended</th><th>Duration</th><th>Status</th>
              </tr>
          </thead>
          <tbody>
          <?php foreach ($sessions as $s): ?>
              <?php $open = $s['session_end'] === null; ?>
              <tr>
                  <td><?= htmlspecialchars($s['session_id']) ?></td>
                  <td><?= htmlspecialchars($s['user_username'] ?? 'Deleted user') ?><br>
                      <small class="text-muted"><?= htmlspecialchars($s['user_email'] ?? '') ?></small></td>
                  <td><?= htmlspecialchars($s['user_role'] ?? '') ?></td>
                  <td><?= htmlspecialchars($s['session_start']) ?></td>
                  <td><?= $open ? '—' : htmlspecialchars($s['session_end']) ?></td>
                  <td><?= gmdate('H:i:s', (int) $s['duration_seconds']) ?></td>
                  <td><span class="badge <?= $open ? 'text-bg-success' : 'text-bg-secondary' ?>">
                      <?= $open ? 'Active' : 'Ended' ?></span></td>
              </tr>
          <?php endforeach; ?>
          </tbody>
      </table>
  </div>
</div>
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
</html> -->