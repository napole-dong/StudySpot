// StudySpot: flash, chọn sao, xem ảnh, yêu thích AJAX, tooltip, lên đầu trang, gợi ý tìm kiếm.
// Chạy khi DOMContentLoaded.


// Khởi tạo khi DOMContentLoaded: 8 hàm dưới đây.

document.addEventListener('DOMContentLoaded', function () {
    // 1. Tự động mờ dần và đóng thông báo Flash sau 4 giây
    initAutoDismissFlashMessages();

    // 2. Khởi tạo Widget chọn 5 ngôi sao đánh giá trực quan
    initInteractiveStarRatings();

    // 3. Khởi tạo tính năng Xem trước ảnh (Live Image Preview) cho mọi ô input[type="file"]
    initLiveImagePreview();

    // 4. Xử lý nút Thêm / Bỏ Yêu thích bằng AJAX (fetch)
    initFavoriteToggleAjax();

    // 5. Khởi tạo Bootstrap Tooltip (dùng cho các nút icon ở Admin & User)
    initBootstrapTooltips();

    // 6. Khởi tạo nút cuộn lên đầu trang (Back to Top)
    initBackToTopButton();

    // 7. Gợi ý tìm kiếm trực tiếp khi gõ vào ô tìm kiếm (header, hero, sidebar mobile)
    initSearchSuggestions();

    // 8. Ô lọc select: hover hiện đủ tên khi chữ dài bị cắt.
    initFilterSelectTitles();
});

// Flash: tự động mờ dần rồi đóng sau 4000ms, không phải 5000ms.
function initAutoDismissFlashMessages() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(function (alertEl) {
        setTimeout(function () {
            alertEl.style.transition = 'opacity 0.4s ease, transform 0.4s ease'; // Hiệu ứng mờ dần trong 0.4 giây trước khi đóng thanh flash.
            alertEl.style.opacity = '0';
            alertEl.style.transform = 'translateY(-6px)';
            setTimeout(function () {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alertEl);
                    if (bsAlert) {
                        bsAlert.close();
                        return;
                    }
                }
                alertEl.remove();
            }, 400);
        }, 4000);
    });
}

// Chọn sao: thay select bằng 5 sao, giữ nguyên name cho PHP.
function initInteractiveStarRatings() {
    const ratingSelects = document.querySelectorAll('select[name^="rating_"], select.js-star-rating');

    const ratingLabels = {
        1: '1/5 - Tệ',
        2: '2/5 - Tạm ổn',
        3: '3/5 - Khá',
        4: '4/5 - Tốt',
        5: '5/5 - Tuyệt vời'
    };

    ratingSelects.forEach(function (selectEl) {
        if (selectEl.dataset.starUpgraded === '1') { // Cơ chế chống lặp - select đã được nâng cấp thành widget sao thì bỏ qua.
            return;
        }
        selectEl.dataset.starUpgraded = '1';

        // Ẩn thẻ select gốc nhưng vẫn giữ trong DOM để form submit đúng name="..."
        selectEl.classList.add('d-none');

        const currentVal = parseInt(selectEl.value, 10) || 5;

        const widget = document.createElement('div');
        widget.className = 'star-rating-interactive d-flex align-items-center justify-content-between w-100';

        const starsGroup = document.createElement('div');
        starsGroup.className = 'd-inline-flex align-items-center gap-1';

        const scoreBadge = document.createElement('span');
        scoreBadge.className = 'star-score-badge text-end';
        scoreBadge.textContent = ratingLabels[currentVal] || (currentVal + '/5 sao');

        const starButtons = [];

        const renderStarsState = function (activeValue, hoverValue = 0) {
            const targetVal = hoverValue > 0 ? hoverValue : activeValue;
            starButtons.forEach(function (btn, idx) {
                const starNum = idx + 1;
                const icon = btn.querySelector('i');
                if (starNum <= targetVal) {
                    btn.classList.add(hoverValue > 0 ? 'is-hovered' : 'is-active');
                    if (hoverValue === 0) btn.classList.remove('is-hovered');
                    if (icon) icon.className = 'bi bi-star-fill';
                } else {
                    btn.classList.remove('is-active', 'is-hovered');
                    if (icon) icon.className = 'bi bi-star';
                }
            });
            scoreBadge.textContent = ratingLabels[targetVal] || (targetVal + '/5 sao');
        };

        for (let i = 1; i <= 5; i++) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'star-btn';
            btn.setAttribute('data-value', String(i));
            btn.setAttribute('aria-label', i + ' sao');
            btn.innerHTML = '<i class="bi bi-star-fill"></i>';

            btn.addEventListener('mouseenter', function () {
                renderStarsState(parseInt(selectEl.value, 10) || 5, i);
            });

            btn.addEventListener('click', function () {
                selectEl.value = String(i);
                selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                renderStarsState(i, 0);
            });

            starButtons.push(btn);
            starsGroup.appendChild(btn);
        }

        starsGroup.addEventListener('mouseleave', function () {
            renderStarsState(parseInt(selectEl.value, 10) || 5, 0);
        });

        renderStarsState(currentVal, 0);

        widget.appendChild(starsGroup);
        widget.appendChild(scoreBadge);
        selectEl.parentNode.insertBefore(widget, selectEl.nextSibling);
    });
}

// Xem ảnh: chọn file thì hiện ảnh xem trước, chưa tải lên server.
function initLiveImagePreview() {
    const fileInputs = document.querySelectorAll('input[type="file"]');

    fileInputs.forEach(function (inputEl) {
        inputEl.addEventListener('change', function () {
            // Nếu có sẵn thẻ img#avatarPreviewImg (ở trang sửa hồ sơ) thì cập nhật trực tiếp luôn, khỏi tạo thumbnail phụ
            const customAvatarPreview = document.getElementById('avatarPreviewImg');
            let previewContainer = null;
            if (!customAvatarPreview) {
                previewContainer = inputEl.parentElement.querySelector('.live-upload-preview');
                if (!previewContainer) {
                    previewContainer = document.createElement('div');
                    previewContainer.className = 'live-upload-preview';
                    inputEl.parentElement.appendChild(previewContainer);
                }
                previewContainer.innerHTML = '';
            }

            const files = Array.from(inputEl.files || []);
            if (files.length === 0) {
                return;
            }

            const fileNameEl = document.getElementById('avatarFileName');
            if (fileNameEl && files[0]) {
                fileNameEl.textContent = files[0].name;
            }

            files.forEach(function (file, index) {
                if (!file.type.startsWith('image/')) {
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    const dataUrl = e.target.result;

                    if (customAvatarPreview && index === 0) {
                        customAvatarPreview.src = dataUrl;
                    }

                    if (!previewContainer) {
                        return;
                    }
                    const item = document.createElement('div');
                    item.className = 'live-upload-preview-item';
                    item.title = file.name;

                    const img = document.createElement('img');
                    img.src = dataUrl;
                    img.alt = 'Xem trước ảnh tải lên';

                    item.appendChild(img);
                    previewContainer.appendChild(item);
                };
                reader.readAsDataURL(file);
            });
        });
    });
}

// Yêu thích AJAX: bấm tim gửi POST /favorite/toggle, đổi tim không tải lại trang.
function initFavoriteToggleAjax() {
    const baseUrl = document.body.getAttribute('data-base-url') || ''; // baseUrl do PHP in sẵn vào thuộc tính data-base-url trên thẻ body.
    const csrfToken = document.body.getAttribute('data-csrf') || ''; // CSRF token do PHP in sẵn vào thuộc tính data-csrf trên thẻ body.

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

            const response = await fetch(baseUrl + '/favorite/toggle', { // POST /favorite/toggle, kèm CSRF token trong cả FormData và header X-CSRF-Token.
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
                }, 900); // Đợi 900ms cho người dùng đọc toast rồi mới chuyển sang trang đăng nhập.
                return;
            }

            if (!response.ok || !data.success) {
                showStudySpotToast(data.message || 'Không thể thực hiện thao tác.', 'danger');
                return;
            }

            // Cập nhật trạng thái tất cả nút yêu thích của cùng place_id trên trang hiện tại
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
}

// Toast: hiện hộp góc phải dưới, tự ẩn sau 4000ms.
// Cảnh báo: message toast ghép thẳng vào HTML, chỉ truyền chữ từ server tin cậy.
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
    toastEl.className = `toast align-items-center text-bg-${type} border-0 shadow-lg rounded-3`;
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
    const bsToast = new bootstrap.Toast(toastEl, { delay: 4000 });
    bsToast.show();
    toastEl.addEventListener('hidden.bs.toast', function () {
        toastEl.remove();
    });
}

// Tooltip: bật tooltip Bootstrap cho nút icon có data-bs-toggle.
function initBootstrapTooltips() {
    if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) {
        return;
    }
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });
}

// Lên đầu trang: hiện khi cuộn quá 350px, bấm cuộn mượt lên đầu.
function initBackToTopButton() {
    const btn = document.createElement('button');
    btn.id = 'btnBackToTop';
    btn.type = 'button';
    btn.className = 'btn btn-coffee';
    btn.title = 'Lên đầu trang';
    btn.innerHTML = '<i class="bi bi-arrow-up"></i>';
    document.body.appendChild(btn);

    window.addEventListener('scroll', function () {
        if (window.scrollY > 350) { // Ngưỡng 350px - cuộn qua 350px mới hiện nút, cuộn lên trên thì ẩn nút.
            btn.style.display = 'inline-flex';
        } else {
            btn.style.display = 'none';
        }
    });

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Cuộn mượt (smooth) về đầu trang, không nhảy đột ngột.
    });
}

// Gợi ý tìm kiếm: gõ từ 2 ký tự, đợi 250ms rồi gọi GET /place/suggest, chỉ ô header/hero/sidebar mobile.
function initSearchSuggestions() {
    const baseUrl = document.body.getAttribute('data-base-url') || ''; // baseUrl do PHP in sẵn vào thuộc tính data-base-url trên thẻ body.
    const inputs = document.querySelectorAll('header input[name="q"], .hero-split input[name="q"], .offcanvas input[name="q"]');
    inputs.forEach(function (input) {
        const group = input.closest('.input-group');
        if (!group) return;
        const box = document.createElement('div');
        box.className = 'search-suggest';
        box.hidden = true;
        group.appendChild(box);
        let timer = null;
        let reqId = 0;
        let activeIdx = -1;
        function hide() { box.hidden = true; box.innerHTML = ''; activeIdx = -1; }
        function esc(text) { const d = document.createElement('div'); d.textContent = text; return d.innerHTML; }
        input.addEventListener('input', function () {
            const q = input.value.trim();
            if (timer) clearTimeout(timer);
            if (q.length < 2) { hide(); return; }
            timer = setTimeout(async function () {
                const myId = ++reqId;
                try {
                    const res = await fetch(baseUrl + '/place/suggest?q=' + encodeURIComponent(q)); // GET /place/suggest, chỉ đọc gợi ý nên không cần CSRF token.
                    const data = await res.json();
                    if (myId !== reqId) return;
                    if (!data.items || data.items.length === 0) {
                        box.innerHTML = '<div class="search-suggest-empty">Không tìm thấy quán phù hợp</div>';
                    } else {
                        let html = data.items.map(function (it) {
                            return '<a class="search-suggest-item" href="' + esc(it.url) + '"><span><strong>' + esc(it.name) + '</strong><small>' + esc(it.district) + ' • ' + esc(it.category) + '</small></span><span class="search-suggest-rating">★ ' + Number(it.rating).toFixed(1) + '</span></a>';
                        }).join('');
                        html += '<a class="search-suggest-all" href="' + esc(baseUrl + '/place?q=' + encodeURIComponent(q)) + '">Xem tất cả ' + data.total + ' kết quả cho "' + esc(q) + '" →</a>';
                        box.innerHTML = html;
                    }
                    box.hidden = false;
                    activeIdx = -1;
                } catch (e) { hide(); }
            }, 250);
        });
        input.addEventListener('keydown', function (e) {
            if (box.hidden) return;
            const items = box.querySelectorAll('.search-suggest-item');
            if (e.key === 'Escape') { hide(); input.blur(); }
            if (e.key === 'ArrowDown' && items.length) { e.preventDefault(); activeIdx = (activeIdx + 1) % items.length; items.forEach(function (el, i) { el.classList.toggle('active', i === activeIdx); }); }
            if (e.key === 'ArrowUp' && items.length) { e.preventDefault(); activeIdx = (activeIdx - 1 + items.length) % items.length; items.forEach(function (el, i) { el.classList.toggle('active', i === activeIdx); }); }
            if (e.key === 'Enter' && activeIdx >= 0 && items[activeIdx]) { e.preventDefault(); window.location.href = items[activeIdx].href; }
        });
        input.addEventListener('blur', function () { setTimeout(hide, 150); }); // Đợi 150ms sau khi rời khỏi ô tìm kiếm rồi mới đóng hộp gợi ý.
        document.addEventListener('click', function (e) { if (!group.contains(e.target)) hide(); });
    });
}

// Tiêu đề lọc: gán title cho select lọc bằng lựa chọn hiện tại, để hover xem đủ chữ dài bị cắt.
function initFilterSelectTitles() {
    document.querySelectorAll('.filter-bar .form-select').forEach(function (sel) {
        function sync() {
            var opt = sel.options[sel.selectedIndex];
            sel.title = opt ? opt.text : '';
        }
        sel.addEventListener('change', sync);
        sync();
    });
}
