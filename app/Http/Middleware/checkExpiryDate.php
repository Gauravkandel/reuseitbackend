<?php

namespace App\Http\Middleware;

use App\Models\product;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AlertNotification;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

class checkExpiryDate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $transactions = Transaction::where('expiredat', '<', now())->where('expired', 0)->get();

            foreach ($transactions as $transaction) {
                $productIds = json_decode($transaction->product_ids);

                foreach ($productIds as $productId) {
                    $product = Product::find($productId);
                    $user = User::find($product->user_id);
                    if ($product) {
                        $product->featured_package = 0;
                        $product->save();
                    }
                }
                if ($user) {
                    Notification::send($user, new AlertNotification);
                }
                $transaction->expired = 1;
                $transaction->save();
            }
        } catch (\Exception $e) {
            \Log::error('Error occurred in checkExpiryDate middleware: ' . $e->getMessage());
        }

        return $next($request);
    }
}
