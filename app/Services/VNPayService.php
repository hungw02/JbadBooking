<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VNPayService
{
    /**
     * Create payment URL for VNPay
     *
     * @param int $userId
     * @param string $bookingType 'single' or 'subscription'
     * @param int $amount Amount in VND
     * @param string $paymentType 'deposit' or 'full'
     * @return string
     */
    public function createPaymentUrl($userId, $bookingType, $amount, $paymentType)
    {
        // Cấu hình VNPay
        $vnp_TmnCode = env('VNPAY_TMN_CODE', ''); // Terminal ID assigned by VNPay
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', ''); // Secret key assigned by VNPay
        $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_ReturnUrl = route('payment.vnpay.return');
        
        // Thông tin thanh toán
        $vnp_TxnRef = $bookingType . '_' . $userId . '_' . time(); // Mã đơn hàng unique
        $vnp_OrderInfo = $paymentType === 'deposit' ? 
            'Dat coc dat san ' . $bookingType : 
            'Thanh toan toan bo ' . $bookingType;
        
        $vnp_OrderType = 'billpayment'; // Mặc định
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $amount * 100, // VNPay requires amount in smallest currency unit (VND × 100)
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $expire
        );
        
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnp_Url = $vnp_Url . "?" . $query;
        
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }
        
        return $vnp_Url;
    }
    
    /**
     * Validate VNPay payment return
     * 
     * @param array $requestData Data returned from VNPay
     * @return array
     */
    public function validatePayment($requestData)
    {
        // Lấy hash secret từ config
        $vnp_HashSecret = env('VNPAY_HASH_SECRET', '');
        
        // Lấy secure hash từ request
        $vnp_SecureHash = $requestData['vnp_SecureHash'] ?? '';
        
        // Xóa secure hash từ data để tạo chuỗi hash mới
        $inputData = $requestData;
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);
        
        // Sắp xếp mảng theo key
        ksort($inputData);
        
        // Tạo chuỗi hash mới
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }
        
        // Tạo secure hash mới
        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        
        // Kiểm tra hash và trạng thái thanh toán
        $isValidHash = ($secureHash === $vnp_SecureHash);
        $isSuccessful = ($requestData['vnp_ResponseCode'] === '00');
        
        // Lấy thông tin từ mã tham chiếu
        $txnRef = $requestData['vnp_TxnRef'] ?? '';
        $refParts = explode('_', $txnRef);
        
        // Mặc định kết quả
        $result = [
            'success' => false,
            'message' => 'Thanh toán thất bại',
            'data' => [
                'booking_type' => $refParts[0] ?? '',
                'user_id' => $refParts[1] ?? 0,
                'amount' => ($requestData['vnp_Amount'] ?? 0) / 100, // Convert back from smallest unit
                'transaction_id' => $requestData['vnp_TransactionNo'] ?? '',
                'bank_code' => $requestData['vnp_BankCode'] ?? '',
                'transaction_status' => $requestData['vnp_ResponseCode'] ?? '',
                'transaction_date' => $requestData['vnp_PayDate'] ?? '',
            ]
        ];
        
        // Nếu hash không khớp
        if (!$isValidHash) {
            $result['message'] = 'Dữ liệu không hợp lệ!';
            Log::error('VNPay validation failed: Invalid hash', ['request' => $requestData]);
            return $result;
        }
        
        // Nếu thanh toán không thành công
        if (!$isSuccessful) {
            $result['message'] = 'Thanh toán không thành công.';
            Log::error('VNPay payment failed', ['code' => $requestData['vnp_ResponseCode'] ?? 'Unknown']);
            return $result;
        }
        
        // Thanh toán thành công
        $result['success'] = true;
        $result['message'] = 'Thanh toán thành công';
        
        return $result;
    }
} 