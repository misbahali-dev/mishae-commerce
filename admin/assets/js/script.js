"use strict";

document.addEventListener("DOMContentLoaded", function () {

    // ============================================================
    // PROFESSIONAL SESSION ALERT HANDLER
    // 1. Success messages disappear automatically after 3 seconds (3000ms)
    // 2. Error messages remain visible for 5 seconds (5000ms)
    // 3. Cross (×) button closes any alert immediately
    // 4. Smooth collapse transition prevents sudden layout jumps
    // 5. Completely safe: zero errors when no alert exists
    // ============================================================
    const alertElements = document.querySelectorAll(
        ".category_message, #category_message, .product-message, #product_message, .login-message, .alert, #msg, [role='alert']"
    );

    alertElements.forEach(function (alertItem) {
        if (!alertItem) return;

        let isDismissed = false;

        function dismiss() {
            if (isDismissed) return;
            isDismissed = true;

            // Anchor current height and transition smoothly
            alertItem.style.boxSizing = "border-box";
            alertItem.style.maxHeight = alertItem.offsetHeight + "px";
            alertItem.style.transition = "opacity 0.4s ease, max-height 0.4s ease, margin 0.4s ease, padding 0.4s ease";

            // Force reflow and start collapse
            void alertItem.offsetHeight;

            alertItem.style.opacity = "0";
            alertItem.style.overflow = "hidden";
            alertItem.style.maxHeight = "0px";
            alertItem.style.marginTop = "0px";
            alertItem.style.marginBottom = "0px";
            alertItem.style.paddingTop = "0px";
            alertItem.style.paddingBottom = "0px";

            setTimeout(function () {
                if (alertItem.parentNode) {
                    alertItem.remove();
                }
            }, 420);
        }

        // Cross (×) button immediate dismiss
        const closeBtn = alertItem.querySelector(".alert-close-btn, .btn-close, .close");
        if (closeBtn) {
            closeBtn.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                dismiss();
            });
        }

        // Check if message is an error or success
        const typeAttr = alertItem.getAttribute("data-type");
        const isError = typeAttr === "error" ||
            alertItem.classList.contains("alert-danger") ||
            alertItem.classList.contains("category_message_error") ||
            alertItem.classList.contains("product-message-error") ||
            alertItem.classList.contains("login-message") ||
            /(not|failed|could not|error|invalid)/i.test(alertItem.textContent || "");

        // 3 seconds for success, 5 seconds for error
        const delay = isError ? 5000 : 3000;

        setTimeout(dismiss, delay);
    });

    // MOBILE SIDEBAR
    const sidebar = document.getElementById("sidebar");
    const menuToggle = document.getElementById("menuToggle");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    function closeSidebar() {
        if (!sidebar || !menuToggle || !sidebarOverlay) return;

        sidebar.classList.remove("show");
        sidebarOverlay.classList.remove("show");
        menuToggle.setAttribute("aria-expanded", "false");
        document.body.style.overflow = "";
    }

    if (sidebar && menuToggle && sidebarOverlay) {
        menuToggle.addEventListener("click", function () {
            const isOpen = sidebar.classList.contains("show");

            sidebar.classList.toggle("show", !isOpen);
            sidebarOverlay.classList.toggle("show", !isOpen);
            menuToggle.setAttribute("aria-expanded", String(!isOpen));

            document.body.style.overflow = isOpen ? "" : "hidden";
        });

        sidebarOverlay.addEventListener("click", closeSidebar);

        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") {
                closeSidebar();
            }
        });

        sidebar.querySelectorAll("a").forEach(function (link) {
            link.addEventListener("click", function () {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        window.addEventListener("resize", function () {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    }

    // SIDEBAR DROPDOWNS
    document.querySelectorAll(".sidebar-menu .dropdown").forEach(function (dropdown) {
        dropdown.addEventListener("click", function () {
            const submenu = this.nextElementSibling;

            if (!submenu || !submenu.classList.contains("submenu")) return;

            const wasOpen = this.classList.contains("open");

            document.querySelectorAll(".sidebar-menu .dropdown").forEach(function (item) {
                item.classList.remove("open");
                item.setAttribute("aria-expanded", "false");

                const child = item.nextElementSibling;

                if (child && child.classList.contains("submenu")) {
                    child.classList.remove("open");
                }
            });

            if (!wasOpen) {
                this.classList.add("open");
                this.setAttribute("aria-expanded", "true");
                submenu.classList.add("open");
            }
        });
    });

    // ACTIVE SIDEBAR LINK
    const currentPath = window.location.pathname.replace(/\/+$/, "");

    document.querySelectorAll(".sidebar-menu a").forEach(function (link) {
        const linkPath = new URL(link.href, window.location.origin).pathname.replace(/\/+$/, "");

        if (linkPath === currentPath) {
            link.classList.add("active");

            const submenu = link.closest(".submenu");

            if (submenu) {
                submenu.classList.add("open");

                const parent = submenu.previousElementSibling;

                if (parent && parent.classList.contains("dropdown")) {
                    parent.classList.add("open");
                    parent.setAttribute("aria-expanded", "true");
                }
            }
        } else {
            link.classList.remove("active");
        }
    });

    // TOPBAR DROPDOWNS (Notifications & Admin Profile)
    const notifBtn = document.getElementById("notificationBtn");
    const notifDropdown = document.getElementById("notificationDropdown");
    const profileBtn = document.getElementById("adminProfileBtn");
    const profileDropdown = document.getElementById("profileDropdown");

    function closeAllTopbarDropdowns() {
        if (notifDropdown) {
            notifDropdown.classList.remove("show");
            if (notifBtn) notifBtn.setAttribute("aria-expanded", "false");
        }
        if (profileDropdown) {
            profileDropdown.classList.remove("show");
            if (profileBtn) {
                profileBtn.classList.remove("open");
                profileBtn.setAttribute("aria-expanded", "false");
            }
        }
    }

    if (notifBtn && notifDropdown) {
        notifBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            const isOpen = notifDropdown.classList.contains("show");
            closeAllTopbarDropdowns();
            if (!isOpen) {
                notifDropdown.classList.add("show");
                notifBtn.setAttribute("aria-expanded", "true");
            }
        });
    }

    if (profileBtn && profileDropdown) {
        profileBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            const isOpen = profileDropdown.classList.contains("show");
            closeAllTopbarDropdowns();
            if (!isOpen) {
                profileDropdown.classList.add("show");
                profileBtn.classList.add("open");
                profileBtn.setAttribute("aria-expanded", "true");
            }
        });

        profileBtn.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                profileBtn.click();
            }
        });
    }

    document.addEventListener("click", function (e) {
        if (notifDropdown && notifDropdown.contains(e.target)) return;
        if (profileDropdown && profileDropdown.contains(e.target)) return;
        closeAllTopbarDropdowns();
    });

    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeAllTopbarDropdowns();
        }
    });

    // DASHBOARD GREETING
    const greetingElement = document.getElementById("greeting");

    if (greetingElement) {
        const hour = new Date().getHours();
        let greeting = "Good Morning";

        if (hour >= 12 && hour < 17) {
            greeting = "Good Afternoon";
        } else if (hour >= 17 && hour < 21) {
            greeting = "Good Evening";
        } else if (hour >= 21 || hour < 5) {
            greeting = "Good Night";
        }

        greetingElement.textContent = greeting + ", Admin";
    }

    // CATEGORY SEARCH
    const categorySearch = document.getElementById("categorySearch");
    const categoryCount = document.getElementById("categoryCount");
    const noCategoriesFound = document.getElementById("noCategoriesFound");

    if (categorySearch) {
        categorySearch.addEventListener("input", function () {
            const rows = document.querySelectorAll("#categoryTableBody tr");
            let count = 0;
            const value = this.value.toLowerCase().trim();

            rows.forEach(function (row) {
                const matched = row.textContent.toLowerCase().includes(value);
                row.style.display = matched ? "" : "none";

                if (matched) count++;
            });

            if (noCategoriesFound) {
                noCategoriesFound.hidden = count !== 0;
            }

            if (categoryCount) {
                categoryCount.textContent = "Showing " + count + " categories";
            }
        });
    }

    // CATEGORY STATUS TOGGLE
    document.querySelectorAll(".category-status").forEach(function (button) {
        button.addEventListener("click", async function () {
            const oldStatus = this.dataset.status;
            const newStatus = oldStatus === "1" ? "0" : "1";
            const catId = this.dataset.id;

            this.disabled = true;

            try {
                const response = await fetch("./../backend/category.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: new URLSearchParams({
                        change_status: "1",
                        cat_id: catId,
                        cat_status: oldStatus
                    })
                });

                const result = await response.json();

                if (!response.ok || !result.success) {
                    alert("Status could not be updated.");
                    return;
                }

                this.dataset.status = newStatus;
                this.textContent = newStatus === "1" ? "Active" : "Inactive";
                this.classList.toggle("active", newStatus === "1");
                this.classList.toggle("inactive", newStatus === "0");

            } catch (error) {
                console.error("Category status error:", error);
                alert("Connection or server response error.");
            } finally {
                this.disabled = false;
            }
        });
    });

    // CATEGORY EDIT AND DELETE MODALS
    const editCategoryModal = document.getElementById("editCategoryModal");
    const deleteCategoryModal = document.getElementById("deleteCategoryModal");

    function openModal(modal) {
        if (modal) {
            modal.classList.add("show");
            document.body.style.overflow = "hidden";
        }
    }

    function closeModal(modal) {
        if (modal) {
            modal.classList.remove("show");

            if (!document.querySelector(".category-modal-overlay.show") &&
                !document.querySelector(".modal-overlay.show")) {
                document.body.style.overflow = "";
            }
        }
    }

    document.querySelectorAll(".edit-category-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            const id = document.getElementById("editCatId");
            const name = document.getElementById("editCatName");
            const description = document.getElementById("editCatDes");
            const preview = document.getElementById("editCatPreview");

            if (id) id.value = this.dataset.id;
            if (name) name.value = this.dataset.name;
            if (description) description.value = this.dataset.description;

            if (preview) {
                if (this.dataset.image) {
                    preview.src = "./../uploads/" + this.dataset.image;
                    preview.hidden = false;
                } else {
                    preview.src = "";
                    preview.hidden = true;
                }
            }

            openModal(editCategoryModal);
        });
    });

    document.querySelectorAll(".delete-category-btn").forEach(function (button) {
        button.addEventListener("click", function () {
            const id = document.getElementById("deleteCatId");
            const name = document.getElementById("deleteCatName");

            if (id) id.value = this.dataset.id;
            if (name) name.textContent = this.dataset.name;

            openModal(deleteCategoryModal);
        });
    });

    // PRODUCT SEARCH
    const productSearch = document.getElementById("productSearch");
    const productCount = document.getElementById("productCount");
    const noProductsFound = document.getElementById("noProductsFound");

    if (productSearch) {
        productSearch.addEventListener("input", function () {
            const rows = document.querySelectorAll("#productTableBody tr");
            let count = 0;
            const value = this.value.toLowerCase().trim();

            rows.forEach(function (row) {
                const matched = row.textContent.toLowerCase().includes(value);
                row.style.display = matched ? "" : "none";

                if (matched) count++;
            });

            if (noProductsFound) {
                noProductsFound.hidden = count !== 0;
            }

            if (productCount) {
                productCount.textContent = "Showing " + count + " products";
            }
        });
    }

    // PRODUCT EDIT MODAL
    const updateModal = document.getElementById("updateModal");
    const deleteModal = document.getElementById("deleteModal");

    document.querySelectorAll(".edit-product").forEach(function (button) {
        button.addEventListener("click", function () {
            const fields = {
                edit_id: this.dataset.id,
                edit_name: this.dataset.name,
                edit_cat: this.dataset.cat,
                edit_price: this.dataset.price,
                edit_quan: this.dataset.quan,
                edit_des: this.dataset.des
            };

            Object.keys(fields).forEach(function (id) {
                const input = document.getElementById(id);

                if (input) {
                    input.value = fields[id] || "";
                }
            });

            const image = document.getElementById("edit_current_img");

            if (image) {
                if (this.dataset.img) {
                    image.src = "./../uploads/" + this.dataset.img;
                    image.style.display = "inline-block";
                } else {
                    image.removeAttribute("src");
                    image.style.display = "none";
                }
            }

            if (updateModal) {
                updateModal.classList.add("show");
                document.body.style.overflow = "hidden";
            }
        });
    });

    // PRODUCT DELETE MODAL
    document.querySelectorAll(".delete-product").forEach(function (button) {
        button.addEventListener("click", function () {
            const id = document.getElementById("delete_id");
            const name = document.getElementById("deleteProductName");

            if (id) id.value = this.dataset.id;
            if (name) name.textContent = this.dataset.name;

            if (deleteModal) {
                deleteModal.classList.add("show");
                document.body.style.overflow = "hidden";
            }
        });
    });

    // CLOSE MODALS
    document.querySelectorAll("[data-modal-close], [data-close]").forEach(function (button) {
        button.addEventListener("click", function () {
            const modal = this.closest(".category-modal-overlay, .modal-overlay, .modal");

            if (modal) {
                modal.classList.remove("show");
            } else {
                closeModal(editCategoryModal);
                closeModal(deleteCategoryModal);
                closeModal(updateModal);
                closeModal(deleteModal);
            }

            document.body.style.overflow = "";
        });
    });

    [editCategoryModal, deleteCategoryModal, updateModal, deleteModal].forEach(function (modal) {
        if (modal) {
            modal.addEventListener("click", function (event) {
                if (event.target === modal) {
                    modal.classList.remove("show");
                    document.body.style.overflow = "";
                }
            });
        }
    });

    document.addEventListener("keydown", function (event) {
        if (event.key === "Escape") {
            [editCategoryModal, deleteCategoryModal, updateModal, deleteModal].forEach(function (modal) {
                if (modal) modal.classList.remove("show");
            });

            document.body.style.overflow = "";
        }
    });

    // ORDER SEARCH & FILTER (guarded for pages where element may not exist)
    const searchOrder = document.getElementById("searchOrder");
    const statusFilter = document.getElementById("statusFilter");

    function filterOrders() {
        const orderTable = document.getElementById("orderTable");
        if (!orderTable) return;

        const searchVal = searchOrder ? searchOrder.value.toLowerCase().trim() : "";
        const statusVal = statusFilter ? statusFilter.value : "";
        const rows = orderTable.querySelectorAll("tr");
        let visible = 0;

        rows.forEach(function (row) {
            const text = row.innerText.toLowerCase();
            const matchesSearch = text.includes(searchVal);
            const matchesStatus = statusVal === "" || row.dataset.status === statusVal;

            if (matchesSearch && matchesStatus) {
                row.style.display = "";
                visible++;
            } else {
                row.style.display = "none";
            }
        });

        const noOrders = document.getElementById("noOrders");
        if (noOrders) {
            noOrders.style.display = visible === 0 ? "block" : "none";
        }
    }

    if (searchOrder) searchOrder.addEventListener("input", filterOrders);
    if (statusFilter) statusFilter.addEventListener("change", filterOrders);

    const orderSearch = document.getElementById("orderSearch");
    if (orderSearch) {
        orderSearch.addEventListener("input", function () {
            const searchVal = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll("#orderTableBody tr");

            rows.forEach(function (row) {
                row.style.display = row.innerText.toLowerCase().includes(searchVal) ? "" : "none";
            });
        });
    }

    const customerSearch = document.getElementById("customerSearch");
    if (customerSearch) {
        customerSearch.addEventListener("input", function () {
            const searchVal = this.value.toLowerCase().trim();
            const rows = document.querySelectorAll("#customerTableBody tr");

            rows.forEach(function (row) {
                row.style.display = row.innerText.toLowerCase().includes(searchVal) ? "" : "none";
            });
        });
    }

});

function viewOrder(button) {
    const row = button.closest("tr");
    if (!row) return;

    const setField = function(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val || "";
    };

    setField("dId", row.dataset.id);
    setField("dCustomer", row.dataset.customer);
    setField("dDate", row.dataset.date);
    setField("dTotal", "Rs. " + (row.dataset.total || "0"));
    setField("dStatus", row.dataset.status);
    setField("dPayment", row.dataset.payment);
    setField("dMethod", row.dataset.method);
    setField("dAddress", row.dataset.address);

    const modalElem = document.getElementById("orderModal");
    if (modalElem && typeof bootstrap !== "undefined" && bootstrap.Modal) {
        new bootstrap.Modal(modalElem).show();
    }
}
