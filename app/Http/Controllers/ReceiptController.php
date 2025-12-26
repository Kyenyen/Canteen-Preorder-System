<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ReceiptService;
use App\Notifications\ReceiptMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    protected ReceiptService $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /** View Receipt */
    public function viewReceipt($orderId)
    {
        $user = Auth::user();

        // Find the order
        $order = Order::where('order_id', $orderId)->with(['products', 'user'])->firstOrFail();

        // Authorization: Admin can view any receipt, User can only view their own
        if ($user->role !== 'admin' && $order->user_id !== $user->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Generate HTML receipt
            $html = $this->receiptService->generateReceiptHtml($order);

            // Return as HTML response or PDF based on query parameter
            if (request()->query('format') === 'pdf') {
                return $this->generatePdfResponse($html, $order->order_id, 'download');
            }

            // Return HTML for viewing
            return response()->json([
                'html' => $html,
                'order_id' => $order->order_id
            ]);
        } catch (\Exception $e) {
            Log::error('Receipt generation error', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to generate receipt'], 500);
        }
    }

    /** Download Receipt */
    public function downloadReceipt($orderId)
    {
        $user = Auth::user();

        $order = Order::where('order_id', $orderId)->with(['products', 'user'])->firstOrFail();

        // Authorization: Admin can download any receipt, User can only download their own
        if ($user->role !== 'admin' && $order->user_id !== $user->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $html = $this->receiptService->generateReceiptHtml($order);
            return $this->generatePdfResponse($html, $order->order_id, 'download');
        } catch (\Exception $e) {
            Log::error('Receipt PDF generation error', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to generate PDF'], 500);
        }
    }

    /** Request Receipt */
    public function requestReceipt($orderId)
    {
        $user = Auth::user();

        // Find the order
        $order = Order::where('order_id', $orderId)->with(['products', 'user'])->firstOrFail();

        // Authorization: User can only request receipt for their own orders
        if ($order->user_id !== $user->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            // Send receipt via email
            $order->user->notify(new ReceiptMail($order));

            Log::info('Receipt email sent', ['order_id' => $orderId, 'user_id' => $user->user_id]);

            return response()->json([
                'message' => 'Receipt has been sent to your email address',
                'email' => $order->user->email
            ]);
        } catch (\Exception $e) {
            Log::error('Receipt email error', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to send receipt email'], 500);
        }
    }

    /** Send Receipt Email */
    public function sendReceiptEmail($orderId)
    {
        try {
            $order = Order::where('order_id', $orderId)->with(['products', 'user'])->firstOrFail();

            // Send receipt via email
            $order->user->notify(new ReceiptMail($order));

            Log::info('Receipt email sent automatically', ['order_id' => $orderId]);

            return true;
        } catch (\Exception $e) {
            Log::error('Auto receipt email error', ['order_id' => $orderId, 'error' => $e->getMessage()]);
            return false;
        }
    }

    /** Generate PDF Response */
    private function generatePdfResponse($html, $orderId, $action = 'view')
    {
        // Using dompdf library
        try {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'Receipt_' . $orderId . '.pdf';
            $output = $dompdf->output();

            if ($action === 'download') {
                return response($output, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Length' => strlen($output)
                ]);
            }

            // View in browser
            return response($output, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
                'Content-Length' => strlen($output)
            ]);
        } catch (\Exception $e) {
            Log::error('PDF generation error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to generate PDF: ' . $e->getMessage()], 500);
        }
    }
}
