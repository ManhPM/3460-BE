<div class="d-flex justify-content-between align-items-center mb-2">
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
        <i class="ti ti-plus"></i> {{ __('Thêm sản phẩm') }}
    </button>
    <div class="text-muted small">{{ __('Quản lý sản phẩm trong chương trình flash sale') }}</div>
</div>
<style>
    #tableProduct th,
    #tableProduct td {
        text-align: center !important;
        vertical-align: middle !important;
    }

    #tableProduct .form-control {
        text-align: center !important;
        margin: 0 auto;
    }

    #tableProduct .input-group {
        justify-content: center;
    }

    #tableProduct .input-group .form-control {
        text-align: center !important;
    }

    /* Row add highlight animation */
    @keyframes flashAdd {
        0% {
            background-color: #e6ffed;
        }

        100% {
            background-color: transparent;
        }
    }

    #tableProduct tbody tr.added-animate {
        animation: flashAdd 1.2s ease-out;
    }

    /* Row remove highlight animation */
    @keyframes flashRemove {
        0% {
            background-color: #ffe6e6;
        }

        100% {
            background-color: transparent;
        }
    }

    #tableProduct tbody tr.removing-animate {
        animation: flashRemove 0.3s ease-in;
    }
</style>
<table id="tableProduct" class="table table-sm align-middle text-center">
    <thead>
        <tr>
            <th class="text-center" style="width: 1%;"></th>
            <th class="text-center" style="width: 45%;">{{ __('Sản phẩm') }}</th>
            <th class="text-center" style="width: 18%;">{{ __('Số lượng flash sale') }}</th>
            <th class="text-center" style="width: 18%;">{{ __('Đã bán') }}</th>
            <th class="text-center" style="width: 18%;">{{ __('Giá flash sale') }}</th>
        </tr>
    </thead>
    <tbody>
        @each('admin.flash_sales.partials.item-product', $instance->details ?? [], 'flash_sale_detail', 'admin.flash_sales.partials.no-item-product')
    </tbody>
</table>
