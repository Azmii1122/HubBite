<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Update store operational status (open/closed).
     */
    public function updateStatus(Request $request): JsonResponse|RedirectResponse
    {
        $store = $request->user()->store;

        $request->validate([
            'store_status' => ['required', 'in:open,closed'],
        ]);

        $store->update([
            'store_status' => $request->store_status,
        ]);

        $statusText = $request->store_status === 'open' ? 'Buka (Open)' : 'Tutup (Closed)';
        $message = "Status toko berhasil diubah menjadi {$statusText}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'store_status' => $store->store_status,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Update store crowd status (low/medium/high).
     */
    public function updateCrowd(Request $request): JsonResponse|RedirectResponse
    {
        $store = $request->user()->store;

        $request->validate([
            'crowd_status' => ['required', 'in:low,medium,high'],
        ]);

        $store->update([
            'crowd_status' => $request->crowd_status,
        ]);

        $crowdLabel = match ($request->crowd_status) {
            'low' => 'Low (Sepi)',
            'medium' => 'Medium (Sedang)',
            'high' => 'High (Ramai)',
            default => $request->crowd_status,
        };
        $message = "Status keramaian toko berhasil diperbarui ke {$crowdLabel}.";

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'crowd_status' => $store->crowd_status,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Toggle Go Live operational state.
     */
    public function goLive(Request $request): JsonResponse|RedirectResponse
    {
        $store = $request->user()->store;

        if ($store->is_live) {
            $store->update(['is_live' => false]);
            $message = 'Toko sekarang dalam mode Offline (Dijeda). Pesanan baru sementara tidak diterima.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'is_live' => false,
                    'message' => $message,
                ]);
            }

            return back()->with('warning', $message);
        }

        $check = $store->validateGoLiveRequirements();
        if (! $check['can_go_live']) {
            $reasonList = implode(' ', $check['reasons']);
            $errorMessage = "Tidak dapat Go Live: {$reasonList}";

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'is_live' => false,
                    'message' => $errorMessage,
                    'reasons' => $check['reasons'],
                ], 422);
            }

            return back()->with('error', $errorMessage);
        }

        $store->update(['is_live' => true]);
        $message = 'Toko Anda sekarang AKTIF (Go Live)! Pelanggan dapat memesan menu Anda.';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_live' => true,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
