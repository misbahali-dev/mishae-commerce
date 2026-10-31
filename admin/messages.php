<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/config.php';

// Helper to format WhatsApp international phone number for Pakistan / global
function formatWhatsAppNumber($phone) {
    $digits = preg_replace('/\D/', '', (string)$phone);
    if (empty($digits)) return '';
    if (substr($digits, 0, 1) === '0') {
        $digits = '92' . substr($digits, 1);
    } elseif (strlen($digits) === 10 && substr($digits, 0, 2) !== '92') {
        $digits = '92' . $digits;
    }
    return $digits;
}

// Handle Delete Message
if (isset($_GET['del'])) {
    $del_id = (int)$_GET['del'];
    if ($del_id > 0 && mysqli_query($conn, "DELETE FROM contact_messages WHERE id = $del_id")) {
        $_SESSION['admin_msg'] = "Inquiry message has been successfully deleted.";
    } else {
        $_SESSION['admin_msg'] = "Failed to delete inquiry message.";
    }
    header('Location: messages.php');
    exit();
}

$query = "SELECT * FROM contact_messages ORDER BY id DESC";
$res = mysqli_query($conn, $query);
$messages = [];
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $messages[] = $row;
    }
}
$totalMessages = count($messages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>MiSHAÉ | Client Inquiries</title>

    <link rel="stylesheet" href="./assets/css/layout.css?v=2.4">
    <link rel="stylesheet" href="./assets/css/orders.css?v=2.4">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
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
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                        <p>Read customer inquiries and reply directly via Email or WhatsApp.</p>
                    </div>
                </div>

                <div class="product-table-wrapper" style="scrollbar-width: none; -ms-overflow-style: none;">
                    <table class="premium-product-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Client Name</th>
                                <th>Contact Information</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Received Date</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php 
                        if ($totalMessages > 0):
                            $i = 1;
                            foreach ($messages as $msg): 
                                $wa_num = formatWhatsAppNumber($msg['phone'] ?? '');
                                $wa_text = "Hello " . $msg['name'] . ", thank you for contacting MiSHAÉ regarding: " . $msg['subject'] . ". How may we assist you today?";
                                $email_body = "Hello " . $msg['name'] . ",\n\nThank you for reaching out to MiSHAÉ regarding your inquiry on '" . $msg['subject'] . "'.\n\nKind regards,\nMiSHAÉ Concierge Team";
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td>
                                    <strong style="color: #29211f;"><?php echo htmlspecialchars($msg['name']); ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" class="inquiry-badge-email" title="Click to email">
                                            <i class="fa-solid fa-envelope"></i> <?php echo htmlspecialchars($msg['email']); ?>
                                        </a>
                                    </div>
                                    <?php if (!empty($msg['phone'])): ?>
                                        <div class="mt-1">
                                            <span class="inquiry-badge-phone">
                                                <i class="fa-brands fa-whatsapp"></i> <?php echo htmlspecialchars($msg['phone']); ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border" style="font-weight: 600; font-size: 12px; padding: 5px 9px;">
                                        <?php echo htmlspecialchars($msg['subject']); ?>
                                    </span>
                                </td>
                                <td style="max-width: 280px; white-space: normal;">
                                    <div style="max-height: 48px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; line-height: 1.4; font-size: 12.5px; color: #555;">
                                        <?php echo htmlspecialchars($msg['message']); ?>
                                    </div>
                                    <a href="#" data-bs-toggle="modal" data-bs-target="#viewMsg<?php echo $msg['id']; ?>" style="font-size: 11px; color: #a78543; text-decoration: none; font-weight: 600;">
                                        Read full message &rarr;
                                    </a>
                                </td>
                                <td>
                                    <small class="text-muted" style="font-size: 11px;">
                                        <?php echo date('M d, Y h:i A', strtotime($msg['created_at'])); ?>
                                    </small>
                                </td>
                                <td style="text-align: right;">
                                    <div class="action-btn-group">
                                        <!-- View Full Message Button -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-secondary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewMsg<?php echo $msg['id']; ?>" 
                                                title="View Complete Message"
                                                style="font-size: 11px; padding: 5px 9px;">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        <!-- Reply via WhatsApp (if phone provided) -->
                                        <?php if (!empty($wa_num)): ?>
                                            <a href="https://wa.me/<?php echo $wa_num; ?>?text=<?php echo urlencode($wa_text); ?>" 
                                               target="_blank" 
                                               class="btn btn-sm btn-success" 
                                               title="Reply on WhatsApp" 
                                               style="font-size: 11px; padding: 5px 9px; background: #25D366; border-color: #25D366;">
                                                <i class="fa-brands fa-whatsapp"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Reply via Email -->
                                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>&body=<?php echo urlencode($email_body); ?>" 
                                           class="btn btn-sm btn-dark" 
                                           title="Reply via Email" 
                                           style="font-size: 11px; padding: 5px 9px;">
                                            <i class="fa-solid fa-reply"></i>
                                        </a>

                                        <!-- Delete Message Button -->
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteMsg<?php echo $msg['id']; ?>" 
                                                title="Delete Inquiry" 
                                                style="font-size: 11px; padding: 5px 9px;">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php 
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 40px; color: #888;">
                                    <i class="fa-regular fa-envelope-open d-block mb-2" style="font-size: 28px; color: #ccc;"></i>
                                    No client inquiries received yet.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- MODALS (Rendered outside table for 100% clean stacking) -->
            <?php if ($totalMessages > 0): ?>
                <?php foreach ($messages as $msg): 
                    $wa_num = formatWhatsAppNumber($msg['phone'] ?? '');
                    $wa_text = "Hello " . $msg['name'] . ", thank you for contacting MiSHAÉ regarding: " . $msg['subject'] . ". How may we assist you today?";
                    $email_body = "Hello " . $msg['name'] . ",\n\nThank you for reaching out to MiSHAÉ regarding your inquiry on '" . $msg['subject'] . "'.\n\nKind regards,\nMiSHAÉ Concierge Team";
                ?>
                    <!-- 1. VIEW & REPLY MODAL -->
                    <div class="modal fade" id="viewMsg<?php echo $msg['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content" style="border-radius: 8px; border: none; box-shadow: 0 15px 40px rgba(0,0,0,0.18); overflow: hidden;">
                                <div class="modal-header" style="background: #29211f; color: #fff; padding: 16px 22px;">
                                    <h5 class="modal-title" style="font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                                        <i class="fa-solid fa-envelope-open-text" style="color: #a78543;"></i>
                                        <span>Inquiry from <?php echo htmlspecialchars($msg['name']); ?></span>
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body" style="padding: 24px; font-size: 13.5px; color: #333;">
                                    <div class="row mb-3 pb-3 border-bottom">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <small class="text-muted text-uppercase d-block" style="font-size: 10.5px; letter-spacing: 1px; font-weight: 600;">Sender Name</small>
                                            <strong style="font-size: 15px; color: #29211f;"><?php echo htmlspecialchars($msg['name']); ?></strong>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted text-uppercase d-block" style="font-size: 10.5px; letter-spacing: 1px; font-weight: 600;">Date &amp; Time</small>
                                            <span><?php echo date('F d, Y \a\t h:i A', strtotime($msg['created_at'])); ?></span>
                                        </div>
                                    </div>

                                    <div class="row mb-3 pb-3 border-bottom">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <small class="text-muted text-uppercase d-block" style="font-size: 10.5px; letter-spacing: 1px; font-weight: 600;">Email Address</small>
                                            <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>" style="color: #29211f; font-weight: 500;">
                                                <i class="fa-solid fa-envelope text-muted me-1"></i> <?php echo htmlspecialchars($msg['email']); ?>
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted text-uppercase d-block" style="font-size: 10.5px; letter-spacing: 1px; font-weight: 600;">Phone / WhatsApp</small>
                                            <span>
                                                <?php if (!empty($msg['phone'])): ?>
                                                    <i class="fa-brands fa-whatsapp text-success me-1"></i> <?php echo htmlspecialchars($msg['phone']); ?>
                                                <?php else: ?>
                                                    <em>Not provided</em>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted text-uppercase d-block mb-1" style="font-size: 10.5px; letter-spacing: 1px; font-weight: 600;">Subject</small>
                                        <div style="font-size: 14px; font-weight: 600; color: #29211f;">
                                            <?php echo htmlspecialchars($msg['subject']); ?>
                                        </div>
                                    </div>

                                    <div>
                                        <small class="text-muted text-uppercase d-block mb-1" style="font-size: 10.5px; letter-spacing: 1px; font-weight: 600;">Message Content</small>
                                        <div style="background: #FAF8F5; padding: 18px; border-radius: 6px; border: 1px solid #EAE5DF; line-height: 1.7; white-space: pre-wrap; color: #29211f; font-size: 13.5px;">
                                            <?php echo htmlspecialchars($msg['message']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer" style="background: #FAF8F5; border-top: 1px solid #EAE5DF; display: flex; justify-content: space-between; padding: 14px 22px;">
                                    <div>
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#deleteMsg<?php echo $msg['id']; ?>">
                                            <i class="fa-solid fa-trash me-1"></i> Delete Message
                                        </button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <?php if (!empty($wa_num)): ?>
                                            <a href="https://wa.me/<?php echo $wa_num; ?>?text=<?php echo urlencode($wa_text); ?>" 
                                               target="_blank" 
                                               class="btn btn-success btn-sm"
                                               style="background: #25D366; border-color: #25D366;">
                                                <i class="fa-brands fa-whatsapp me-1"></i> Reply on WhatsApp
                                            </a>
                                        <?php endif; ?>
                                        <a href="mailto:<?php echo htmlspecialchars($msg['email']); ?>?subject=Re: <?php echo urlencode($msg['subject']); ?>&body=<?php echo urlencode($email_body); ?>" 
                                           class="btn btn-dark btn-sm">
                                            <i class="fa-solid fa-paper-plane me-1"></i> Reply via Email
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. DELETE INQUIRY MODAL -->
                    <div class="modal fade" id="deleteMsg<?php echo $msg['id']; ?>" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content" style="border-radius: 8px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15); overflow: hidden;">
                                <div class="modal-header" style="background: #FAF8F5; border-bottom: 1px solid #EAE5DF; padding: 14px 20px;">
                                    <h5 class="modal-title" style="font-size: 15px; font-weight: 600; color: #29211f;">
                                        <i class="fa-solid fa-triangle-exclamation text-danger me-2"></i> Delete Inquiry?
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-start" style="font-size: 13.5px; color: #444; padding: 20px;">
                                    Are you sure you want to delete inquiry from <strong><?php echo htmlspecialchars($msg['name']); ?></strong> (<em><?php echo htmlspecialchars($msg['email']); ?></em>)?
                                    <p class="text-muted small mt-2 mb-0">This message will be permanently removed from your database.</p>
                                </div>
                                <div class="modal-footer" style="border-top: 1px solid #EAE5DF; background: #FAF8F5; padding: 12px 20px;">
                                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                                    <a href="messages.php?del=<?php echo $msg['id']; ?>" class="btn btn-danger btn-sm">
                                        <i class="fa-solid fa-trash me-1"></i> Yes, Delete Inquiry
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </main>
    </div>

    <?php include './include/footer.php'; ?>

</div>

<!-- Bootstrap 5 Bundle JS (Mandatory for Modals & Tooltips) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
