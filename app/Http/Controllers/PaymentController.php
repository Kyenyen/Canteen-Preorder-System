<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set Stripe API key
        Stripe::setApiKey(config('stripe.secret'));
    }

    /** Store Payment */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,order_id',
            'method' => 'required|in:fpx,card,cash,duitnow,grabpay',
        ]);

        $order = Order::where('order_id', $request->order_id)
                      ->where('user_id', Auth::id())
                      ->firstOrFail();

        // Check if already paid
        if ($order->payment) {
             return response()->json(['message' => 'Order already paid'], 400);
        }

        try {
            DB::beginTransaction();

            // 1. Generate Custom Payment ID (P0001, etc.)
            $lastPayment = Payment::orderBy('payment_id', 'desc')->first();
            $number = $lastPayment ? intval(substr($lastPayment->payment_id, 1)) + 1 : 1;
            $newPaymentId = 'P' . str_pad($number, 4, '0', STR_PAD_LEFT);

            // 2. Create Payment Record
            Payment::create([
                'payment_id' => $newPaymentId,
                'order_id' => $order->order_id,
                'method' => $request->method,
                'refunded' => 0,      // Default value
                'refund_date' => null, // Default value
                'paid_at' => now(),
            ]);

            // Status remains 'Preparing' - no need to update

            DB::commit();

            return response()->json(['message' => 'Payment successful']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Payment failed', 'details' => $e->getMessage()], 500);
        }
    }

    /** Create Payment Intent */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method_type' => 'nullable|string|in:card,fpx,grabpay',
            'order_data' => 'nullable|array',
        ]);

        try {
            // Create a PaymentIntent with the amount and currency
            // Stripe expects amount in cents (smallest currency unit)
            $amountInCents = (int)($request->amount * 100);

            $paymentMethodType = $request->input('payment_method_type', 'card');

            // Configure payment intent based on payment method type
            $intentConfig = [
                'amount' => $amountInCents,
                'currency' => config('stripe.currency', 'myr'),
                'metadata' => [
                    'user_id' => Auth::id(),
                    'order_data' => json_encode($request->order_data ?? []),
                ],
            ];

            // For FPX, specify the payment method types
            if ($paymentMethodType === 'fpx') {
                $intentConfig['payment_method_types'] = ['fpx'];
            } elseif ($paymentMethodType === 'grabpay') {
                $intentConfig['payment_method_types'] = ['grabpay'];
            } elseif ($paymentMethodType === 'card') {
                $intentConfig['payment_method_types'] = ['card'];
            } else {
                $intentConfig['automatic_payment_methods'] = ['enabled' => true];
            }

            $paymentIntent = PaymentIntent::create($intentConfig);

            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
                'paymentIntentId' => $paymentIntent->id,
                'amount' => $amountInCents, // Include amount for frontend display
            ]);

        } catch (\Exception $e) {
            Log::error('Stripe Payment Intent Creation Failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create payment intent',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /** Confirm Stripe Payment */
    public function confirmStripePayment(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'payment_method' => 'nullable|string|in:card,fpx,grabpay,FPX,GrabPay',
            'order_data' => 'required|array',
            'order_data.items' => 'required|array|min:1',
            'order_data.note' => 'nullable|string',
            'order_data.pickup_time' => 'required|string',
            'order_data.payment_method' => 'required|string',
            'order_data.dining_option' => 'required|string',
            'order_data.total' => 'required|numeric|min:0',
        ]);

        try {
            // Retrieve the PaymentIntent from Stripe to verify its status
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'error' => 'Payment not completed',
                    'status' => $paymentIntent->status
                ], 400);
            }

            DB::beginTransaction();

            // Generate Custom Order ID
            $lastOrder = Order::orderBy('order_id', 'desc')->first();
            $orderNumber = $lastOrder ? intval(substr($lastOrder->order_id, 1)) + 1 : 1;
            $newOrderId = 'O' . str_pad($orderNumber, 4, '0', STR_PAD_LEFT);

            // Create Order
            $order = Order::create([
                'order_id' => $newOrderId,
                'user_id' => Auth::id(),
                'date' => now()->toDateString(),
                'pickup_time' => $request->order_data['pickup_time'],
                'note' => $request->order_data['note'] ?? null,
                'total' => $request->order_data['total'],
                'status' => 'Preparing',
                'dining_option' => $request->order_data['dining_option'],
            ]);

            // Attach ordered products with subtotal
            foreach ($request->order_data['items'] as $item) {
                // Get product price
                $product = \App\Models\Product::findOrFail($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                
                $order->products()->attach($item['product_id'], [
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal
                ]);
            }

            // Generate Custom Payment ID
            $lastPayment = Payment::orderBy('payment_id', 'desc')->first();
            $paymentNumber = $lastPayment ? intval(substr($lastPayment->payment_id, 1)) + 1 : 1;
            $newPaymentId = 'P' . str_pad($paymentNumber, 4, '0', STR_PAD_LEFT);

            // Determine payment method
            $paymentMethod = $request->input('payment_method', 'card');
            
            // Store FPX as uppercase, grabpay as GrabPay
            if ($paymentMethod === 'fpx') {
                $paymentMethod = 'FPX';
            } elseif ($paymentMethod === 'grabpay') {
                $paymentMethod = 'GrabPay';
            }

            // Create Payment Record
            Payment::create([
                'payment_id' => $newPaymentId,
                'order_id' => $order->order_id,
                'method' => $paymentMethod,
                'stripe_payment_intent_id' => $paymentIntent->id,
                'stripe_charge_id' => $paymentIntent->latest_charge ?? null,
                'stripe_payment_status' => $paymentIntent->status,
                'paid_at' => now(),
                'refunded' => false,
                'refund_date' => null,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Order created and payment confirmed successfully',
                'order_id' => $newOrderId,
                'payment_id' => $newPaymentId,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Stripe Payment Confirmation Failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Payment confirmation failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /** Handle Webhook */
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Webhook Error: Invalid payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Webhook Error: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentFailed($paymentIntent);
                break;

            case 'charge.refunded':
                $charge = $event->data->object;
                $this->handleChargeRefunded($charge);
                break;

            default:
                Log::info('Unhandled webhook event type: ' . $event->type);
        }

        return response()->json(['status' => 'success']);
    }

    /** Handle Payment Intent Succeeded */
    private function handlePaymentIntentSucceeded($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) {
            Log::error('Payment Intent succeeded but no order_id in metadata');
            return;
        }

        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'stripe_payment_status' => 'succeeded',
                'stripe_charge_id' => $paymentIntent->latest_charge ?? $payment->stripe_charge_id,
            ]);
            Log::info("Payment {$payment->payment_id} updated to succeeded");
        } else {
            Log::info("Payment Intent {$paymentIntent->id} succeeded but no payment record found yet");
        }
    }

    /** Handle Payment Intent Failed */
    private function handlePaymentIntentFailed($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) {
            Log::error('Payment Intent failed but no order_id in metadata');
            return;
        }

        $payment = Payment::where('stripe_payment_intent_id', $paymentIntent->id)->first();

        if ($payment) {
            $payment->update([
                'stripe_payment_status' => 'failed',
            ]);
            Log::warning("Payment {$payment->payment_id} marked as failed");
        }
    }

    /** Handle Charge Refunded */
    private function handleChargeRefunded($charge)
    {
        $payment = Payment::where('stripe_charge_id', $charge->id)->first();

        if ($payment) {
            $payment->update([
                'refunded' => true,
                'refund_date' => now(),
                'stripe_payment_status' => 'refunded',
            ]);
            Log::info("Payment {$payment->payment_id} refunded");
        }
    }

    /** Refund Payment */
    public function refundPayment(Request $request, $paymentId)
    {
        $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $payment = Payment::where('payment_id', $paymentId)->firstOrFail();

            if ($payment->refunded) {
                return response()->json(['message' => 'Payment already refunded'], 400);
            }

            if (!in_array($payment->method, ['card', 'fpx'])) {
                return response()->json(['message' => 'Only Stripe payments (card/FPX) can be refunded through this endpoint'], 400);
            }

            if (!$payment->stripe_charge_id) {
                return response()->json(['message' => 'No Stripe charge ID found'], 400);
            }

            // Create refund in Stripe
            $refund = \Stripe\Refund::create([
                'charge' => $payment->stripe_charge_id,
                'reason' => 'requested_by_customer',
            ]);

            // Update payment record
            $payment->update([
                'refunded' => true,
                'refund_date' => now(),
                'stripe_payment_status' => 'refunded',
            ]);

            return response()->json([
                'message' => 'Payment refunded successfully',
                'refund_id' => $refund->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Refund Failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Refund failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}