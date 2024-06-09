<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AddToCart;
use App\Models\Checkout;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    public function index()
    {
        $response = $this->default_response;

        try {
            // Ambil semua item keranjang yang belum checkout untuk pengguna yang terotentikasi
            $checkouts = Checkout::where('customer_id', auth()->user()->id)
                ->with('cart')
                ->get();

            $response['success'] = true;
            $response['data'] = $checkouts;
            $response['message'] = 'Cart items retrieved successfully';
        } catch (Exception $e) {
            // Tangani kesalahan
            $response['success'] = false;
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        return response()->json($response);
    }


    public function store(Request $request)
    {
        $response = $this->default_response;
    
        // Validasi input
        $request->validate([
            'card_id' => 'required|array',
            'card_id.*' => 'required|exists:add_to_carts,id',
            'metode_pembayaran' => 'required',
            'metode_pengiriman' => 'required|in:COD,JNE,SICEPAT',
            'tujuan' => 'required',
        ]);
    
        // Biaya pengiriman
        $biaya_pengiriman = [
            'COD' => 0,
            'JNE' => 25000,
            'SICEPAT' => 30000
        ];
    
        DB::beginTransaction();
    
        // Ambil semua item keranjang yang sesuai dengan input
        $carts = AddToCart::where('customer_id', auth()->user()->id)
            ->whereNull('checkout_id')
            ->whereIn('id', $request->card_id)
            ->with("product")
            ->get();
    
        if ($carts->isEmpty()) {
            $response['message'] = 'Cart is empty';
            $response['success'] = false;
            return response()->json($response, 404);
        }
    
        // Validasi stok dan hitung total harga produk
        $total_harga_produk = 0;
        foreach ($carts as $cart) {
            if ($cart->product->stok < $cart->qty) {
                $response['message'] = 'Stock produk tidak cukup: ' . $cart->qty . ', stok tersedia ' . $cart->product->stok . ' (' . $cart->product->product_name . ')';
                $response['success'] = false;
                return response()->json($response, 404);
            }
    
            $total_harga_produk += $cart->total_harga;
        }
    
        // Kurangi stok produk
        foreach ($carts as $cart) {
            $cart->product->decrement('stok', $cart->qty);
        }
    
        // Simpan data checkout
        $checkout = new Checkout();
        $checkout->customer_id = auth()->user()->id;
        $checkout->total_harga_product = $total_harga_produk;
        $checkout->biaya_pengiriman = $biaya_pengiriman[$request->metode_pengiriman];
        $checkout->metode_pembayaran = $request->metode_pembayaran;
        $checkout->metode_pengiriman = $request->metode_pengiriman;
        $checkout->tujuan = $request->tujuan;
        $checkout->save();


    
        // Update data Cart dengan ID checkout
        $carts->each(function ($cart) use ($checkout) {
            $cart->update(['checkout_id' => $checkout->id]);
        });
    
        DB::commit();
    
        $response['data'] = $checkout;
        $response['success'] = true;
        $response['message'] = 'Pesanan Berhasil di Checkout';
        return response()->json($response, 200);
    }
    
    public function destroy(string $id)
    {
        $response = $this->default_response;
    
        try {
            // Temukan checkout berdasarkan ID dan customer_id
            $checkout = Checkout::where('customer_id', auth()->id())
                ->find($id);
    
            // Jika checkout tidak ditemukan
            if (!$checkout) {
                $response['success'] = false;
                $response['message'] = 'Checkout not found';
                return response()->json($response, 404);
            }
    
            // Hapus checkout
            $checkout->delete();
    
            $response['success'] = true;
            $response['message'] = 'Checkout deleted successfully';
        } catch (Exception $e) {
            // Tangani kesalahan        
            $response['success'] = false;
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
    
        return response()->json($response);
    }
    
}
