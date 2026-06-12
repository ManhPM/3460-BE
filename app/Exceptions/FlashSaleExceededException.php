<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Exception thrown when flash sale quantity is exceeded.
 * Returns a structured JSON response with error_code for Flutter UX handling.
 */
class FlashSaleExceededException extends Exception
{
    protected string $productName;
    protected int $remainingQty;
    protected ?int $productId;
    protected bool $isBuyNow;

    public function __construct(
        string $productName,
        int $remainingQty,
        ?int $productId = null,
        bool $isBuyNow = false,
    ) {
        $this->productName = $productName;
        $this->remainingQty = $remainingQty;
        $this->productId = $productId;
        $this->isBuyNow = $isBuyNow;

        parent::__construct(
            'Số lượng sản phẩm "' . $productName . '" vượt quá số lượng flash sale còn lại (' . $remainingQty . ' sản phẩm).'
        );
    }

    /**
     * Render exception into HTTP response (auto-called by Laravel's exception handler).
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'status'     => 400,
            'error_code' => 'FLASH_SALE_EXCEEDED',
            'message'    => $this->getMessage(),
            'data'       => [
                'product_name'  => $this->productName,
                'product_id'    => $this->productId,
                'remaining_qty' => $this->remainingQty,
                'is_buy_now'    => $this->isBuyNow,
            ],
        ], 400);
    }
}
