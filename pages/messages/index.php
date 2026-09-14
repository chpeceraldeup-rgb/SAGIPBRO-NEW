<?php
require_once '../../includes/auth_check.php';
requireRole(['admin', 'official']);
require_once '../../config/database.php';
$conn = sagipbroDatabase();

$pageTitle = 'Messages';
$pageDescription = 'Review messages sent through the public contact form.';
$basePath = '../../';
$isAdmin = true;
$activeAdmin = 'messages';
$messages = [];
$messagesLoaded = false;
try {
    $messages = $conn->query('SELECT id, name, email, phone, sitio, subject, message, status, created_at FROM contact_messages ORDER BY created_at DESC, id DESC')->fetchAll();
    $messagesLoaded = true;
} catch (Throwable $e) {
    error_log('Messages unavailable (' . get_class($e) . ').');
}
include '../../includes/header.php';
?>
<div class="admin-shell">
    <?php include '../../includes/sidebar.php'; ?>
    <div class="admin-main">
        <?php include '../../includes/navbar.php'; ?>
        <main class="admin-content" id="main-content">
            <header class="page-header"><div><nav aria-label="Breadcrumb"><ol class="breadcrumb"><li class="breadcrumb-item"><a href="../../dashboard/admin.php">Dashboard</a></li><li class="breadcrumb-item active">Messages</li></ol></nav><h1>Messages</h1><p>Messages sent through the public contact form.</p></div></header>
            <section class="data-card" aria-labelledby="messagesHeading" data-messages-inbox data-loaded="<?= $messagesLoaded ? 'true' : 'false' ?>">
                <p class="px-4 pt-3 mb-0 small text-secondary" data-messages-live-status role="status">Messages update automatically.</p>
                <div class="data-card-header"><div><h2 id="messagesHeading">Contact inbox</h2><p>Review and respond through the contact details provided by the sender.</p></div><span class="status-badge status-info"><?= count($messages) ?> messages</span></div>
                <div class="table-responsive"><table class="table app-table align-middle"><thead><tr><th>Sender</th><th>Sitio</th><th>Contact number</th><th>Subject</th><th>Message</th><th>Date</th><th>Status</th></tr></thead><tbody>
                <?php foreach ($messages as $message): ?><tr><td><span class="table-primary-text"><?= htmlspecialchars($message['name'], ENT_QUOTES, 'UTF-8') ?></span><span class="table-secondary-text"><?= htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8') ?></span></td><td><?= htmlspecialchars($message['sitio'] ?: 'Not provided', ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($message['phone'] ?: 'Not provided', ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($message['subject'], ENT_QUOTES, 'UTF-8') ?></td><td style="max-width:420px;"><?= nl2br(htmlspecialchars($message['message'], ENT_QUOTES, 'UTF-8')) ?></td><td><?= htmlspecialchars($message['created_at'], ENT_QUOTES, 'UTF-8') ?></td><td><span class="status-badge <?= $message['status'] === 'Unread' ? 'status-warning' : 'status-neutral' ?>"><?= htmlspecialchars($message['status'], ENT_QUOTES, 'UTF-8') ?></span></td></tr><?php endforeach; ?>
                <?php if (!$messages): ?><tr><td colspan="7" class="text-center text-body-secondary py-4">No messages received yet.</td></tr><?php endif; ?>
                </tbody></table></div>
            </section>
        </main>
    </div>
</div>
<script src="../../assets/js/messages-live.js?v=2" defer></script>
<?php include '../../includes/footer.php'; ?>
