<?php

namespace App\Admin\Services\Order;

use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Services\Order\OrderServiceInterface;
use App\Admin\Repositories\Order\{OrderRepositoryInterface, OrderDetailRepositoryInterface};
use App\Admin\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;
use App\Admin\Repositories\Product\{ProductRepositoryInterface, ProductVariationRepositoryInterface};
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\Setup;
use App\Enums\Product\ProductType;
use App\Enums\Order\{OrderStatus, PaymentStatus};
use App\Traits\Membership;
use App\Traits\SendNotification;
use App\Traits\UseLog;
use Exception;
use Illuminate\Support\Facades\DB;

class OrderService implements OrderServiceInterface
{
    use Setup, UseLog, Membership, SendNotification;
    protected $data;
    protected $orderDetails;
    protected $repository;
    protected $userRepository;
    protected $productRepository;
    protected $productVariationRepository;
    protected $repositoryOrderDetail;
    protected $discountRepository;
    protected $settingRepository;
    protected $voucherRepository;
    protected $fileService;

    public function __construct(
        OrderRepositoryInterface $repository,
        OrderDetailRepositoryInterface $repositoryOrderDetail,
        SettingRepositoryInterface $settingRepository,
        DiscountRepositoryInterface $discountRepository,
        UserRepositoryInterface $userRepository,
        ProductRepositoryInterface $productRepository,
        ProductVariationRepositoryInterface $productVariationRepository,
        VoucherRepositoryInterface $voucherRepository,
        FileService $fileService,
    ) {
        $this->repository = $repository;
        $this->repositoryOrderDetail = $repositoryOrderDetail;
        $this->discountRepository = $discountRepository;
        $this->userRepository = $userRepository;
        $this->productRepository = $productRepository;
        $this->productVariationRepository = $productVariationRepository;
        $this->settingRepository = $settingRepository;
        $this->voucherRepository = $voucherRepository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        $this->data['order']['status'] = OrderStatus::Pending;
        $this->data['order']['code'] = $this->createCodeOrder();
        DB::beginTransaction();
        try {
            if (!$this->makeNewDataOrderDetail()) {
                return false;
            }
            if (isset($this->data['order']['discount_id'])) {
                $this->handleDiscount('store');
            }
            $order = $this->repository->create($this->data['order']);
            $this->storeOrderDetail($order->id, $this->orderDetails);
            DB::commit();
            return $order;
        } catch (Exception $e) {
            $this->logError('Failed to process order: ', $e);
            DB::rollBack();
            throw $e;
        }
    }

    public function checkValidDiscount(Request $request)
    {
        $this->data = $request->validated();
        if (isset($this->data['order']['discount_id'])) {
            $discount = $this->discountRepository->findOrFail($this->data['order']['discount_id']);
            if ($discount->min_order_amount > $this->data['order']['total']) {
                return false;
            }
        }
        return true;
    }

    private function restoreProduct($order): void
    {
        $adminId = $order->admin_id;

        foreach ($order->details as $detail) {
            if ($detail->product_variation_id) {
                // restore variation quantity in admin inventory
                $inventory = \App\Models\AdminInventory::where('admin_id', $adminId)
                    ->where('product_id', $detail->product_id)
                    ->where('product_variation_id', $detail->product_variation_id)
                    ->first();

                if ($inventory) {
                    $inventory->increment('qty', $detail->qty);
                }
            } else {
                // restore main product quantity in admin inventory
                $inventory = \App\Models\AdminInventory::where('admin_id', $adminId)
                    ->where('product_id', $detail->product_id)
                    ->whereNull('product_variation_id')
                    ->first();

                if ($inventory) {
                    $inventory->increment('qty', $detail->qty);
                }
            }

            // Xử lý hoàn nguyên số lượng flash sale nếu có
            if ($detail->product->is_flash_sale) {
                $flashSaleQuery = $detail->product->is_flash_sale->details()
                    ->where('product_id', $detail->product_id);

                // Phân biệt biến thể vs sản phẩm đơn
                if ($detail->product_variation_id) {
                    $flashSaleQuery->where('product_variation_id', $detail->product_variation_id);
                } else {
                    $flashSaleQuery->whereNull('product_variation_id');
                }

                $flashSaleDetail = $flashSaleQuery->first();

                if ($flashSaleDetail) {
                    $soldQty = max($flashSaleDetail->sold - $detail->qty, 0);
                    $flashSaleDetail->update(['sold' => $soldQty]);
                }
            }
        }
    }


    public function cancel(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $order = $this->repository->findOrFail($data['id']);
            if ($order && $order->status != OrderStatus::Cancelled && $order->status != OrderStatus::Completed) {
                if ($order->status != OrderStatus::Pending) {
                    $this->restoreProduct($order);
                }
                if ($order->discount_code) {
                    $discount = $this->discountRepository->findByField('code', $order->discount_code);
                    if ($discount) {
                        $discount->max_usage = $discount->max_usage + 1;
                        $discount->save();
                    }
                }
                if ($order->points > 0) {
                    $this->userRepository->update($order->user_id, ['points' => $order->user->points + $order->points]);
                }
                if ($order->voucher_shipping_code) {
                    $voucherShipping = $this->voucherRepository->findByField('code', $order->voucher_shipping_code);
                    $voucherShipping->update(['is_used' => 0]);
                }
                if ($order->voucher_product_code) {
                    $voucherProduct = $this->voucherRepository->findByField('code', $order->voucher_product_code);
                    $voucherProduct->update(['is_used' => 0]);
                }
                $order->update(['status' => OrderStatus::Cancelled]);

                // Refund to wallet if the order was paid by wallet
                if (($order->payment_method == \App\Enums\Payment\PaymentMethod::Wallet) && $order->user) {
                    $order->refresh();
                    $refundable = ($order->total + ($order->shipping_fee ?? 0))
                        - (($order->discount_value ?? 0)
                            + ($order->voucher_shipping_discount_value ?? 0)
                            + ($order->voucher_product_discount_value ?? 0)
                            + ($order->membership_discount_value ?? 0)
                            + ($order->membership_shipping_discount_value ?? 0));

                    $user = $order->user;
                    // Sử dụng increment để tránh race condition
                    $user->increment('wallet_balance', $refundable);

                    \App\Models\WalletTransaction::create([
                        'user_id' => $user->id,
                        'amount' => $refundable,
                        'type' => \App\Enums\Transaction\WalletTransactionType::Refund->value,
                        'status' => \App\Enums\Transaction\WalletTransactionStatus::Approved->value,
                        'order_id' => $order->id,
                        'note' => 'Hoàn tiền vào ví do huỷ đơn (admin)',
                    ]);
                }
            } else {
                DB::rollBack();
                return false;
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            $this->logError('Failed to cancel order: ', $e);
            DB::rollBack();
            return false;
        }
    }

    private function makeNewDataOrderDetail()
    {
        $products = $this->productRepository->getBySlugs(
            array_unique($this->data['order_detail']['product_slug'])
        );
        if ($products->count() == 0) {
            return false;
        }
        $this->dataOrderDetail($products);
        return true;
    }
    private function dataOrderDetail($products)
    {
        foreach ($this->data['order_detail']['product_slug'] as $key => $value) {
            $product = $products->firstWhere('slug', $value);
            $this->orderDetails[] = [
                'product_id' => $product->id,
                'unit_price' => $this->data['order_detail']['unit_price'][$key],
                'product_variation_id' => $this->data['order_detail']['product_variation_id'][$key] ?: null,
                'qty' => $this->data['order_detail']['product_qty'][$key],
                'product_name' => $product->name,
                'product_avatar' => $product->avatar ?? null,
                'product_slug' => $product->slug,
            ];
        }
    }

    protected function storeOrderDetail($orderId, $data)
    {
        foreach ($data as $item) {
            $item['order_id'] = $orderId;
            $this->repositoryOrderDetail->create($item);
        }
    }

    public function update(Request $request)
    {
        $this->data = $request->validated();
        $oldOrder = $this->repository->findOrFail($this->data['order']['id']);

        // validateTransition sẽ tự động throw ValidationException nếu không hợp lệ
        if (isset($this->data['order']['status']) && $oldOrder->status->value != $this->data['order']['status']) {
            $newStatus = OrderStatus::from($this->data['order']['status']);
            $oldOrder->status->validateTransition($newStatus);
        }

        // Validate payment_status transition nếu có thay đổi trạng thái thanh toán
        // validateTransition sẽ tự động throw ValidationException nếu không hợp lệ
        if (isset($this->data['order']['payment_status']) && $oldOrder->payment_status->value != $this->data['order']['payment_status']) {
            $newPaymentStatus = PaymentStatus::from($this->data['order']['payment_status']);
            $oldOrder->payment_status->validateTransition($newPaymentStatus);
        }

        DB::beginTransaction();
        try {
            $this->processOrderDetails();
            $this->handleDiscountIfNeeded();
            $order = $this->repository->update($this->data['order']['id'], $this->data['order']);
            $settings = $this->settingRepository->getAll();

            if ($oldOrder->status != $order->status) {
                if ($oldOrder->status == OrderStatus::Pending && $order->status == OrderStatus::Confirmed) {
                    $confirmResult = $this->confirm($order->id);
                    if ($confirmResult !== true) {
                        $errorMessage = is_string($confirmResult)
                            ? "Sản phẩm {$confirmResult} không đủ số lượng tồn kho."
                            : ($confirmResult === 1 ? "Giá sản phẩm đã thay đổi." : "Xác nhận đơn hàng thất bại.");
                        throw new Exception($errorMessage);
                    }
                } else {
                    if ($order->status == OrderStatus::Cancelled) {
                        $this->restorePoints($order);
                        // Refund to wallet if the order was paid by wallet
                        if (($order->payment_method == \App\Enums\Payment\PaymentMethod::Wallet) && $order->user) {
                            $order->refresh();
                            $refundable = ($order->total + ($order->shipping_fee ?? 0))
                                - (($order->discount_value ?? 0) + ($order->voucher_shipping_discount_value ?? 0) + ($order->voucher_product_discount_value ?? 0) + ($order->membership_discount_value ?? 0) + ($order->membership_shipping_discount_value ?? 0));
                            $user = $order->user;
                            // Sử dụng increment để tránh race condition
                            $user->increment('wallet_balance', $refundable);
                            \App\Models\WalletTransaction::create([
                                'user_id' => $user->id,
                                'amount' => $refundable,
                                'type' => 'refund',
                                'status' => 'approved',
                                'order_id' => $order->id,
                                'note' => 'Hoàn tiền vào ví do huỷ đơn',
                            ]);
                        }
                    }
                    $this->handlePointsUpdate($oldOrder, $order, $settings);
                    if ($order->user) {
                        $user = $this->userRepository->find($order->user_id);
                        $this->updateMembershipLevel($user);
                    }
                }
            }

            DB::commit();
            return $order;
        } catch (Exception $e) {
            $this->logError('Failed to process order: ', $e);
            DB::rollBack();
            throw $e;
        }
    }

    private function restorePoints($order): void
    {
        if ($order->points > 0) {
            $this->userRepository->update($order->user_id, ['points' => $order->user->points + $order->points]);
        }
    }

    private function processOrderDetails()
    {
        if (isset($this->data['order']['user_id'])) {
            $dataOrderDetail = $this->updateOrCreateDataOrderDetail();
            if (!empty($dataOrderDetail)) {
                $this->data['order_detail'] = $dataOrderDetail;
                $this->makeNewDataOrderDetail();
                $this->storeOrderDetail($this->data['order']['id'], $this->orderDetails);
            }
        }
    }

    private function handleDiscountIfNeeded()
    {
        if (isset($this->data['order']['discount_id'])) {
            $this->handleDiscount('update');
        }
    }

    private function handlePointsUpdate($oldOrder, $order, $settings)
    {
        $amountToExchange = $settings->where('setting_key', 'amount_to_exchange')->first()->plain_value;
        $amountToExchangeMembership = $settings->where('setting_key', 'amount_to_exchange_membership')->first()->plain_value;

        if (!$order->user_id) {
            return;
        }

        // Tích điểm dựa trên giá trị tiền hàng của đơn hàng (không cần check điều kiện tối thiểu)
        $validTotal = $order->total;
        $pointsChange = $validTotal / $amountToExchange;
        $pointsChangeMembership = $validTotal / $amountToExchangeMembership;

        if ($order->status == OrderStatus::Completed) {
            $this->handleOrderCompleted($order, $pointsChange, $pointsChangeMembership);
        }

        if ($oldOrder->status == OrderStatus::Completed) {
            $this->handleOrderUnCompleted($order, $pointsChange, $pointsChangeMembership);
        }
    }

    private function handleOrderCompleted($order, $pointsChange, $pointsChangeMembership): void
    {
        // Refresh user để lấy dữ liệu mới nhất
        $user = $this->userRepository->find($order->user_id);

        // Lưu membership_id cũ để so sánh sau khi cập nhật
        $oldMembershipId = $user->membership_id;

        // Cập nhật điểm cho user
        $this->userRepository->update($order->user_id, [
            'points' => $user->points + $pointsChange,
            'membership_level_points' => $user->membership_level_points + $pointsChangeMembership,
        ]);

        // Refresh user để lấy dữ liệu mới nhất
        $user = $this->userRepository->find($order->user_id);

        // Kiểm tra và nâng hạng thành viên nếu có hạng cao hơn
        $this->updateMembershipLevel($user);

        // Refresh lại user để lấy membership_id mới
        $user->refresh();
        $user->load('member');

        // Gửi thông báo tích điểm
        if ($pointsChange > 0) {
            $pointsMessage = "Bạn đã nhận được " . number_format($pointsChange, 0, ',', '.') . " điểm từ đơn hàng #{$order->code}";
            $this->sendNotification(
                $user->id,
                "Tích điểm thành công",
                $pointsMessage,
                $user->device_token
            );
        }

        // Gửi thông báo lên hạng nếu có thay đổi
        if ($oldMembershipId != $user->membership_id && $user->member) {
            $membershipMessage = "Chúc mừng! Bạn đã được nâng cấp lên hạng thành viên {$user->member->name}";
            $this->sendNotification(
                $user->id,
                "Nâng cấp hạng thành viên",
                $membershipMessage,
                $user->device_token
            );
        }

        // Xử lý affiliate earning
        $this->handleAffiliateEarning($order);

        // Cập nhật points_earned và member_ship_points_earned cho đơn hàng
        $order->update([
            'shipping_date' => now(),
            'points_earned' => $pointsChange,
            'member_ship_points_earned' => $pointsChangeMembership
        ]);
    }

    private function handleOrderUnCompleted($order, $pointsChange, $pointsChangeMembership): void
    {
        $this->userRepository->update($order->user_id, [
            'points' => max(0, $order->user->points - $pointsChange),
            'membership_level_points' => max(0, $order->user->membership_level_points - $pointsChangeMembership),
        ]);

        // Refresh user để lấy dữ liệu mới nhất
        $user = $this->userRepository->find($order->user_id);

        // Kiểm tra và điều chỉnh hạng thành viên sau khi trừ điểm
        $this->updateMembershipLevel($user);
    }

    /**
     * Xử lý affiliate earning khi đơn hàng hoàn thành
     */
    private function handleAffiliateEarning($order): void
    {
        // Kiểm tra xem đã tạo affiliate transaction cho đơn hàng này chưa
        $existingTransaction = \App\Models\WalletTransaction::where('order_id', $order->id)
            ->where('type', \App\Enums\Transaction\WalletTransactionType::Affiliate->value)
            ->exists();

        if ($existingTransaction) {
            return; // Đã xử lý rồi, không tạo lại
        }

        // Lấy tất cả order details có affiliate_code
        $orderDetails = $order->details()->whereNotNull('affiliate_code')->get();

        if ($orderDetails->isEmpty()) {
            return;
        }

        // Nhóm theo affiliate_code và tính tổng earning
        $affiliateEarnings = $orderDetails->groupBy('affiliate_code')->map(function ($details) {
            return $details->sum('affiliate_earning');
        });

        // Lấy danh sách các affiliate_code duy nhất để gửi thông báo
        $affiliateCodes = $orderDetails->pluck('affiliate_code')->unique();

        // Tạo wallet transaction cho từng affiliate và gửi thông báo
        foreach ($affiliateEarnings as $affiliateCode => $totalEarning) {
            // Tìm user có affiliate_code này
            $affiliateUser = $this->userRepository->getBy(['affiliate_code' => $affiliateCode])->first();

            if ($affiliateUser) {
                if ($totalEarning > 0) {
                    // Cộng tiền vào ví bằng increment để tránh race condition
                    $affiliateUser->increment('wallet_balance', $totalEarning);

                    // Refresh để lấy wallet_balance mới nhất sau khi cộng
                    $affiliateUser->refresh();

                    // Tạo wallet transaction
                    \App\Models\WalletTransaction::create([
                        'user_id' => $affiliateUser->id,
                        'amount' => $totalEarning,
                        'type' => \App\Enums\Transaction\WalletTransactionType::Affiliate->value,
                        'status' => \App\Enums\Transaction\WalletTransactionStatus::Approved->value,
                        'order_id' => $order->id,
                        'note' => "Hoa hồng từ đơn hàng #{$order->code}",
                    ]);

                    // Gửi thông báo cho affiliate user về việc nhận tiền hoa hồng
                    $affiliateMessage = "Bạn đã nhận được " . number_format($totalEarning, 0, ',', '.') . " đ hoa hồng từ đơn hàng #{$order->code}";
                    $this->sendNotification(
                        $affiliateUser->id,
                        "Nhận hoa hồng affiliate",
                        $affiliateMessage,
                        $affiliateUser->device_token
                    );
                } else {
                    // Gửi thông báo cho affiliate user về việc đơn hàng hoàn thành (dù không có hoa hồng)
                    $affiliateMessage = "Đơn hàng #{$order->code} mà bạn giới thiệu đã hoàn thành.";
                    $this->sendNotification(
                        $affiliateUser->id,
                        "Đơn hàng đã hoàn thành",
                        $affiliateMessage,
                        $affiliateUser->device_token
                    );
                }
            }
        }

        // Gửi thông báo cho các affiliate_code khác không có earning (nếu có)
        foreach ($affiliateCodes as $affiliateCode) {
            if (!isset($affiliateEarnings[$affiliateCode])) {
                $affiliateUser = $this->userRepository->getBy(['affiliate_code' => $affiliateCode])->first();
                if ($affiliateUser) {
                    $affiliateMessage = "Đơn hàng #{$order->code} mà bạn giới thiệu đã hoàn thành.";
                    $this->sendNotification(
                        $affiliateUser->id,
                        "Đơn hàng đã hoàn thành",
                        $affiliateMessage,
                        $affiliateUser->device_token
                    );
                }
            }
        }
    }



    public function confirm($id)
    {
        DB::beginTransaction();
        try {
            $order = $this->repository->findOrFail($id);
            $adminId = $order->admin_id;

            foreach ($order->details as $detail) {
                if ($detail->product_variation_id) {
                    // Kiểm tra số lượng tồn kho của biến thể trong kho admin
                    $inventory = \App\Models\AdminInventory::where('admin_id', $adminId)
                        ->where('product_id', $detail->product_id)
                        ->where('product_variation_id', $detail->product_variation_id)
                        ->first();

                    if (!$inventory || $inventory->qty < $detail->qty) {
                        DB::rollBack();
                        return $detail->productVariation->product->name;
                    }

                    // Trừ số lượng trong kho admin
                    $inventory->decrement('qty', $detail->qty);
                } else {
                    // Kiểm tra số lượng tồn kho của sản phẩm chính trong kho admin
                    $inventory = \App\Models\AdminInventory::where('admin_id', $adminId)
                        ->where('product_id', $detail->product_id)
                        ->whereNull('product_variation_id')
                        ->first();

                    if (!$inventory || $inventory->qty < $detail->qty) {
                        DB::rollBack();
                        return $detail->product->name;
                    }

                    // Trừ số lượng trong kho admin
                    $inventory->decrement('qty', $detail->qty);
                }

                // Xử lý giảm số lượng flash sale nếu có
                if ($detail->product->is_flash_sale) {
                    $flashSaleQuery = $detail->product->is_flash_sale->details()
                        ->where('product_id', $detail->product_id);

                    // Phân biệt biến thể vs sản phẩm đơn
                    if ($detail->product_variation_id) {
                        $flashSaleQuery->where('product_variation_id', $detail->product_variation_id);
                    } else {
                        $flashSaleQuery->whereNull('product_variation_id');
                    }

                    $flashSaleDetail = $flashSaleQuery->first();

                    if ($flashSaleDetail) {
                        $remainFlashSaleQty = $flashSaleDetail->qty - $flashSaleDetail->sold;

                        if ($remainFlashSaleQty >= $detail->qty) {
                            $flashSaleDetail->update(['sold' => $flashSaleDetail->sold + $detail->qty]);
                        } else {
                            if ($remainFlashSaleQty > 0) {
                                $flashSaleDetail->update(['sold' => $flashSaleDetail->qty]);
                            }
                        }
                    }
                } else {
                    if ($detail->productVariation) {
                        if ($detail->unit_price == $detail->productVariation->flashsale_price) {
                            DB::rollBack();
                            return 1;
                        }
                    } else {
                        if ($detail->unit_price == $detail->product->flashsale_price) {
                            DB::rollBack();
                            return 1;
                        }
                    }
                }
            }
            $this->sendNotification($order->user_id, "Thông báo", "Đơn hàng của bạn đã được xác nhận.", $order->user->device_token);
            $order->update(['status' => OrderStatus::Confirmed]);
            DB::commit();
            return true;
        } catch (Exception $e) {
            $this->logError('Failed to confirm order: ', $e);
            DB::rollBack();
            return false;
        }
    }

    private function handleDiscount($type)
    {
        $discount = $this->discountRepository->findOrFail($this->data['order']['discount_id']);
        $this->data['order']['discount_code'] = $discount->code;
        if ($type == 'store') {
            $discount->max_usage = $discount->max_usage - 1;
            $discount->save();
        } else {
            $oldOrder = $this->repository->findOrFail($this->data['order']['id']);
            $oldDiscountCode = $oldOrder->discount_code;

            // Nếu mã giảm giá cũ tồn tại và khác với mã giảm giá mới
            if ($oldDiscountCode && $oldDiscountCode != $discount->code) {
                $oldDiscount = $this->discountRepository->findByField('code', $oldDiscountCode)->first();

                if ($oldDiscount) {
                    // Tăng max_usage cho mã giảm giá cũ
                    $oldDiscount->max_usage = $oldDiscount->max_usage + 1;
                    $oldDiscount->save();
                }

                // Giảm max_usage cho mã giảm giá mới
                $discount->max_usage = $discount->max_usage - 1;
                $discount->save();
            }
        }
    }


    private function updateOrCreateDataOrderDetail()
    {
        $data = [];
        foreach ($this->data['order_detail']['id'] as $key => $value) {
            if ($value == 0) {
                $data['product_slug'][] = $this->data['order_detail']['product_slug'][$key];
                $data['product_variation_id'][] = $this->data['order_detail']['product_variation_id'][$key];
                $data['product_qty'][] = $this->data['order_detail']['product_qty'][$key];
                $data['unit_price'][] = $this->data['order_detail']['unit_price'][$key];
            } else {
                $this->repositoryOrderDetail->update(
                    $value,
                    [
                        'qty' => $this->data['order_detail']['product_qty'][$key]
                    ]
                );
            }
        }
        return $data;
    }

    public function delete($id)
    {
        $order = $this->repository->findOrFail($id);
        $order->update(['is_deleted' => 1]);
        return true;
    }

    public function addProduct(Request $request)
    {
        $data = $request->validated();
        $product = $this->productRepository->findByField('slug', $data['product_slug']);
        if ($product->type == ProductType::Variable) {
            $product = $product->load(['productVariation' => function ($query) use ($data) {
                $query->where('id', $data['product_variation_id'] ?? 0)->with('attribute_variations');
            }]);
            if (!$product->productVariation) {
                return false;
            }
        }
        return $product;
    }

    public function calculateTotal(Request $request)
    {
        $data = $request->validated('order_detail');
        $total = 0;
        $productSimple = [];
        $productVariation = [];
        foreach ($data['product_slug'] as $key => $value) {
            if ($data['product_variation_id'][$key] == 0) {
                $product = $this->productRepository->findByField('slug', $value);
                $productSimple[] = [
                    'id' => $product->id,
                    'qty' => $data['product_qty'][$key]
                ];
            } else {
                $productVariation[] = [
                    'id' => $data['product_variation_id'][$key],
                    'qty' => $data['product_qty'][$key]
                ];
            }
        }
        if (!empty($productSimple)) {
            $total += $this->totalPrice(
                $this->productRepository->getByIdsAndOrderByIds(array_column($productSimple, 'id')),
                array_column($productSimple, 'qty'),
                'simple'
            );
        }
        if (!empty($productVariation)) {
            $total += $this->totalPrice(
                $this->productVariationRepository->getByIdsAndOrderByIdsWithRelations(array_column($productVariation, 'id')),
                array_column($productVariation, 'qty'),
                'variation'
            );
        }
        return $total;
    }

    public function totalPrice($collect, $qty, $type)
    {
        $total = 0;
        $total += $collect->mapWithKeys(function ($item, $key) use ($qty, $type) {
            if ($type == 'simple') {
                $price = ($item->is_flash_sale ? $item->flashsale_price : $item->promotion_price) * $qty[$key];
            } else {
                $price = ($item->product->is_flash_sale ? $item->flashsale_price : $item->promotion_price) * $qty[$key];
            }
            return [$item->id => $price];
        })->sum();
        return $total;
    }
}
