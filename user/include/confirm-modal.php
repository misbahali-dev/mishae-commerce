<!-- MiSHAÉ Luxury Confirmation Modal -->
<div id="mishaeToastContainer" class="mishae-toast-container"></div>

<div class="modal fade mishae-confirm-modal" id="mishaeConfirmModal" tabindex="-1" role="dialog" aria-labelledby="mishaeConfirmTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="confirm-icon-box" id="mishaeConfirmIconWrap">
                        <i class="far fa-trash-alt" id="mishaeConfirmIcon"></i>
                    </div>
                    <h5 class="modal-title ml-3" id="mishaeConfirmTitle">Confirm Action</h5>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p class="confirm-main-msg" id="mishaeConfirmMessage">
                    Are you sure you want to proceed?
                </p>
                <div class="confirm-item-badge" id="mishaeConfirmSubtext" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal-cancel" data-dismiss="modal" id="mishaeCancelBtn">
                    Cancel
                </button>
                <a href="#" class="btn-modal-confirm" id="mishaeConfirmBtn">
                    <i class="far fa-trash-alt mr-1"></i> <span id="mishaeConfirmBtnText">Delete</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Container & Dialog */
.mishae-confirm-modal {
    z-index: 10600 !important;
}
.mishae-confirm-modal .modal-dialog {
    max-width: 440px;
    margin: 1.75rem auto;
}
.mishae-confirm-modal .modal-content {
    background: #FFFFFF;
    border: 1px solid #EAE5DF;
    border-radius: 12px;
    box-shadow: 0 20px 50px rgba(14, 14, 16, 0.18);
    overflow: hidden;
}

/* Header */
.mishae-confirm-modal .modal-header {
    background: #FAF8F5;
    border-bottom: 1px solid #F0ECE6;
    padding: 16px 22px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mishae-confirm-modal .modal-title {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: 18px;
    font-weight: 600;
    color: #29211f;
    margin: 0;
    line-height: 1.2;
}
.mishae-confirm-modal .confirm-icon-box {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #FFFFFF;
    border: 1px solid #EAE5DF;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #a78543;
    font-size: 15px;
    flex-shrink: 0;
}
.mishae-confirm-modal .close {
    font-size: 26px;
    line-height: 1;
    color: #77716C;
    background: transparent;
    border: none;
    opacity: 0.6;
    cursor: pointer;
    padding: 0;
    margin: -10px -5px 0 0;
    transition: opacity 0.2s, color 0.2s;
    outline: none;
}
.mishae-confirm-modal .close:hover {
    color: #29211f;
    opacity: 1;
}

/* Body */
.mishae-confirm-modal .modal-body {
    padding: 24px 22px;
    background: #FFFFFF;
}
.mishae-confirm-modal .confirm-main-msg {
    font-size: 14.5px;
    color: #29211f;
    font-weight: 500;
    line-height: 1.55;
    margin: 0 0 8px;
}
.mishae-confirm-modal .confirm-item-badge {
    display: inline-block;
    background: #FAF8F5;
    border: 1px solid #EAE5DF;
    border-radius: 4px;
    padding: 4px 12px;
    font-size: 12px;
    color: #77716C;
    margin-top: 4px;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-weight: 500;
}

/* Footer */
.mishae-confirm-modal .modal-footer {
    background: #FAF8F5;
    border-top: 1px solid #F0ECE6;
    padding: 14px 22px;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}
.mishae-confirm-modal .btn-modal-cancel {
    background: #FFFFFF;
    color: #29211f !important;
    border: 1px solid #29211f;
    padding: 9px 20px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none !important;
    line-height: 1.4;
}
.mishae-confirm-modal .btn-modal-cancel:hover {
    background: #a78543 !important;
    border-color: #a78543 !important;
    color: #FFFFFF !important;
}
.mishae-confirm-modal .btn-modal-confirm {
    background: #29211f;
    color: #FFFFFF !important;
    border: 1px solid #29211f;
    padding: 9px 22px;
    font-size: 11px;
    font-weight: 600;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none !important;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    line-height: 1.4;
}
.mishae-confirm-modal .btn-modal-confirm:hover {
    background: #a78543 !important;
    border-color: #a78543 !important;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(167, 133, 67, 0.3);
}

.modal-backdrop.show {
    opacity: 0.55 !important;
    z-index: 10550 !important;
}
</style>

<script>
/**
 * Global MiSHAÉ Luxury Confirmation Modal Handler
 */
function openMishaeConfirm(options) {
    options = options || {};
    var titleEl = document.getElementById('mishaeConfirmTitle');
    var msgEl = document.getElementById('mishaeConfirmMessage');
    var subtextEl = document.getElementById('mishaeConfirmSubtext');
    var btnEl = document.getElementById('mishaeConfirmBtn');
    var btnTextEl = document.getElementById('mishaeConfirmBtnText');
    var iconEl = document.getElementById('mishaeConfirmIcon');

    if (titleEl) titleEl.innerText = options.title || 'Please Confirm';
    if (msgEl) msgEl.innerText = options.message || 'Are you sure you want to proceed?';

    if (subtextEl) {
        if (options.subtext) {
            subtextEl.innerText = options.subtext;
            subtextEl.style.display = 'inline-block';
        } else {
            subtextEl.style.display = 'none';
        }
    }

    if (btnTextEl) {
        btnTextEl.innerText = options.confirmText || 'Confirm';
    }

    if (iconEl && options.icon) {
        iconEl.className = options.icon;
    }

    if (btnEl) {
        if (options.confirmHref) {
            btnEl.setAttribute('href', options.confirmHref);
            btnEl.onclick = null;
        } else if (typeof options.onConfirm === 'function') {
            btnEl.setAttribute('href', '#');
            btnEl.onclick = function(e) {
                e.preventDefault();
                options.onConfirm();
                closeMishaeConfirm();
            };
        }
    }

    // Trigger display
    if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
        window.jQuery('#mishaeConfirmModal').modal('show');
    } else {
        var m = document.getElementById('mishaeConfirmModal');
        if (m) {
            m.classList.add('show');
            m.style.display = 'block';
            document.body.classList.add('modal-open');
        }
    }
}

function closeMishaeConfirm() {
    if (window.jQuery && typeof window.jQuery.fn.modal === 'function') {
        window.jQuery('#mishaeConfirmModal').modal('hide');
    } else {
        var m = document.getElementById('mishaeConfirmModal');
        if (m) {
            m.classList.remove('show');
            m.style.display = 'none';
            document.body.classList.remove('modal-open');
            var bd = document.querySelector('.modal-backdrop');
            if (bd) bd.remove();
        }
    }
}

// Ensure data-dismiss works even without full bootstrap JS
document.addEventListener('DOMContentLoaded', function() {
    var dismissButtons = document.querySelectorAll('#mishaeConfirmModal [data-dismiss="modal"]');
    dismissButtons.forEach(function(btn) {
        btn.addEventListener('click', closeMishaeConfirm);
    });
});

/**
 * Convenience Helpers for Specific Site Actions
 */
function confirmWishlistRemove(deleteUrl, productName) {
    openMishaeConfirm({
        title: 'Remove From Wishlist',
        message: 'Are you sure you really want to delete this item from your wishlist?',
        subtext: productName ? productName : '',
        confirmText: 'Delete Item',
        confirmHref: deleteUrl,
        icon: 'far fa-heart'
    });
}

function confirmCartRemove(deleteUrl, productName) {
    openMishaeConfirm({
        title: 'Remove From Shopping Bag',
        message: 'Are you sure you really want to remove this item from your shopping bag?',
        subtext: productName ? productName : '',
        confirmText: 'Remove Item',
        confirmHref: deleteUrl,
        icon: 'fas fa-shopping-bag'
    });
}

function confirmClearBag(clearUrl) {
    openMishaeConfirm({
        title: 'Empty Shopping Bag',
        message: 'Are you sure you really want to empty your entire shopping bag?',
        subtext: 'All selected fabrics will be cleared from your order.',
        confirmText: 'Empty Bag',
        confirmHref: clearUrl,
        icon: 'far fa-trash-alt'
    });
}

/**
 * MiSHAÉ Luxury Viewport Toast Notifications
 */
function showMishaeToast(options) {
    options = options || {};
    var container = document.getElementById('mishaeToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'mishaeToastContainer';
        container.className = 'mishae-toast-container';
        document.body.appendChild(container);
    }

    var toast = document.createElement('div');
    toast.className = 'mishae-luxury-toast';

    var iconHtml = options.isHeart
        ? '<div class="toast-icon-wrap is-heart"><i class="fas fa-heart"></i></div>'
        : '<div class="toast-icon-wrap"><i class="' + (options.icon || 'fas fa-check') + '"></i></div>';

    var actionHtml = '';
    if (options.actionUrl && options.actionText) {
        actionHtml = '<a href="' + options.actionUrl + '" class="toast-action-btn">' + options.actionText + '</a>';
    }

    toast.innerHTML = iconHtml +
        '<div class="toast-content">' +
            '<div class="toast-title">' + (options.title || 'MiSHAÉ Atelier') + '</div>' +
            '<div class="toast-message">' + (options.message || '') + '</div>' +
            actionHtml +
        '</div>' +
        '<button type="button" class="toast-close-btn" aria-label="Dismiss">&times;</button>' +
        '<div class="toast-progress"></div>';

    var closeBtn = toast.querySelector('.toast-close-btn');
    if (closeBtn) {
        closeBtn.onclick = function() {
            dismissToast(toast);
        };
    }

    container.appendChild(toast);

    // Auto dismiss after 4.2 seconds
    var timer = setTimeout(function() {
        dismissToast(toast);
    }, 4200);

    function dismissToast(el) {
        clearTimeout(timer);
        el.classList.add('toast-hiding');
        setTimeout(function() {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }, 320);
    }
}

/**
 * Global AJAX Wishlist Click Interceptor
 * Toggles wishlist items in-place without page reload or losing scroll position
 */
document.addEventListener('click', function(e) {
    var link = e.target.closest('a[href*="action=wishlist_toggle"]');
    if (!link) return;

    // If on wishlist.php itself, allow normal confirmation modal flow
    if (window.location.pathname.indexOf('wishlist.php') !== -1) {
        return;
    }

    e.preventDefault();
    var href = link.getAttribute('href');
    var ajaxUrl = href + (href.indexOf('?') !== -1 ? '&' : '?') + 'ajax=1';

    // Heart icon animation
    var icon = link.querySelector('i');
    if (icon) {
        icon.style.transition = 'transform 0.25s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        icon.style.transform = 'scale(1.4)';
    }

    fetch(ajaxUrl, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(res) {
        return res.json();
    })
    .then(function(data) {
        if (data && data.success) {
            // Update icon appearance
            if (icon) {
                if (data.status === 'added') {
                    icon.className = 'fas fa-heart text-danger';
                    link.style.color = '#C62828';
                } else {
                    icon.className = 'far fa-heart';
                    link.style.color = '#222222';
                }
                setTimeout(function() {
                    icon.style.transform = 'scale(1)';
                }, 220);
            }

            // Update navbar wishlist count badge
            var badge = document.getElementById('navWishlistBadge');
            if (badge) {
                badge.innerText = data.count;
                badge.style.display = (data.count > 0) ? '' : 'none';
            }

            // Show floating luxury toast notification
            showMishaeToast({
                title: (data.status === 'added') ? 'Added to Wishlist' : 'Removed from Wishlist',
                message: data.message,
                isHeart: (data.status === 'added'),
                actionUrl: 'wishlist.php',
                actionText: 'View Wishlist \u2192'
            });
        } else {
            // Fallback
            window.location.href = href;
        }
    })
    .catch(function(err) {
        // Fallback to standard navigation if fetch fails
        window.location.href = href;
    });
});

/**
 * Auto-dismiss any static flash messages smoothly after 4.5 seconds
 */
document.addEventListener('DOMContentLoaded', function() {
    var flashAlerts = document.querySelectorAll('.mishae-alert');
    flashAlerts.forEach(function(alert) {
        setTimeout(function() {
            if (typeof window.dismissMishaeAlert === 'function') {
                window.dismissMishaeAlert(alert);
            } else {
                alert.classList.add('alert-collapsing');
                setTimeout(function() {
                    if (alert.parentNode) alert.parentNode.removeChild(alert);
                }, 400);
            }
        }, 4500);
    });
});
</script>