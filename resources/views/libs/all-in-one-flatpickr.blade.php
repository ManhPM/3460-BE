<style>
    .flatpickr-btn-group {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        padding: 8px 10px;
        border-top: 1px solid #eee;
        margin-top: 4px;
    }

    .flatpickr-action-btn {
        padding: 4px 10px;
        font-size: 13px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        background-color: #f1f1f1;
        transition: background-color 0.2s ease;
    }

    .flatpickr-action-btn:hover {
        background-color: #e0e0e0;
    }

    .flatpickr-action-btn.clear-btn {
        color: #d9534f;
    }

    .flatpickr-action-btn.today-btn {
        color: #0275d8;
    }

    /* Tăng size input nếu bị bé */
    .flatpickr-time.input,
    input.flatpickr-time {
        min-height: 38px;
        padding: 8px 12px;
        font-size: 14px;
    }

    /* Sửa lỗi flatpickr popup thời gian hiển thị nhỏ */
    .flatpickr-time .flatpickr-time input,
    .flatpickr-time .flatpickr-time .numInput {
        height: 30px;
        font-size: 14px;
    }

    /* Nếu bạn muốn bo tròn và hover đẹp */
    input.flatpickr-time {
        border-radius: 6px;
        border: 1px solid #ccc;
        transition: border 0.2s ease;
    }

    input.flatpickr-time:focus {
        border-color: #007bff;
        outline: none;
    }
</style>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function attachTimeInputHandlers(instance) {
            if (!instance || !instance.calendarContainer) return;
            const container = instance.calendarContainer;
            const inputs = container.querySelectorAll('.flatpickr-time .numInput');
            if (!inputs || inputs.length === 0) return;

            inputs.forEach((input, idx) => {
                // Prevent duplicate listeners
                if (input._handlersAttached) return;
                input._handlersAttached = true;

                input.addEventListener('input', function(e) {
                    // Keep only digits and max 2 characters
                    const start = input.selectionStart;
                    const end = input.selectionEnd;
                    input.value = (input.value || '').replace(/\D/g, '').slice(0, 2);

                    // Auto-advance when 2 digits entered
                    if (input.value.length === 2) {
                        const next = inputs[idx + 1];
                        if (next) {
                            next.focus();
                            next.select();
                        }
                    }
                });

                input.addEventListener('keydown', function(e) {
                    // Move to previous when Backspace on empty at start
                    if (e.key === 'Backspace' && input.selectionStart === 0 && input
                        .selectionEnd === 0 && !input.value) {
                        const prev = inputs[idx - 1];
                        if (prev) {
                            e.preventDefault();
                            prev.focus();
                            prev.setSelectionRange(Math.max((prev.value || '').length, 0), Math
                                .max((prev.value || '').length, 0));
                        }
                    }
                });
            });
        }

        const commonOptions = {
            allowInput: true,
            wrap: false,
            clickOpens: true,

            onReady: function(selectedDates, dateStr, instance) {
                const btnWrapper = document.createElement('div');
                btnWrapper.classList.add('flatpickr-btn-group');

                // Nút Clear
                const clearBtn = document.createElement('button');
                clearBtn.textContent = '✕';
                clearBtn.type = 'button';
                clearBtn.classList.add('flatpickr-action-btn', 'clear-btn');
                clearBtn.onclick = () => instance.clear();

                // Nút Hôm nay
                const todayBtn = document.createElement('button');
                todayBtn.textContent = 'Hiện tại';
                todayBtn.type = 'button';
                todayBtn.classList.add('flatpickr-action-btn', 'today-btn');
                todayBtn.onclick = () => {
                    const now = new Date();
                    instance.setDate(now, true); // true = trigger change
                };

                btnWrapper.appendChild(clearBtn);
                btnWrapper.appendChild(todayBtn);

                instance.calendarContainer.appendChild(btnWrapper);

                // Attach handlers for time inputs (if present)
                attachTimeInputHandlers(instance);
            },
            onOpen: function(selectedDates, dateStr, instance) {
                // Ensure handlers are attached when calendar opens/rebuilds
                attachTimeInputHandlers(instance);
            }
        };

        // Chỉ ngày (per-input options: data-min-date, data-max-date)
        document.querySelectorAll('.flatpickr').forEach(function(el) {
            const minDate = el.getAttribute('data-min-date') || undefined;
            const maxDate = el.getAttribute('data-max-date') || undefined;
            flatpickr(el, {
                ...commonOptions,
                dateFormat: "d-m-Y",
                minDate,
                maxDate,
                disableMobile: true
            });
        });

        // Ngày + Giờ + Phút + Giây
        flatpickr(".flatpickr-dt", {
            ...commonOptions,
            enableTime: true,
            enableSeconds: true,
            time_24hr: true,
            dateFormat: "d-m-Y H:i:S",
            disableMobile: true
        });

        // Chỉ giờ phút giây
        flatpickr(".flatpickr-time", {
            ...commonOptions,
            enableTime: true,
            enableSeconds: true,
            noCalendar: true,
            time_24hr: true,
            dateFormat: "H:i:S",
            allowInput: true,
            disableMobile: true
        });
    });
</script>
