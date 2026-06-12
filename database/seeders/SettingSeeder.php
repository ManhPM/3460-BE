<?php

namespace Database\Seeders;

use App\Enums\Setting\SettingGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\Setting\SettingTypeInput;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->truncate();
        DB::table('settings')->insert([
            //theme mobile
            [
                'setting_key' => 'mobile_theme_color',
                'setting_name' => 'Màu chủ đạo của app',
                'plain_value' => '#1c5639',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::MobileTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'mobile_logo',
                'setting_name' => 'Logo của app',
                'plain_value' => 'public/assets/images/light-logo.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::MobileTheme,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6',
            ],
            //theme website
            [
                'setting_key' => 'web_theme_color',
                'setting_name' => 'Màu chủ đạo của trang web',
                'plain_value' => '#1c5639',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'site_name',
                'setting_name' => 'Tên site',
                'plain_value' => 'Mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-world',
                'class' => 'col-md-6',
            ],
            // WebTheme Home
            [
                'setting_key' => 'topbar_title',
                'setting_name' => 'Tiêu đề top bar trên header',
                'plain_value' => 'Miễn phí vận chuyển trong ngày của Mevivu.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6'
            ],
            [
                'setting_key' => 'home_title',
                'setting_name' => 'Tiêu đề trang chủ',
                'plain_value' => 'AppMart',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-home',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'home_short_desc',
                'setting_name' => 'Thẻ meta description trang chủ',
                'plain_value' => 'Xuân Shop là giải pháp mini app bán hàng online giúp bạn dễ dàng mua sắm, đặt hàng và quản lý đơn hàng trên Zalo.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            // WebTheme Information
            [
                'setting_key' => 'information_title',
                'setting_name' => 'Tiêu đề trang giới thiệu',
                'plain_value' => 'AppMart',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-info-circle',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'information_meta_desc',
                'setting_name' => 'Thẻ meta description trang giới thiệu',
                'plain_value' => 'Xuân Shop là giải pháp mini app bán hàng online giúp bạn dễ dàng mua sắm, đặt hàng và quản lý đơn hàng trên Zalo.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            // WebTheme Product
            [
                'setting_key' => 'product_title',
                'setting_name' => 'Tiêu đề trang sản phẩm',
                'plain_value' => 'Danh mục sản phẩm',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-shopping-cart',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'product_meta_desc',
                'setting_name' => 'Thẻ meta description trang sản phẩm',
                'plain_value' => 'Danh mục sản phẩm - Mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            // WebTheme Contact
            [
                'setting_key' => 'contact_title',
                'setting_name' => 'Tiêu đề trang liên hệ',
                'plain_value' => 'Liên hệ',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-phone',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'contact_meta_desc',
                'setting_name' => 'Thẻ meta description trang liên hệ',
                'plain_value' => 'Liên hệ - Mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            // WebTheme Sale
            [
                'setting_key' => 'sale_title',
                'setting_name' => 'Tiêu đề trang khuyến mãi',
                'plain_value' => 'Flash Sale',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-discount',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'sale_meta_desc',
                'setting_name' => 'Thẻ meta description trang khuyến mãi',
                'plain_value' => 'Flash Sale - Mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            // WebTheme Post
            [
                'setting_key' => 'post_title',
                'setting_name' => 'Tiêu đề trang tin tức',
                'plain_value' => 'Tin tức',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-news',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'post_meta_desc',
                'setting_name' => 'Thẻ meta description trang tin tức',
                'plain_value' => 'Tin tức - Mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'banner_flash',
                'setting_name' => 'Banner trang flash sale',
                'plain_value' => '/userfiles/images/qr/bg-flash-sale.jpg',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'banner_home_1',
                'setting_name' => 'Banner trang chủ 1',
                'plain_value' => 'userfiles/images/qr/banner-home-1.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'banner_home_2',
                'setting_name' => 'Banner trang chủ 2',
                'plain_value' => 'userfiles/images/qr/banner-home-2.jpg',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'slider_side_image_1',
                'setting_name' => 'Ảnh bên phải slider 1',
                'plain_value' => '/userfiles/images/banner-home2-01.jpg',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'slider_side_image_2',
                'setting_name' => 'Ảnh ảnh bên phải slider 2',
                'plain_value' => '/userfiles/images/banner-home2-02.jpg',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::WebTheme,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6',
            ],
            //theme cms
            [
                'setting_key' => 'bg_color',
                'setting_name' => 'Màu nền chung',
                'plain_value' => '#ffffff',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'top_sidebar_text_color',
                'setting_name' => 'Màu chữ top sidebar',
                'plain_value' => '#000000',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'top_sidebar_bg_color_1',
                'setting_name' => 'Màu top sidebar 1',
                'plain_value' => '#e8edf1',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'top_sidebar_bg_color_2',
                'setting_name' => 'Màu top sidebar 2',
                'plain_value' => '#ffd166',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'left_sidebar_text_color',
                'setting_name' => 'Màu chữ left sidebar',
                'plain_value' => '#ffffff',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'left_sidebar_selected_color',
                'setting_name' => 'Màu left sidebar khi được chọn',
                'plain_value' => '#B8E4E4',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'left_sidebar_selected_text_color',
                'setting_name' => 'Màu chữ left sidebar khi được chọn',
                'plain_value' => '#383838',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'left_sidebar_bg_color_1',
                'setting_name' => 'Màu left sidebar 1',
                'plain_value' => '#141618',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'left_sidebar_bg_color_2',
                'setting_name' => 'Màu left sidebar 2',
                'plain_value' => '#0c367a',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'breadcrumbs_text_color',
                'setting_name' => 'Màu chữ breadcrumbs',
                'plain_value' => '#ffffff',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'breadcrumbs_bg_color_1',
                'setting_name' => 'Màu breadcrumbs 1',
                'plain_value' => '#141618',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'breadcrumbs_bg_color_2',
                'setting_name' => 'Màu breadcrumbs 2',
                'plain_value' => '#0c367a',
                'type_input' => SettingTypeInput::Color,
                'group' => SettingGroup::CMSTheme,
                'icon' => 'ti ti-palette',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'light_site_logo',
                'setting_name' => 'Light Logo',
                'plain_value' => '/public/assets/images/light-hori-logo.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'site_logo',
                'setting_name' => 'Dark Logo',
                'plain_value' => '/public/assets/images/light-hori-logo.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'site_icon',
                'setting_name' => 'Logo',
                'plain_value' => '/public/assets/images/light-hori-logo.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'email_notification',
                'setting_name' => 'Email nhận thông báo đơn hàng mới: (Nhập theo dạng: email1, email2, email3, ...)',
                'plain_value' => 'info@mevivu.com, marispham1509@gmail.com, nhan772000@gmail.com',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-mail',
                'class' => 'col-12',
            ],
            [
                'setting_key' => 'facebook_url',
                'setting_name' => 'Facebook URL',
                'plain_value' => 'https://www.facebook.com/mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-brand-facebook',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'zalo_url',
                'setting_name' => 'Zalo URL',
                'plain_value' => 'https://zalo.me/0909090909',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-brand-zalo',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'address_office',
                'setting_name' => 'Địa chỉ văn phòng',
                'plain_value' => '998/42/15 Quang Trung, P.8, Gò Vấp, TP. HCM',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-map-pin',
                'class' => 'col-12',
            ],
            [
                'setting_key' => 'phone_number_1',
                'setting_name' => 'Số điện thoại 1',
                'plain_value' => '0909090909',
                'type_input' => SettingTypeInput::Phone,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-phone',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'phone_number_2',
                'setting_name' => 'Số điện thoại 2',
                'plain_value' => '0909090909',
                'type_input' => SettingTypeInput::Phone,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-phone',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'email',
                'setting_name' => 'Email',
                'plain_value' => 'info@mevivu.com',
                'type_input' => SettingTypeInput::Email,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-mail',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'about_us',
                'setting_name' => 'Về chúng tôi',
                'plain_value' => 'info@mevivu.com',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-file-text',
                'class' => 'col-12',
            ],
            [
                'setting_key' => 'policy',
                'setting_name' => 'Chính sách',
                'plain_value' => 'info@mevivu.com',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-file-text',
                'class' => 'col-12',
            ],
            [
                'setting_key' => 'term',
                'setting_name' => 'Điều khoản',
                'plain_value' => 'info@mevivu.com',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-file-text',
                'class' => 'col-12',
            ],
            [
                'setting_key' => 'bank_transfer_info',
                'setting_name' => 'Thông tin chuyển khoản ngân hàng',
                'plain_value' => '<h3>PHẦN CHUYỂN KHOẢN NGÂN HÀNG</h3>
<p>👉 Quý khách vui lòng chuyển khoản vào tài khoản sau để thanh toán:</p>
<table style="width:100%; border-collapse:collapse;">
<tr><td><strong>Ngân hàng:</strong></td><td>Momiji (Momiji 銀行)</td></tr>
<tr><td><strong>Mã ngân hàng:</strong></td><td>0569</td></tr>
<tr><td><strong>Chủ tài khoản / 口座名:</strong></td><td>ファット (Fat)</td></tr>
<tr><td><strong>Loại tài khoản / 預金項目:</strong></td><td>普通</td></tr>
<tr><td><strong>Chi nhánh / 支店番号:</strong></td><td>三篠支店 (ミササ) (Misasa)</td></tr>
<tr><td><strong>Số tài khoản / 口座番号:</strong></td><td>3095417</td></tr>
</table>
<h4>⚠️ Lưu ý:</h4>
<ul>
<li>Phí chuyển khoản do khách hàng chi trả.</li>
<li>Vui lòng chuyển đúng số tiền của đơn hàng để hệ thống xác nhận.</li>
</ul>
<p>Sau khi chuyển khoản, quý khách vui lòng gửi ảnh hóa đơn chuyển khoản qua:</p>
<ul>
<li>Fanpage Facebook (ấn vào icon Messenger)</li>
<li>Hoặc email: <a href="mailto:fatjpgroup@gmail.com">fatjpgroup@gmail.com</a></li>
</ul>
<p>để chúng tôi xác nhận và xử lý đơn hàng nha 🙏</p>',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::General,
                'icon' => 'ti ti-building-bank',
                'class' => 'col-12',
            ],
            // Footer
            [
                'setting_key' => 'footer_open_time',
                'setting_name' => 'Bán hàng',
                'plain_value' => '08h00 - 17h30',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-clock',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_shop_phone',
                'setting_name' => 'Bán hàng',
                'plain_value' => '0.707070.444 (nhánh số 1)',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-phone',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_office_phone',
                'setting_name' => 'Office',
                'plain_value' => '0.707070.444 (nhánh số 2)',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-phone',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_warranty_phone',
                'setting_name' => 'Bảo hành',
                'plain_value' => '0.707070.444(nhánh số 3)',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-phone',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_email',
                'setting_name' => 'Hợp tác khiếu nại',
                'plain_value' => 'contact@mevivu.com',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-mail',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_address',
                'setting_name' => 'Địa chỉ',
                'plain_value' => '998/42/15 Quang Trung, P.8, Gò Vấp, TP. HCM',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-map-pin',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_phone',
                'setting_name' => 'Số điện thoại',
                'plain_value' => '0707070444',
                'type_input' => SettingTypeInput::Phone,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-phone',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_detail',
                'setting_name' => 'Dòng title cuối chân trang',
                'plain_value' => ' tự hào mang đến cho bạn một trải nghiệm mua sắm công nghệ tuyệt vời. Chúng tôi là
                địa điểm tốt nhất để bạn khám phá và tìm hiểu về những xu hướng công nghệ mới nhất, cũng như tìm mua các
                sản phẩm công nghệ hàng đầu.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'privacy_policy',
                'setting_name' => 'Chính sách bảo mật',
                'plain_value' => '<div class="regulations-container">
<h1 style="text-align:center">Quy Chế Hoạt Động</h1>

<div class="highlight">
<p style="text-align:center">Chúng tôi cam kết cung cấp một nền tảng an toàn và minh bạch. Quy chế này mô tả các quy định và điều kiện khi sử dụng dịch vụ của chúng tôi.</p>
</div>

<h2>1. Phạm vi áp dụng</h2>

<p>- Quy chế này áp dụng cho tất cả người dùng sử dụng dịch vụ của chúng tôi.</p>

<h2>2. Quyền và nghĩa vụ của người dùng</h2>

<p>- Tuân thủ các quy định của pháp luật và quy chế của chúng tôi.</p>
<p>- Cung cấp thông tin chính xác khi đăng ký và sử dụng dịch vụ.</p>
<p>- Không sử dụng dịch vụ vào mục đích vi phạm pháp luật.</p>

<h2>3. Quyền và trách nhiệm của chúng tôi</h2>

<p>- Đảm bảo cung cấp dịch vụ ổn định, an toàn.</p>
<p>- Bảo vệ thông tin cá nhân của người dùng.</p>
<p>- Xử lý các hành vi vi phạm theo quy định.</p>

<h2>4. Chính sách bảo mật</h2>

<p>- Chúng tôi cam kết bảo vệ thông tin cá nhân của người dùng theo chính sách bảo mật đã công bố.</p>

<h2>5. Xử lý vi phạm</h2>

<p>- Chúng tôi có quyền tạm ngưng hoặc chấm dứt tài khoản của người dùng nếu vi phạm quy chế.</p>
<p>- Các vi phạm nghiêm trọng có thể bị xử lý theo pháp luật.</p>

<h2>6. Thay đổi quy chế</h2>

<p>- Chúng tôi có thể cập nhật quy chế hoạt động tùy từng thời điểm. Người dùng có trách nhiệm theo dõi và tuân thủ.</p>

<h2>7. Liên hệ</h2>

<p>- Nếu có bất kỳ thắc mắc nào về Quy chế hoạt động, vui lòng liên hệ với chúng tôi qua email: <a href="mailto:support@example.com">support@example.com</a> hoặc qua địa chỉ văn phòng: 123 Đường ABC, Quận XYZ, Thành phố HCM.</p>

<p>- Cập nhật lần cuối: Ngày 25 tháng 02 năm 2025</p>
</div>
',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-file-text',
                'class' => 'col-md-12'
            ],
            [
                'setting_key' => 'operating_regulations',
                'setting_name' => 'Quy chế hoạt động',
                'plain_value' => '<div class="regulations-container">
<h1 style="text-align:center">Quy Chế Hoạt Động</h1>

<div class="highlight">
<p style="text-align:center">Chúng tôi cam kết cung cấp một nền tảng an toàn và minh bạch. Quy chế này mô tả các quy định và điều kiện khi sử dụng dịch vụ của chúng tôi.</p>
</div>

<h2>1. Phạm vi áp dụng</h2>

<p>Quy chế này áp dụng cho tất cả người dùng sử dụng dịch vụ của chúng tôi.</p>

<h2>2. Quyền và nghĩa vụ của người dùng</h2>

<p>Người dùng có các quyền và nghĩa vụ sau:</p>

<p>- Tuân thủ các quy định của pháp luật và quy chế của chúng tôi.</p>
<p>- Cung cấp thông tin chính xác khi đăng ký và sử dụng dịch vụ.</p>
<p>- Không sử dụng dịch vụ vào mục đích vi phạm pháp luật.</p>

<h2>3. Quyền và trách nhiệm của chúng tôi</h2>

<p>Chúng tôi có quyền và trách nhiệm:</p>

<p>- Đảm bảo cung cấp dịch vụ ổn định, an toàn.</p>
<p>- Bảo vệ thông tin cá nhân của người dùng.</p>
<p>- Xử lý các hành vi vi phạm theo quy định.</p>

<h2>4. Chính sách bảo mật</h2>

<p>Chúng tôi cam kết bảo vệ thông tin cá nhân của người dùng theo chính sách bảo mật đã công bố.</p>

<h2>5. Xử lý vi phạm</h2>

<p>- Chúng tôi có quyền tạm ngưng hoặc chấm dứt tài khoản của người dùng nếu vi phạm quy chế.</p>
<p>- Các vi phạm nghiêm trọng có thể bị xử lý theo pháp luật.</p>

<h2>6. Thay đổi quy chế</h2>

<p>Chúng tôi có thể cập nhật quy chế hoạt động tùy từng thời điểm. Người dùng có trách nhiệm theo dõi và tuân thủ.</p>

<h2>7. Liên hệ</h2>

<p>Nếu có bất kỳ thắc mắc nào về Quy chế hoạt động, vui lòng liên hệ với chúng tôi qua email: <a href="mailto:support@example.com">support@example.com</a> hoặc qua địa chỉ văn phòng: 123 Đường ABC, Quận XYZ, Thành phố HCM.</p>

<p>Cập nhật lần cuối: Ngày 25 tháng 02 năm 2025</p>
</div>
',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-file-text',
                'class' => 'col-md-12',
            ],
            [
                'setting_key' => 'shipping_policy',
                'setting_name' => 'Chính sách vận chuyển',
                'plain_value' => '<div class="regulations-container">
<h1 style="text-align:center">Chính Sách Vận Chuyển</h1>

<div class="highlight">
<p style="text-align:center">Chính sách vận chuyển của chúng tôi đảm bảo hàng hóa được giao đến khách hàng một cách nhanh chóng và an toàn.</p>
</div>

<h2>1. Phạm vi vận chuyển</h2>
<ul>
    <li>Chúng tôi cung cấp dịch vụ vận chuyển trên toàn quốc.</li>
    <li>Một số khu vực có thể không được hỗ trợ do điều kiện địa lý.</li>
</ul>

<h2>2. Thời gian giao hàng</h2>
<ul>
    <li>Thời gian giao hàng dự kiến từ 2 - 7 ngày làm việc tùy theo khu vực.</li>
    <li>Trong các dịp lễ, thời gian giao hàng có thể kéo dài hơn bình thường.</li>
</ul>

<h2>3. Phí vận chuyển</h2>
<ul>
    <li>Phí vận chuyển được tính dựa trên khoảng cách và trọng lượng đơn hàng.</li>
    <li>Miễn phí vận chuyển cho đơn hàng trên một mức giá cụ thể.</li>
</ul>

<h2>4. Kiểm tra và nhận hàng</h2>
<ul>
    <li>Khách hàng có trách nhiệm kiểm tra hàng hóa trước khi nhận.</li>
    <li>Nếu phát hiện hàng hóa bị hư hỏng, vui lòng báo ngay cho chúng tôi trong vòng 24 giờ.</li>
</ul>
</div>',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-truck-delivery',
                'class' => 'col-md-12',
            ],
            [
                'setting_key' => 'return_and_refund_policy',
                'setting_name' => 'Chính sách trả hàng & hoàn tiền',
                'plain_value' => '<div class="regulations-container">
<h1 style="text-align:center">Chính Sách Trả Hàng & Hoàn Tiền</h1>

<div class="highlight">
<p style="text-align:center">Chúng tôi cam kết mang đến sự hài lòng cho khách hàng với chính sách đổi trả linh hoạt.</p>
</div>

<h2>1. Điều kiện trả hàng</h2>
<ul>
    <li>Hàng hóa phải còn nguyên vẹn, không qua sử dụng.</li>
    <li>Thời gian yêu cầu trả hàng trong vòng 7 ngày kể từ khi nhận hàng.</li>
</ul>

<h2>2. Quy trình trả hàng</h2>
<ul>
    <li>Liên hệ với bộ phận chăm sóc khách hàng để yêu cầu trả hàng.</li>
    <li>Gửi hàng về địa chỉ của chúng tôi theo hướng dẫn.</li>
    <li>Kiểm tra và xử lý yêu cầu trong vòng 5 - 7 ngày làm việc.</li>
</ul>

<h2>3. Chính sách hoàn tiền</h2>
<ul>
    <li>Hoàn tiền qua phương thức thanh toán ban đầu hoặc tín dụng mua sắm.</li>
    <li>Thời gian hoàn tiền từ 7 - 14 ngày làm việc tùy phương thức thanh toán.</li>
</ul>
</div>',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-receipt-refund',
                'class' => 'col-md-12',
            ],
            [
                'setting_key' => 'footer_facebook',
                'setting_name' => 'Facebook',
                'plain_value' => 'https://www.facebook.com/mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-brand-facebook',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_linkedin',
                'setting_name' => 'Linkedin',
                'plain_value' => 'https://www.facebook.com/mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-brand-linkedin',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_tiktok',
                'setting_name' => 'Tiktok',
                'plain_value' => 'https://www.facebook.com/mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-brand-tiktok',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_youtube',
                'setting_name' => 'Youtube',
                'plain_value' => 'https://www.facebook.com/mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-brand-youtube',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'footer_instagram',
                'setting_name' => 'Instagram',
                'plain_value' => 'https://www.facebook.com/mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Footer,
                'icon' => 'ti ti-brand-instagram',
                'class' => 'col-md-6',
            ],
            // Contact
            [
                'setting_key' => 'contact_messenger',
                'setting_name' => 'Messenger',
                'plain_value' => 'https://www.facebook.com/mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Contact,
                'icon' => 'ti ti-brand-messenger',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'contact_facebook',
                'setting_name' => 'Facebook',
                'plain_value' => 'https://www.facebook.com/mevivu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Contact,
                'icon' => 'ti ti-brand-facebook',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'contact_zalo',
                'setting_name' => 'Zalo',
                'plain_value' => '0707070444',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Contact,
                'icon' => 'ti ti-message-circle',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'contact_phone',
                'setting_name' => 'Phone',
                'plain_value' => '0707070444',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Contact,
                'icon' => 'ti ti-phone',
                'class' => 'col-md-6',
            ],
            // Information
            [
                'setting_key' => 'infor_title',
                'setting_name' => 'Tiêu đề',
                'plain_value' => 'Bộ sưu tập phụ kiện điện tử và các sản phẩm công nghệ khác',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'infor_content',
                'setting_name' => 'Nội dung',
                'plain_value' => 'Chất lượng là ưu tiên hàng đầu của chúng tôi, vì vậy bạn có thể yên tâm rằng bạn đang mua sắm những sản phẩm chính hãng và đáng tin cậy.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            // Information Card
            [
                'setting_key' => 'infor_card_title_1',
                'setting_name' => 'Tiêu đề thẻ 1',
                'plain_value' => 'Đa dạng sản phẩm',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_card_icon_1',
                'setting_name' => 'Icon thẻ 1',
                'plain_value' => 'ti ti-box',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_card_content_1',
                'setting_name' => 'Nội dung thẻ 1',
                'plain_value' => 'Từ điện thoại thông minh, máy tính xách tay, phụ kiện điện tử đến thiết bị gia đình thông minh, chúng tôi đã tạo ra một bộ sưu tập đáng kinh ngạc để đáp ứng mọi nhu cầu của khách hàng.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4'
            ],
            [
                'setting_key' => 'infor_card_title_2',
                'setting_name' => 'Tiêu đề thẻ 2',
                'plain_value' => 'Mua sắm trực tuyến',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_card_icon_2',
                'setting_name' => 'Icon thẻ 2',
                'plain_value' => 'ti ti-credit-card',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_card_content_2',
                'setting_name' => 'Nội dung thẻ 2',
                'plain_value' => 'Từ điện thoại thông minh, máy tính xách tay, phụ kiện điện tử đến thiết bị gia đình thông minh, chúng tôi đã tạo ra một bộ sưu tập đáng kinh ngạc để đáp ứng mọi nhu cầu của khách hàng.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_card_title_3',
                'setting_name' => 'Tiêu đề thẻ 3',
                'plain_value' => 'Cam kết chất lượng',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_card_icon_3',
                'setting_name' => 'Icon thẻ 3',
                'plain_value' => 'ti ti-check',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_card_content_3',
                'setting_name' => 'Nội dung thẻ 3',
                'plain_value' => 'Từ điện thoại thông minh, máy tính xách tay, phụ kiện điện tử đến thiết bị gia đình thông minh, chúng tôi đã tạo ra một bộ sưu tập đáng kinh ngạc để đáp ứng mọi nhu cầu của khách hàng.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4',
            ],
            // Information Vision
            [
                'setting_key' => 'infor_vision_content',
                'setting_name' => 'Nội dung tầm nhìn',
                'plain_value' => 'Bằng khát vọng tiên phong cùng chiến lược đầu tư - phát triển bền vững, Mevivu đặt mục tiêu trở thành Tập đoàn truyền thông thương mại - marketing hàng đầu tại Việt Nam và vươn tầm khu vực Đông Nam Á. Trở thành đối tác tin cậy, chiến lược, mang đến cho khách hàng trải nghiệm tối ưu và mức độ hài lòng cao nhất.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-12',
            ],
            [
                'setting_key' => 'infor_vision_icon_1',
                'setting_name' => 'Icon tầm nhìn 1',
                'plain_value' => 'ti ti-number-1',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_vision_text_1',
                'setting_name' => 'Nội dung tầm nhìn 1',
                'plain_value' => 'Nắm giữ vị trí dẫn đầu trong lĩnh vực cung cấp các sản phẩm và dịch vụ chất lượng cao.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-9',
            ],
            [
                'setting_key' => 'infor_vision_icon_2',
                'setting_name' => 'Icon tầm nhìn 2',
                'plain_value' => 'ti ti-users',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_vision_text_2',
                'setting_name' => 'Nội dung tầm nhìn 2',
                'plain_value' => 'Đào tạo và xây dựng đội ngũ nhân viên năng động, có trình độ chuyên môn giỏi, tâm huyết với công việc.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-9',
            ],
            [
                'setting_key' => 'infor_vision_icon_3',
                'setting_name' => 'Icon tầm nhìn 3',
                'plain_value' => 'ti ti-components',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_vision_text_3',
                'setting_name' => 'Nội dung tầm nhìn 3',
                'plain_value' => 'Xây dựng công ty với hệ thống quản trị khoa học, minh bạch, phát triển để trở thành một doanh nghiệp kinh doanh vững mạnh, an toàn.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-9',
            ],
            [
                'setting_key' => 'infor_vision_icon_4',
                'setting_name' => 'Icon tầm nhìn 4',
                'plain_value' => 'ti ti-checks',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_vision_text_4',
                'setting_name' => 'Nội dung tầm nhìn 4',
                'plain_value' => 'Đảm bảo mọi sản phẩm - dịch vụ đều đạt chất lượng và hiệu quả cao nhất.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-9',
            ],
            // Information Mission
            [
                'setting_key' => 'infor_mission_slogan',
                'setting_name' => 'Khẩu hiệu sứ mệnh',
                'plain_value' => 'Đội ngũ tiên phong - Nâng tầm giá trị',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-quote',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'infor_mission_content',
                'setting_name' => 'Nội dung sứ mệnh',
                'plain_value' => 'Sứ mệnh của chúng tôi là tạo ra một đội ngũ tiên phong, không ngừng đổi mới và nâng cao giá trị, đem lại sự khác biệt cho khách hàng và cộng đồng,mang lại những trải nghiệm và thành công vượt bậc.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'infor_mission_icon_1',
                'setting_name' => 'Icon sứ mệnh 1',
                'plain_value' => 'ti ti-user-up',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_mission_text_1',
                'setting_name' => 'Nội dung sứ mệnh 1',
                'plain_value' => 'Mevivu đề cao tinh thần cầu tiến, mỗi thành viên trong tập thể cam kết cải tiến 1% mỗi ngày. Chúng tôi không ngừng nâng cao chất lượng dịch vụ, hoàn thiện từng khâu nhỏ để mang đến trải nghiệm hoàn hảo cho khách hàng.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-9',
            ],
            [
                'setting_key' => 'infor_mission_icon_2',
                'setting_name' => 'Icon sứ mệnh 2',
                'plain_value' => 'ti ti-building-factory-2',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_mission_text_2',
                'setting_name' => 'Nội dung sứ mệnh 2',
                'plain_value' => 'Mevivu hướng đến xây dựng một môi trường làm việc văn minh, đề cao giá trị đạo đức và văn hóa doanh nghiệp. Mỗi hành trình mua sắm tại Mevivu đều mang đến cho khách hàng trải nghiệm TỐT, sự hài lòng và ấn tượng sâu sắc.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-9',
            ],
            [
                'setting_key' => 'infor_mission_icon_3',
                'setting_name' => 'Icon sứ mệnh 3',
                'plain_value' => 'ti ti-dimensions',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_mission_text_3',
                'setting_name' => 'Nội dung sứ mệnh 3',
                'plain_value' => 'Mong muốn tạo ra sự khác biệt so với các đối thủ cạnh tranh, trở thành thương hiệu được khách hàng tin tưởng và yêu thích.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-9',
            ],
            // Information Value
            [
                'setting_key' => 'infor_value_content',
                'setting_name' => 'Nội dung giá trị cốt lõi',
                'plain_value' => 'Mevivu Group xác định Tâm - Trí - Nhân - Tín - Tiến - Chất là kim chỉ nam cho mọi hoạt động, là nền tảng đạo đức và trí tuệ vững chắc cho sự phát triển bền vững.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'infor_value_sub_content',
                'setting_name' => 'Nội dung phụ giá trị cốt lõi',
                'plain_value' => 'Giá trị cốt lõi của Mevivu Group',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'infor_value_icon_1',
                'setting_name' => 'Icon giá trị 1',
                'plain_value' => 'ti ti-heart',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_title_1',
                'setting_name' => 'Tiêu đề giá trị 1',
                'plain_value' => 'TÂM',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_text_1',
                'setting_name' => 'Nội dung giá trị 1',
                'plain_value' => 'Cống hiến hết mình, tận tâm với công việc để đạt được mục tiêu và mang lại giá trị đích thực cho khách hàng và đối tác. Hành động với tinh thần nhân văn, đề cao sự tôn trọng và hỗ trợ lẫn nhau.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_icon_2',
                'setting_name' => 'Icon giá trị 2',
                'plain_value' => 'ti ti-brain',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_title_2',
                'setting_name' => 'Tiêu đề giá trị 2',
                'plain_value' => 'TRÍ',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_text_2',
                'setting_name' => 'Nội dung giá trị 2',
                'plain_value' => 'Sử dụng trí tuệ và sự sáng tạo để giải quyết các vấn đề và đưa ra các giải pháp tối ưu.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_icon_3',
                'setting_name' => 'Icon giá trị 3',
                'plain_value' => 'ti ti-user-star',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_title_3',
                'setting_name' => 'Tiêu đề giá trị 3',
                'plain_value' => 'NHÂN',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_text_3',
                'setting_name' => 'Nội dung giá trị 3',
                'plain_value' => 'Đề cao giá trị con người, xây dựng đội ngũ nhân viên năng động, có trình độ chuyên môn giỏi, tâm huyết với công việc.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_icon_4',
                'setting_name' => 'Icon giá trị 4',
                'plain_value' => 'ti ti-gizmo',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_title_4',
                'setting_name' => 'Tiêu đề giá trị 4',
                'plain_value' => 'TÍN',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_text_4',
                'setting_name' => 'Nội dung giá trị 4',
                'plain_value' => 'Giữ chữ tín với khách hàng và đối tác, luôn hành động đúng cam kết.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_icon_5',
                'setting_name' => 'Icon giá trị 5',
                'plain_value' => 'ti ti-trending-up',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_title_5',
                'setting_name' => 'Tiêu đề giá trị 5',
                'plain_value' => 'TIẾN',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_text_5',
                'setting_name' => 'Nội dung giá trị 5',
                'plain_value' => 'Luôn tiến lên, không ngừng cải tiến và phát triển để đạt được những thành tựu mới.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_icon_6',
                'setting_name' => 'Icon giá trị 6',
                'plain_value' => 'ti ti-arrow-merge-both',
                'type_input' => SettingTypeInput::Icon,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-icons',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_title_6',
                'setting_name' => 'Tiêu đề giá trị 6',
                'plain_value' => 'CHẤT',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-4',
            ],
            [
                'setting_key' => 'infor_value_text_6',
                'setting_name' => 'Nội dung giá trị 6',
                'plain_value' => 'Đảm bảo chất lượng trong mọi sản phẩm và dịch vụ, mang lại sự hài lòng cao nhất cho khách hàng.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-4',
            ],
            // Information Achievement
            [
                'setting_key' => 'infor_achievement_content',
                'setting_name' => 'Nội dung thành tựu',
                'plain_value' => 'Khám phá tận hưởng tiện lợi và sự đa dạng của mua sắm trực tuyến',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'infor_achievement_stat_1',
                'setting_name' => 'Số liệu thành tựu 1',
                'plain_value' => '1,000+',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-number',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_text_1',
                'setting_name' => 'Nội dung thành tựu 1',
                'plain_value' => 'Thương hiệu nổi tiếng',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_stat_2',
                'setting_name' => 'Số liệu thành tựu 2',
                'plain_value' => '95%',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-number',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_text_2',
                'setting_name' => 'Nội dung thành tựu 2',
                'plain_value' => 'Khách hàng hoàn toàn hài lòng',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_stat_3',
                'setting_name' => 'Số liệu thành tựu 3',
                'plain_value' => '99+',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-number',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_text_3',
                'setting_name' => 'Nội dung thành tựu 3',
                'plain_value' => 'Danh mục sản phẩm nổi bật',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_stat_4',
                'setting_name' => 'Số liệu thành tựu 4',
                'plain_value' => '131,000+',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-number',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_text_4',
                'setting_name' => 'Nội dung thành tựu 4',
                'plain_value' => 'Đơn hàng đã được đặt',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_stat_5',
                'setting_name' => 'Số liệu thành tựu 5',
                'plain_value' => '200,000+',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-number',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_text_5',
                'setting_name' => 'Nội dung thành tựu 5',
                'plain_value' => 'Sản phẩm công nghệ hàng đầu',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_stat_6',
                'setting_name' => 'Số liệu thành tựu 6',
                'plain_value' => '39%',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-number',
                'class' => 'col-md-3',
            ],
            [
                'setting_key' => 'infor_achievement_text_6',
                'setting_name' => 'Nội dung thành tựu 6',
                'plain_value' => 'Lợi nhuận hàng năm tăng trưởng',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::Information,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-3',
            ],
            //config
            [
                'setting_key' => 'amount_to_exchange',
                'setting_name' => 'Số tiền mua hàng để đổi được 1 xu',
                'plain_value' => '100',
                'type_input' => SettingTypeInput::Number,
                'group' => SettingGroup::Config,
                'icon' => 'ti ti-coin',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'commission_rate',
                'setting_name' => 'Phần trăm hoa hồng',
                'plain_value' => '5',
                'type_input' => SettingTypeInput::Number,
                'group' => SettingGroup::Config,
                'icon' => 'ti ti-percentage',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'exchange_percent',
                'setting_name' => 'Một xu tương ứng bao nhiêu tiền',
                'plain_value' => '5',
                'type_input' => SettingTypeInput::Number,
                'group' => SettingGroup::Config,
                'icon' => 'ti ti-coin',
                'class' => 'col-md-6',
            ],
            //miniapp
            [
                'setting_key' => 'is_testing_zalostore',
                'setting_name' => 'Bật/Tắt Testing Zalo Store',
                'plain_value' => '1',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-toggle-left',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'zalo_oa_fullname',
                'setting_name' => 'Tên Zalo OA',
                'plain_value' => 'Phạm Minh Mạnh',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-user',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'zalo_oa_id',
                'setting_name' => 'Zalo OA ID',
                'plain_value' => '169628657279778017',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-id',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'miniapp_app_id',
                'setting_name' => 'App ID',
                'plain_value' => '203595916666729993',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-id',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'miniapp_private_key',
                'setting_name' => 'Private Key',
                'plain_value' => '1cac3e29e07b07db58292f6d8eedf1d9',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-key',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'zalo_home_title',
                'setting_name' => 'Tiêu đề MiniApp',
                'plain_value' => 'AppMart',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-heading',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'zalo_home_short_desc',
                'setting_name' => 'Mô tả ngắn MiniApp',
                'plain_value' => 'Xuân Shop là giải pháp mini app bán hàng online giúp bạn dễ dàng mua sắm, đặt hàng và quản lý đơn hàng trên Zalo.',
                'type_input' => SettingTypeInput::Text,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-file-description',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'information',
                'setting_name' => 'Thông tin giới thiệu',
                'plain_value' => '<h2><strong>1. Giới thiệu về AppMart</strong></h2>

<p>Trong thời đại số h&oacute;a, việc tận dụng c&aacute;c nền tảng mạng x&atilde; hội để b&aacute;n h&agrave;ng l&agrave; xu hướng kh&ocirc;ng thể bỏ qua. <strong>AppMart</strong> ra đời như một giải ph&aacute;p tối ưu gi&uacute;p doanh nghiệp v&agrave; c&aacute; nh&acirc;n kinh doanh dễ d&agrave;ng tiếp cận kh&aacute;ch h&agrave;ng tr&ecirc;n nền tảng <strong>Zalo</strong> th&ocirc;ng qua <strong>Zalo Mini App</strong> &ndash; một ứng dụng thu nhỏ hoạt động trực tiếp trong hệ sinh th&aacute;i Zalo m&agrave; kh&ocirc;ng cần c&agrave;i đặt ri&ecirc;ng.</p>

<p>&nbsp;</p>

<p><img alt="" src="https://demo.appmart.vn/userfiles/images/laptop/Zalominiapp/anhthumbzalominiapp.jpg" style="height:424px; width:700px" /></p>

<p>&nbsp;</p>

<h2><strong>2. AppMart l&agrave; g&igrave;?</strong></h2>

<p><strong>AppMart</strong> l&agrave; một nền tảng hỗ trợ tạo v&agrave; vận h&agrave;nh gian h&agrave;ng trực tuyến ngay tr&ecirc;n Zalo, gi&uacute;p doanh nghiệp tối ưu h&oacute;a quy tr&igrave;nh b&aacute;n h&agrave;ng, quản l&yacute; đơn h&agrave;ng v&agrave; chăm s&oacute;c kh&aacute;ch h&agrave;ng một c&aacute;ch hiệu quả. Th&ocirc;ng qua <strong>Zalo Mini App</strong>, người b&aacute;n c&oacute; thể thiết lập cửa h&agrave;ng của m&igrave;nh nhanh ch&oacute;ng, đồng thời tận dụng lượng người d&ugrave;ng khổng lồ của Zalo để mở rộng thị trường.</p>',
                'type_input' => SettingTypeInput::Ckeditor,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-file-text',
                'class' => 'col-md-12'
            ],
            [
                'setting_key' => 'zalo_oa_avatar',
                'setting_name' => 'Ảnh đại diện Zalo OA',
                'plain_value' => 'assets/images/avatar-user.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6'
            ],
            [
                'setting_key' => 'zalo_logo',
                'setting_name' => 'Logo',
                'plain_value' => '/public/user/assets/images/logo-ngang.png',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6'
            ],
            [
                'setting_key' => 'thumbnail',
                'setting_name' => 'Ảnh thumbnail',
                'plain_value' => '/userfiles/files/Zalominiapp/anhthumbzalominiapp.jpg',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-photo',
                'class' => 'col-md-6',
            ],
            [
                'setting_key' => 'qr_miniapp_image',
                'setting_name' => 'Ảnh QR MiniApp',
                'plain_value' => '/userfiles/files/Zalominiapp/appmart_13-02-2025.jpg',
                'type_input' => SettingTypeInput::Image,
                'group' => SettingGroup::MiniApp,
                'icon' => 'ti ti-qrcode',
                'class' => 'col-md-6',
            ],
            //membership
            [
                'setting_key' => 'amount_to_exchange_membership',
                'setting_name' => 'Số tiền mua hàng để đổi được 1 điểm xét hạng',
                'plain_value' => '100',
                'type_input' => SettingTypeInput::Number,
                'group' => SettingGroup::Membership,
                'icon' => 'ti ti-star',
                'class' => 'col-md-6',
            ],
        ]);
    }
}
