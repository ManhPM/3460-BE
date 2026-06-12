<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Tắt kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Danh sách các bảng cần xóa dữ liệu
        $tables = ['role_has_permissions', 'model_has_permissions', 'permissions', 'roles', 'modules'];

        // Xóa toàn bộ dữ liệu trong các bảng
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        // Bật lại kiểm tra khóa ngoại
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Tạo các vai trò (roles)
        DB::table('roles')->insert([
            'id' => 1,
            'title' => 'Super Admin',
            'name' => 'superAdmin',
            'guard_name' => 'admin',
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()')
        ]);

        DB::table('roles')->insert([
            'id' => 2,
            'title' => 'Khách hàng',
            'name' => 'customer',
            'guard_name' => 'web',
            'created_at' => DB::raw('NOW()'),
            'updated_at' => DB::raw('NOW()')
        ]);

        // ============================================
        // PERMISSIONS KHÔNG THUỘC MODULE (Settings & System)
        // ============================================
        $systemPermissions = [
            ['title' => 'Đọc tài liệu API', 'name' => 'readAPIDoc'],
            ['title' => 'Cài đặt Miniapp', 'name' => 'settingMiniapp'],
            ['title' => 'Cài đặt Chung', 'name' => 'settingGeneral'],
            ['title' => 'Cài đặt Theme', 'name' => 'settingTheme'],
            ['title' => 'Cài đặt Cấu hình', 'name' => 'settingConfig'],
            ['title' => 'Cài đặt Chân trang', 'name' => 'settingFooter'],
            ['title' => 'Cài đặt Liên hệ', 'name' => 'settingContact'],
            ['title' => 'Cài đặt Giới thiệu', 'name' => 'settingInformation'],
            ['title' => 'Cài đặt Hạng thành viên', 'name' => 'settingMembershipLevel'],
            ['title' => 'Dashboard', 'name' => 'mevivuDev'],
        ];

        foreach ($systemPermissions as $permission) {
            DB::table('permissions')->insert([
                'title' => $permission['title'],
                'name' => $permission['name'],
                'guard_name' => 'admin',
                'module_id' => null,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ]);
        }

        // ============================================
        // ĐỊNH NGHĨA MODULES
        // ============================================
        $modules = [
            ['name' => 'QL Dashboard', 'description' => '<p>Quản lý Dashboard</p>', 'key' => 'Dashboard'],
            ['name' => 'QL Hạng thành viên', 'description' => '<p>Quản lý Hạng thành viên</p>', 'key' => 'MembershipLevel'],
            ['name' => 'QL Giá giao hàng', 'description' => '<p>Quản lý Giá giao hàng</p>', 'key' => 'ShippingRates'],
            ['name' => 'QL Ngân hàng', 'description' => '<p>Quản lý Ngân hàng</p>', 'key' => 'Bank'],
            ['name' => 'QL Thông báo', 'description' => '<p>Quản lý Thông báo</p>', 'key' => 'Notification'],
            ['name' => 'QL Voucher', 'description' => '<p>Quản lý Voucher</p>', 'key' => 'Voucher'],
            ['name' => 'QL Chương trình phát voucher', 'description' => '<p>Quản lý Chương trình phát voucher</p>', 'key' => 'VoucherProgram'],
            ['name' => 'QL Mã giảm giá', 'description' => '<p>Quản lý Mã giảm giá</p>', 'key' => 'DiscountCode'],
            ['name' => 'QL Giao dịch', 'description' => '<p>Quản lý Giao dịch</p>', 'key' => 'Transaction'],
            ['name' => 'QL Đơn hàng', 'description' => '<p>Quản lý Đơn hàng</p>', 'key' => 'Order'],
            ['name' => 'QL Sản phẩm', 'description' => '<p>Quản lý Sản phẩm</p>', 'key' => 'Product'],
            ['name' => 'QL Danh mục Sản phẩm', 'description' => '<p>Quản lý Danh mục Sản phẩm</p>', 'key' => 'ProductCategory'],
            ['name' => 'QL Thuộc tính Sản phẩm', 'description' => '<p>Quản lý Thuộc tính Sản phẩm</p>', 'key' => 'ProductAttribute'],
            ['name' => 'QL Khách hàng', 'description' => '<p>Quản lý Khách hàng</p>', 'key' => 'User'],
            ['name' => 'QL FlashSale', 'description' => '<p>Quản lý FlashSale</p>', 'key' => 'FlashSale'],
            ['name' => 'QL Đánh giá', 'description' => '<p>Quản lý Đánh giá</p>', 'key' => 'Review'],
            ['name' => 'QL Bài viết', 'description' => '<p>Quản lý Bài viết</p>', 'key' => 'Post'],
            ['name' => 'QL Danh mục Bài viết', 'description' => '<p>Quản lý Danh mục Bài viết</p>', 'key' => 'PostCategory'],
            ['name' => 'QL Sliders', 'description' => '<p>Quản lý Sliders</p>', 'key' => 'Slider'],
            ['name' => 'QL Vai trò', 'description' => '<p>Quản lý Vai trò</p>', 'key' => 'Role'],
            ['name' => 'QL Admin', 'description' => '<p>Quản lý Admin</p>', 'key' => 'Admin'],
            ['name' => 'QL Section trang chủ', 'description' => '<p>Quản lý Section trang chủ</p>', 'key' => 'Section'],
        ];

        // Vietnamese translations for module names
        $moduleTranslations = [
            'Dashboard' => 'Dashboard',
            'MembershipLevel' => 'Hạng thành viên',
            'ShippingRates' => 'Giá giao hàng',
            'Bank' => 'Ngân hàng',
            'Notification' => 'Thông báo',
            'Voucher' => 'Voucher',
            'VoucherProgram' => 'Chương trình phát voucher',
            'DiscountCode' => 'Mã giảm giá',
            'Transaction' => 'Giao dịch',
            'Order' => 'Đơn hàng',
            'Product' => 'Sản phẩm',
            'ProductCategory' => 'Danh mục sản phẩm',
            'ProductAttribute' => 'Thuộc tính sản phẩm',
            'User' => 'Khách hàng',
            'FlashSale' => 'FlashSale',
            'Review' => 'Đánh giá',
            'Post' => 'Bài viết',
            'PostCategory' => 'Danh mục bài viết',
            'Slider' => 'Sliders',
            'Role' => 'Vai trò',
            'Admin' => 'Admin',
            'Section' => 'Section trang chủ',
        ];

        // Insert modules and build module map
        $moduleMap = [];
        foreach ($modules as $module) {
            DB::table('modules')->insert([
                'name' => $module['name'],
                'description' => $module['description'],
                'status' => 2,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()')
            ]);
            $moduleMap[$module['key']] = DB::getPdo()->lastInsertId();
        }

        // ============================================
        // ĐỊNH NGHĨA PERMISSIONS CHO TỪNG MODULE
        // ============================================
        $modulePermissions = [
            // Dashboard
            'Dashboard' => [
                ['title' => 'Xem Dashboard', 'name' => 'viewDashboard'],
            ],

            // MembershipLevel
            'MembershipLevel' => [
                ['title' => 'Thêm Hạng thành viên', 'name' => 'createMembershipLevel'],
                ['title' => 'Xem Hạng thành viên', 'name' => 'viewMembershipLevel'],
                ['title' => 'Sửa Hạng thành viên', 'name' => 'updateMembershipLevel'],
                ['title' => 'Xóa Hạng thành viên', 'name' => 'deleteMembershipLevel'],
            ],

            // ShippingRates
            'ShippingRates' => [
                ['title' => 'Thêm Giá giao hàng', 'name' => 'createShippingRates'],
                ['title' => 'Xem Giá giao hàng', 'name' => 'viewShippingRates'],
                ['title' => 'Sửa Giá giao hàng', 'name' => 'updateShippingRates'],
                ['title' => 'Xóa Giá giao hàng', 'name' => 'deleteShippingRates'],
            ],

            // Bank
            'Bank' => [
                ['title' => 'Thêm Ngân hàng', 'name' => 'createBank'],
                ['title' => 'Xem Ngân hàng', 'name' => 'viewBank'],
                ['title' => 'Sửa Ngân hàng', 'name' => 'updateBank'],
                ['title' => 'Xóa Ngân hàng', 'name' => 'deleteBank'],
            ],

            // Notification
            'Notification' => [
                ['title' => 'Thêm Thông báo', 'name' => 'createNotification'],
                ['title' => 'Xem Thông báo', 'name' => 'viewNotification'],
                ['title' => 'Sửa Thông báo', 'name' => 'updateNotification'],
                ['title' => 'Xóa Thông báo', 'name' => 'deleteNotification'],
            ],

            // Voucher
            'Voucher' => [
                ['title' => 'Thêm Voucher', 'name' => 'createVoucher'],
                ['title' => 'Xem Voucher', 'name' => 'viewVoucher'],
                ['title' => 'Sửa Voucher', 'name' => 'updateVoucher'],
                ['title' => 'Xóa Voucher', 'name' => 'deleteVoucher'],
            ],

            // VoucherProgram
            'VoucherProgram' => [
                ['title' => 'Thêm Chương trình phát voucher', 'name' => 'createVoucherProgram'],
                ['title' => 'Xem Chương trình phát voucher', 'name' => 'viewVoucherProgram'],
                ['title' => 'Sửa Chương trình phát voucher', 'name' => 'updateVoucherProgram'],
                ['title' => 'Xóa Chương trình phát voucher', 'name' => 'deleteVoucherProgram'],
            ],

            // DiscountCode
            'DiscountCode' => [
                ['title' => 'Thêm Mã giảm giá', 'name' => 'createDiscountCode'],
                ['title' => 'Xem Mã giảm giá', 'name' => 'viewDiscountCode'],
                ['title' => 'Sửa Mã giảm giá', 'name' => 'updateDiscountCode'],
                ['title' => 'Xóa Mã giảm giá', 'name' => 'deleteDiscountCode'],
            ],

            // Transaction
            'Transaction' => [
                ['title' => 'Thêm Giao dịch', 'name' => 'createTransaction'],
                ['title' => 'Xem Giao dịch', 'name' => 'viewTransaction'],
                ['title' => 'Sửa Giao dịch', 'name' => 'updateTransaction'],
                ['title' => 'Xóa Giao dịch', 'name' => 'deleteTransaction'],
            ],

            // Order
            'Order' => [
                ['title' => 'Thêm Đơn hàng', 'name' => 'createOrder'],
                ['title' => 'Xem Đơn hàng', 'name' => 'viewOrder'],
                ['title' => 'Sửa Đơn hàng', 'name' => 'updateOrder'],
                ['title' => 'Xóa Đơn hàng', 'name' => 'deleteOrder'],
            ],

            // Product
            'Product' => [
                ['title' => 'Thêm Sản phẩm', 'name' => 'createProduct'],
                ['title' => 'Xem Sản phẩm', 'name' => 'viewProduct'],
                ['title' => 'Sửa Sản phẩm', 'name' => 'updateProduct'],
                ['title' => 'Xóa Sản phẩm', 'name' => 'deleteProduct'],
            ],

            // ProductCategory
            'ProductCategory' => [
                ['title' => 'Thêm Danh mục sản phẩm', 'name' => 'createProductCategory'],
                ['title' => 'Xem Danh mục sản phẩm', 'name' => 'viewProductCategory'],
                ['title' => 'Sửa Danh mục sản phẩm', 'name' => 'updateProductCategory'],
                ['title' => 'Xóa Danh mục sản phẩm', 'name' => 'deleteProductCategory'],
            ],

            // ProductAttribute
            'ProductAttribute' => [
                ['title' => 'Thêm Thuộc tính sản phẩm', 'name' => 'createProductAttribute'],
                ['title' => 'Xem Thuộc tính sản phẩm', 'name' => 'viewProductAttribute'],
                ['title' => 'Sửa Thuộc tính sản phẩm', 'name' => 'updateProductAttribute'],
                ['title' => 'Xóa Thuộc tính sản phẩm', 'name' => 'deleteProductAttribute'],
            ],

            // User
            'User' => [
                ['title' => 'Thêm Khách hàng', 'name' => 'createUser'],
                ['title' => 'Xem Khách hàng', 'name' => 'viewUser'],
                ['title' => 'Sửa Khách hàng', 'name' => 'updateUser'],
                ['title' => 'Xóa Khách hàng', 'name' => 'deleteUser'],
            ],

            // FlashSale
            'FlashSale' => [
                ['title' => 'Thêm FlashSale', 'name' => 'createFlashSale'],
                ['title' => 'Xem FlashSale', 'name' => 'viewFlashSale'],
                ['title' => 'Sửa FlashSale', 'name' => 'updateFlashSale'],
                ['title' => 'Xóa FlashSale', 'name' => 'deleteFlashSale'],
            ],

            // Review
            'Review' => [
                ['title' => 'Thêm Đánh giá', 'name' => 'createReview'],
                ['title' => 'Xem Đánh giá', 'name' => 'viewReview'],
                ['title' => 'Sửa Đánh giá', 'name' => 'updateReview'],
                ['title' => 'Xóa Đánh giá', 'name' => 'deleteReview'],
            ],

            // Post
            'Post' => [
                ['title' => 'Thêm Bài viết', 'name' => 'createPost'],
                ['title' => 'Xem Bài viết', 'name' => 'viewPost'],
                ['title' => 'Sửa Bài viết', 'name' => 'updatePost'],
                ['title' => 'Xóa Bài viết', 'name' => 'deletePost'],
            ],

            // PostCategory
            'PostCategory' => [
                ['title' => 'Thêm Danh mục bài viết', 'name' => 'createPostCategory'],
                ['title' => 'Xem Danh mục bài viết', 'name' => 'viewPostCategory'],
                ['title' => 'Sửa Danh mục bài viết', 'name' => 'updatePostCategory'],
                ['title' => 'Xóa Danh mục bài viết', 'name' => 'deletePostCategory'],
            ],

            // Slider
            'Slider' => [
                ['title' => 'Thêm Sliders', 'name' => 'createSlider'],
                ['title' => 'Xem Sliders', 'name' => 'viewSlider'],
                ['title' => 'Sửa Sliders', 'name' => 'updateSlider'],
                ['title' => 'Xóa Sliders', 'name' => 'deleteSlider'],
            ],

            // Role
            'Role' => [
                ['title' => 'Thêm Vai trò', 'name' => 'createRole'],
                ['title' => 'Xem Vai trò', 'name' => 'viewRole'],
                ['title' => 'Sửa Vai trò', 'name' => 'updateRole'],
                ['title' => 'Xóa Vai trò', 'name' => 'deleteRole'],
            ],

            // Admin
            'Admin' => [
                ['title' => 'Thêm Admin', 'name' => 'createAdmin'],
                ['title' => 'Xem Admin', 'name' => 'viewAdmin'],
                ['title' => 'Sửa Admin', 'name' => 'updateAdmin'],
                ['title' => 'Xóa Admin', 'name' => 'deleteAdmin'],
            ],

            // Section
            'Section' => [
                ['title' => 'Thêm Section trang chủ', 'name' => 'createSection'],
                ['title' => 'Xem Section trang chủ', 'name' => 'viewSection'],
                ['title' => 'Sửa Section trang chủ', 'name' => 'updateSection'],
                ['title' => 'Xóa Section trang chủ', 'name' => 'deleteSection'],
            ],
        ];

        // Insert permissions for each module
        $allPermissionIds = [];

        foreach ($modulePermissions as $moduleKey => $permissions) {
            if (!isset($moduleMap[$moduleKey])) {
                continue;
            }

            $moduleId = $moduleMap[$moduleKey];

            foreach ($permissions as $permission) {
                DB::table('permissions')->insert([
                    'title' => $permission['title'],
                    'name' => $permission['name'],
                    'guard_name' => 'admin',
                    'module_id' => $moduleId,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()')
                ]);

                $allPermissionIds[] = DB::getPdo()->lastInsertId();
            }
        }

        // Thêm system permissions vào danh sách
        $systemPermissionIds = DB::table('permissions')
            ->whereNull('module_id')
            ->pluck('id')
            ->toArray();

        $allPermissionIds = array_merge($allPermissionIds, $systemPermissionIds);

        // ============================================
        // GÁN PERMISSIONS CHO SUPER ADMIN ROLE
        // ============================================
        $superAdminRoleId = DB::table('roles')->where('name', 'superAdmin')->value('id');

        if ($superAdminRoleId) {
            // Gán role_has_permissions cho Super Admin
            foreach ($allPermissionIds as $permissionId) {
                DB::table('role_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'role_id' => $superAdminRoleId
                ]);
            }

            // Gán model_has_permissions cho Super Admin (admin với ID 1)
            foreach ($allPermissionIds as $permissionId) {
                DB::table('model_has_permissions')->insert([
                    'permission_id' => $permissionId,
                    'model_type' => 'App\Models\Admin', // Điều chỉnh nếu namespace khác
                    'model_id' => 1
                ]);
            }
        }
    }
}
