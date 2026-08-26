<?php
require_once 'config/config.php';

$user_id = "root" ?? null;
$user_email = "root" ?? null;

$buttons = [
    'Login',
    "Logout",
    'Create record',
    'Update record',
    'Delete record',
    'View record',
    'Upload file', 
    'Download',
    'Search',
    'Generate report'
];
?>
<table border = "1" cellpadding = "10">
    <tr>
        <th>Action</th>
        <th>Test</th>
    </tr>

    <?php foreach ($buttons as $button): ?>
        <tr>
            <td><?= htmlspecialchars($button) ?></td>
            <td>
                <form method = "post">
                <input type = "hidden" name = "action"
                 value = "<?= htmlspecialchars($button) ?>"
                 >
                 <button type = "submit">Test</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
