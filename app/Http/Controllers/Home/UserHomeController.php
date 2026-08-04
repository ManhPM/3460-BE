<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Repositories\FlashSale\FlashSaleRepositoryInterface;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Admin\Repositories\Section\SectionRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\AuthService;
use App\Enums\Order\PaymentStatus;
use App\Enums\Setting\SettingGroup;
use Illuminate\Http\Request;

class UserHomeController extends Controller
{
    use AuthService;
    protected SettingRepositoryInterface $settingRepository;
    protected FlashSaleRepositoryInterface $flashSaleRepository;
    protected OrderRepositoryInterface $orderRepository;
    protected SectionRepositoryInterface $sectionRepository;
    protected ProductRepositoryInterface $productRepository;
    protected $fileService;
    public function __construct(
        ProductRepositoryInterface   $repository,
        SettingRepositoryInterface $settingRepository,
        FlashSaleRepositoryInterface $flashSaleRepository,
        OrderRepositoryInterface $orderRepository,
        SectionRepositoryInterface $sectionRepository,
        ProductRepositoryInterface $productRepository,
        FileService $fileService,
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->flashSaleRepository = $flashSaleRepository;
        $this->settingRepository = $settingRepository;
        $this->orderRepository = $orderRepository;
        $this->sectionRepository = $sectionRepository;
        $this->productRepository = $productRepository;
        $this->fileService = $fileService;
    }
    public function getView()
    {
        return [
            'index' => 'user.home.index',
            'information' => 'user.information.index',
            'contact' => 'user.contact.index',
            'order' => 'user.home.check-order',
            'order-result' => 'user.home.check-order-result',
            'flex-page' => 'user.home.flex-page',
        ];
    }
    public function index()
    {
        $settings = $this->settingRepository->getAll();
        $title = $settings->where('setting_key', 'home_title')->first()->plain_value;
        $meta_desc = $settings->where('setting_key', 'home_short_desc')->first()->plain_value;
        $flashSale = $this->flashSaleRepository->getCurrentFlashSale();
        $productsRecommendation = $this->productRepository->getRecommendation($this->getCurrentUser() ?? 0);
        return view($this->view['index'], [
            'flashSale' => $flashSale,
            'title' => $title,
            'meta_desc' => $meta_desc,
            'settings' => $settings,
            'productsRecommendation' => $productsRecommendation,
        ]);
    }

    public function information()
    {
        $settings = $this->settingRepository->getAll();
        $title = $settings->where('setting_key', 'information_title')->first()->plain_value;
        $meta_desc = $settings->where('setting_key', 'information_meta_desc')->first()->plain_value;
        $breadcrumbs = $this->homeCrums->add(__('Giới thiệu'))->getBreadcrumbs();

        $settingsInformation = $this->settingRepository->getByGroup([SettingGroup::Information]);
        return view($this->view['information'], compact('title', 'meta_desc', 'settingsInformation', 'breadcrumbs'));
    }

    public function privacyPolicy()
    {
        $html = $this->settingRepository->findByField('setting_key', 'privacy_policy')->plain_value;
        $title = "Chính sách bảo mật";
        $breadcrumbs = $this->homeCrums->add(__('Chính sách bảo mật'))->getBreadcrumbs();

        return $this->flexReturnView($title, $title, $breadcrumbs, $html);
    }

    public function operatingRegulations()
    {
        $html = $this->settingRepository->findByField('setting_key', 'operating_regulations')->plain_value;
        $title = "Quy chế hoạt động";
        $breadcrumbs = $this->homeCrums->add(__('Quy chế hoạt động'))->getBreadcrumbs();
        return $this->flexReturnView($title, $title, $breadcrumbs, $html);
    }

    public function shippingPolicy()
    {
        $html = $this->settingRepository->findByField('setting_key', 'shipping_policy')->plain_value;
        $title = "Chính sách vận chuyển";
        $breadcrumbs = $this->homeCrums->add(__('Chính sách vận chuyển'))->getBreadcrumbs();
        return $this->flexReturnView($title, $title, $breadcrumbs, $html);
    }

    public function returnAndRefundPolicy()
    {
        $html = $this->settingRepository->findByField('setting_key', 'return_and_refund_policy')->plain_value;
        $title = "Chính sách đổi trả và hoàn tiền";
        $breadcrumbs = $this->homeCrums->add(__('Chính sách đổi trả và hoàn tiền'))->getBreadcrumbs();
        return $this->flexReturnView($title, $title, $breadcrumbs, $html);
    }

    private function flexReturnView($title, $meta_desc, $breadcrumbs, $html)
    {
        return view($this->view['flex-page'], compact('title', 'meta_desc', 'breadcrumbs', 'html'));
    }


    public function contact()
    {
        $settings = $this->settingRepository->getAll();
        $title = $settings->where('setting_key', 'contact_title')->first()->plain_value;
        $meta_desc = $settings->where('setting_key', 'contact_meta_desc')->first()->plain_value;
        $breadcrumbs =  $this->homeCrums->add(__('Liên hệ'))->getBreadcrumbs();

        $settingsFooter = $this->settingRepository->getByGroup([SettingGroup::Footer]);
        $settingsContact = $this->settingRepository->getByGroup([SettingGroup::Contact]);
        return view($this->view['contact'], compact('title', 'meta_desc', 'settingsContact', 'settingsFooter', 'breadcrumbs'));
    }

    public function getOrderDetailForCustomer(Request $request)
    {
        $settings = $this->settingRepository->getAll();
        $title = 'Tra cứu đơn hàng';
        $meta_desc = 'Trang tra cứu đơn hàng LINHKA dùng để tra cứu thông tin các đơn hàng dựa vào mã hóa đơn.';
        $instance = $this->orderRepository->getBy(['code' => $request->input('code', 0), 'phone' => $request->input('phone')])->first();
        if ($instance) {
            return view($this->view['order-result'], compact('settings', 'meta_desc', 'title', 'instance'));
        };
        return view($this->view['order'], compact('settings', 'meta_desc', 'title'));
    }

    public function uploadCheckoutImage(Request $request)
    {
        $settings = $this->settingRepository->getAll();
        $title = 'Tra cứu đơn hàng';
        $meta_desc = 'Trang tra cứu đơn hàng LINHKA dùng để tra cứu thông tin các đơn hàng dựa vào mã hóa đơn.';
        $paymentImage = $request->file('payment_image');
        $code = $request->input('code');
        if (isset($paymentImage)) {
            $instance = $this->orderRepository->findByField('code', $code);
            if ($instance) {
                $paymentImage = $this->fileService->uploadAvatar('images', $paymentImage, null);
                $instance->update(['payment_status' => PaymentStatus::Pending->value, 'payment_image' => $paymentImage]);
                return view($this->view['order'], compact('settings', 'meta_desc', 'title', 'instance'));
            };
        }
        return back()->with('error', __('Thao tác thất bại.'));
    }
}
