<div x-data="confirmModal({
    confirmText: @js($confirmText ?? 'Xác nhận'),
    cancelText: @js($cancelText ?? 'Hủy'),
    loadingText: @js($loadingText ?? 'Đang xử lý...'),
})" x-cloak>
    <!-- Modal -->
    <div class="modal-overlay" x-show="open" x-transition.opacity @keydown.escape.window="close()" @click="close()">
        <div class="modal-content" @click.stop x-transition.scale.origin.center role="dialog" aria-modal="true">
            <h3 class="modal-title" x-text="title"></h3>
            <p class="modal-message" x-text="message"></p>

            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" :disabled="loading" @click="close()"
                    x-text="cancelText"></button>

                <button type="button" class="btn" :class="confirmVariantClass" :disabled="loading"
                    @click="handleConfirm()" x-text="loading ? loadingText : confirmText"></button>
            </div>
        </div>
    </div>

    <style>
        .modal-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(0, 0, 0, .5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 16px;
        }

        .modal-content {
            background-color: #ffffff;
            border-radius: 14px;
            padding: 24px;
            max-width: 340px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .2);
        }

        .modal-title {
            margin: 0 0 12px 0;
            font-weight: 600;
            font-size: 18px;
            line-height: 24px;
            text-align: center;
            color: #000;
        }

        .modal-message {
            margin: 0 0 24px 0;
            font-weight: 400;
            font-size: 14px;
            line-height: 20px;
            text-align: center;
            color: #666;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
        }

        .modal-actions>* {
            flex: 1;
        }

        /* Basic buttons; swap with your own design system if available */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-ghost {
            background: #f3f4f6;
            color: #111827;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn[disabled] {
            opacity: .65;
            pointer-events: none;
        }
    </style>

    <script>
        document.addEventListener('alpine:init', () => {
            window.confirmModal = (defaults = {}) => ({
                open: false,
                loading: false,
                title: '',
                message: '',
                confirmText: defaults.confirmText || 'Xác nhận',
                cancelText: defaults.cancelText || 'Hủy',
                loadingText: defaults.loadingText || 'Đang xử lý...',
                confirmVariant: 'danger',
                onConfirm: null,
                get confirmVariantClass() {
                    return this.confirmVariant === 'primary' ? 'btn-primary' : 'btn-danger'
                },
                show(options = {}) {
                    this.title = options.title || ''
                    this.message = options.message || ''
                    this.onConfirm = options.onConfirm || null
                    this.confirmVariant = options.confirmVariant || 'danger'
                    if (options.confirmText) this.confirmText = options.confirmText
                    if (options.cancelText) this.cancelText = options.cancelText
                    if (options.loadingText) this.loadingText = options.loadingText
                    this.open = true
                    this.loading = false
                },
                close() {
                    if (this.loading) return
                    this.open = false
                },
                async handleConfirm() {
                    if (typeof this.onConfirm !== 'function') {
                        this.close()
                        return
                    }
                    try {
                        const result = this.onConfirm()
                        if (result && typeof result.then === 'function') {
                            this.loading = true
                            await result
                        }
                    } finally {
                        this.loading = false
                        this.close()
                    }
                },
            })
        })

        function confirmModal(defaults) {
            // Instance factory for this component root
            if (!window.confirmModal) return {}
            return window.confirmModal(defaults)
        }
    </script>
</div>
