<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// Handle Delete Message
if (isset($_GET['del'])) {
    $del_id = (int)$_GET['del'];
    if (mysqli_query($conn, "DELETE FROM contact_messages WHERE id = $del_id")) {
        $_SESSION['admin_msg'] = "Inquiry message successfully deleted.";
    } else {
        $_SESSION['admin_msg'] = "Failed to delete inquiry message.";
    }
    header('Location: messages.php');
    exit();
}

$query = "SELECT * FROM contact_messages ORDER BY id DESC";
$res = mysqli_query($conn, $query);
$totalMessages = $res ? mysqli_num_rows($res) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | Client Inquiries</title>

    <link rel="stylesheet" href="./assets/css/layout.css">
    <link rel="stylesheet" href="./assets/css/orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="admin-wrapper">

    <?php include './include/sidebar.php'; ?>

    <div class="main-content">

        <?php include './include/header.php'; ?>

        <main class="page-content">

            <div class="page-header">
                <div>
                    <div class="breadcrumb">
                        <a href="./home.php">Dashboard</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Messages</span>
                    </div>

                    <h1 class="page-title">Client Inquiries</h1>
                    <p class="page-description">Customer service requests submitted via website contact form.</p>
                </div>
            </div>

            <?php if (isset($_SESSION['admin_msg'])): 
                $inq_msg = (string)$_SESSION['admin_msg'];
                $is_inq_error = preg_match('/(not|failed|could not|error)/i', $inq_msg);
            ?>
                <div class="alert <?= $is_inq_error ? 'alert-danger' : 'alert-success' ?> alert-dismissible fade show mb-4" role="alert" data-type="<?= $is_inq_error ? 'error' : 'success' ?>">
                    <i class="fa-solid <?= $is_inq_error ? 'fa-circle-exclamation' : 'fa-circle-check' ?> me-2"></i> <?php echo htmlspecialchars($inq_msg); ?>
                    <button type="button" class="alert-close-btn" data-bs-dismiss="alert" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
                </div>
                <?php unset($_SESSION['admin_msg']); ?>
            <?php endif; ?>

            <div class="product-summary order-summary mb-4">
                <div class="product-summary-card">
                    <div class="product-summary-icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <p>Total Inquiries</p>
                        <h3><?php echo $totalMessages; ?></h3>
                    </div>
                </div>
            </div>

            <div class="product-card product-table-card">
                <div class="product-table-header">
                    <div>
                        <h2>Inquiry Inbox</h2>
                        <p>Read customer inquiries and respond directly.</p>
                    </div>
                </div>

                <div class="product-table-wrapper">
                    <table class="premium-product-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Email / Contact</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php 
                        if ($totalMessages > 0):
                            $i = 1;
                            while ($msg = mysqli_fetch_assoc($res)): 
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><strong><?php echo htmlspecialchars($msg['name']); ?></strong></td>
                                <td>
                                    <div><?php echo htmlspecialchars($msg['email']); ?></div>
                                    <small class="text-muted"><?php echo htmlspecialchars($msg['phone'] ?? '—'); ?></small>
                                </td>
                                <td><strong><?php echo htmlspecialchars($msg['subject']); ?></strong></td>
                                <td style="max-width: 320px; white-space: normal;">
                                    <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?></small>
                                </td>
                                <td>
                                    <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>" 
                                       class="btn btn-sm btn-dark me-1" title="Reply via Email" style="font-size: 11px;">
                                        <i class="fa-solid fa-reply"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-danger" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#deleteMsg<?php echo $msg['id']; ?>" 
                                            title="Delete Inquiry" 
                                            style="font-size: 11px;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                    <!-- DELETE INQUIRY MODAL -->
                                    <div class="modal fade" id="deleteMsg<?php echo $msg['id']; ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content misha-modal">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Delete Inquiry?</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body text-start" style="font-size: 14px; color: #333;">
                                                    Are you sure you want to delete inquiry from <strong><?php echo htmlspecialchars($msg['name']); ?></strong> (<em><?php echo htmlspecialchars($msg['email']); ?></em>)?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="modal-btn btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="messages.php?del=<?php echo $msg['id']; ?>" class="modal-btn modal-btn-delete">
                                                        Delete Inquiry
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
                                    No client inquiries received yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </main>
    </div>

    <?php include './include/footer.php'; ?>

</div>

</body>
</html>
