<?php

namespace App\Http\Controllers\Site;

use Illuminate\Http\Request;
use App\Contracts\OrderContract;
use App\Http\Controllers\Controller;
use App\Services\PayPalService;

class CheckoutController extends Controller
{
    protected $orderRepository;
    protected $payPal;

    public function __construct(OrderContract $orderRepository, PayPalService $payPal)
    {
        $this->payPal = $payPal;
        $this->orderRepository = $orderRepository;
    }

    public function getCheckout()
    {
        return view('site.pages.checkout');
    }

    public function placeOrder(Request $request)
{
    // Before storing the order we should implement the
    // request validation which I leave it to you
    $order = $this->orderRepository->storeOrderDetails($request->all());

    // You can add more control here to handle if the order
    // is not stored properly
    if ($order) {
        $this->payPal->processPayment($order);
    }

    return redirect()->back()->with('message','Order not placed');
}
public function processPayment($order)
{
// Add shipping amount if you want to charge for shipping
$shipping = sprintf('%0.2f', 0);
// Add any tax amount if you want to apply any tax rule
$tax = sprintf('%0.2f', 0);
// Create a new instance of Payer class
$payer = new Payer();
$payer->setPaymentMethod("paypal");
}
public function complete(Request $request)
{
    $paymentId = $request->input('paymentId');
    $payerId = $request->input('PayerID');

    $status = $this->payPal->completePayment($paymentId, $payerId);

    $order = Order::where('order_number', $status['invoiceId'])->first();
    $order->status = 'processing';
    $order->payment_status = 1;
    $order->payment_method = 'PayPal -'.$status['salesId'];
    $order->save();

    Cart::clear();
    return view('site.pages.success', compact('order'));
}

}
