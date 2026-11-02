/**
 * StudySpot - JavaScript chính (assets/js/main.js)
 * Bao gồm:
 * 1. Tự động đóng thông báo Flash Message sau 5 giây
 * 2. Thêm / Bỏ Yêu thích bằng AJAX (fetch API) không reload trang kèm CSRF Token
 * 3. Hiển thị thông báo Toast góc màn hình
 * 4. Nút cuộn nhanh lên đầu trang (Back to Top)
 */
document.addEventListener('DOMContentLoaded', function () {
    // 1. Tự động ẩn thông báo Flash sau 5 giây
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alertEl) {
        setTimeout(function () {
            if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                if (bsAlert) {
                    bsAlert.close();
                }
            }
        }, 5000);
    });

    // 2. Xử lý nút Thêm / Bỏ Yêu thích bằng AJAX (fetch)
    const baseUrl = document.body.getAttribute('data-base-url') || '';
    const csrfToken = document.body.getAttribute('data-csrf') || '';

    document.addEventListener('click', async function (event) {
        const btn = event.target.closest('.btn-favorite-toggle');
        if (!btn) {
            return;
        }

        event.preventDefault();
        if (btn.disabled) {
            return;
        }

        const placeId = btn.getAttribute('data-place-id');
        if (!placeId) {
            return;
        }

        btn.disabled = true;

        try {
            const formData = new FormData();
            formData.append('place_id', placeId);
            formData.append('csrf_token', csrfToken);

            const response = await fetch(baseUrl + '/favorite/toggle', {
                method: 'POST',
                headers: {
                    'X-CSRF-Token': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const data = await response.json();

            if (response.status === 401 && data.require_login) {
                showStudySpotToast(data.message || 'Vui lòng đăng nhập để lưu quán yêu thích.', 'warning');
                setTimeout(function () {
                    window.location.href = data.login_url || (baseUrl + '/auth/login');
                }, 900);
                return;
            }

            if (!response.ok || !data.success) {
                showStudySpotToast(data.message || 'Không thể thực hiện thao tác.', 'danger');
                return;
            }

            // Cập nhật giao diện tất cả nút yêu thích của cùng place_id trên trang hiện tại
            const relatedButtons = document.querySelectorAll('.btn-favorite-toggle[data-place-id="' + placeId + '"]');
            relatedButtons.forEach(function (buttonEl) {
                const icon = buttonEl.querySelector('i');
                const label = buttonEl.querySelector('.fav-label');
                const withLabel = buttonEl.getAttribute('data-with-label') === '1';

                if (data.status === 'added') {
                    if (icon) {
                        icon.className = 'bi bi-heart-fill' + (withLabel ? ' me-1' : ' text-danger');
                    }
                    if (withLabel) {
                        buttonEl.classList.remove('btn-outline-danger');
                        buttonEl.classList.add('btn-danger');
                        if (label) label.textContent = 'Đã lưu yêu thích';
                    }
                } else {
                    if (icon) {
                        icon.className = 'bi bi-heart' + (withLabel ? ' me-1' : ' text-secondary');
                    }
                    if (withLabel) {
                        buttonEl.classList.remove('btn-danger');
                        buttonEl.classList.add('btn-outline-danger');
                        if (label) label.textContent = 'Lưu vào Yêu thích';
                    }

                    // Nếu đang ở trang Danh sách Yêu thích (/favorite) hoặc Tab Yêu thích (/profile) thì ẩn thẻ vừa bỏ lưu
                    if (buttonEl.getAttribute('data-remove-card') === '1') {
                        const col = buttonEl.closest('.favorite-item-col');
                        if (col) {
                            col.style.transition = 'opacity 0.25s ease';
                            col.style.opacity = '0';
                            setTimeout(function () {
                                col.remove();
                                const remaining = document.querySelectorAll('.favorite-item-col').length;
                                if (remaining === 0) {
                                    window.location.reload();
                                }
                            }, 250);
                        }
                    }
                }
            });

            showStudySpotToast(data.message, data.status === 'added' ? 'success' : 'secondary');
        } catch (err) {
            showStudySpotToast('Lỗi kết nối máy chủ khi cập nhật yêu thích.', 'danger');
        } finally {
            btn.disabled = false;
        }
    });

    // 3. Khởi tạo nút cuộn lên đầu trang (Back to Top)
    initBackToTopButton();
});

/**
 * Hiển thị thông báo Toast nhỏ góc dưới bên phải màn hình
 */
function showStudySpotToast(message, type = 'success') {
    let container = document.getElementById('studyspotToastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'studyspotToastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1080';
        document.body.appendChild(container);
    }

    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${type} border-0 shadow`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');

    toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body small fw-semibold">
                <i class="bi bi-check2-circle me-1"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Đóng"></button>
        </div>
    `;

    container.appendChild(toastEl);
    const bsToast = new bootstrap.Toast(toastEl, { delay: 2800 });
    bsToast.show();
    toastEl.addEventListener('hidden.bs.toast', function () {
        toastEl.remove();
    });
}

/**
 * Tạo nút cuộn nhanh lên đầu trang khi người dùng kéo trang xuống sâu
 */
function initBackToTopButton() {
    const btn = document.createElement('button');
    btn.id = 'btnBackToTop';
    btn.type = 'button';
    btn.className = 'btn btn-coffee';
    btn.title = 'Lên đầu trang';
    btn.innerHTML = '<i class="bi bi-arrow-up"></i>';
    document.body.appendChild(btn);

    window.addEventListener('scroll', function () {
        if (window.scrollY > 350) {
            btn.style.display = 'inline-flex';
        } else {
            btn.style.display = 'none';
        }
    });

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}
