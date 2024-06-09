<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCardRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\AddToCart;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;

class AddToCartController extends Controller
{
    public function index(Request $request)
    {
        $response = $this->default_response;

        // get data cart berdasarkan customer id
        $add_to_card = AddToCart::where('customer_id', $request->user()->id)
                            ->whereNull('checkout_id')
                            ->with('product')
                            ->get();

        $response['success'] = true;
        $response['message'] = 'Cart list';
        $response['data'] = $add_to_card;

        return response()->json($response);
    }

    public function store(StoreCardRequest $request)
    {
        // dd($request->all());
        $response = $this->default_response;
    
        // Validasi qty tak lebih dari stok
        $product = Product::find($request->product_id);
    
        if (!$product) {
            $response['success'] = false;
            $response['message'] = 'Product not found';
            return response()->json($response);
        }
    
        if ($request->qty > $product->stok) {
            $response['success'] = false;
            $response['message'] = 'Stock not enough';
            return response()->json($response);
        }
    
        // dd($request->user());
        // Simpan ke table cart
        $add_to_cart = AddToCart::where('product_id', $request->product_id)
            ->where('customer_id', $request->user()->id)
            ->whereNull('checkout_id')
            ->first();
    
        if ($add_to_cart) {
            $add_to_cart->qty += $request->qty;
        } else {
            $add_to_cart = new AddToCart();
            $add_to_cart->product_id = $request->product_id;
            $add_to_cart->customer_id = $request->user()->id;
            $add_to_cart->qty = $request->qty;
        }
    
        $add_to_cart->harga_satuan = (int)$product->harga;
        $add_to_cart->total_harga = (int)$add_to_cart->harga_satuan * $add_to_cart->qty;
        $add_to_cart->save();
    
        $response['success'] = true;
        $response['message'] = 'Produk berhasil masuk ke cart';
        $response['data'] = $add_to_cart;
        return response()->json($response);
    }
    

    public function show(string $id)
    {
        $response = $this->default_response;
    
        try {
            // Temukan cart item berdasarkan ID, customer_id, dan belum di-checkout, dengan relasi produk
            $add_to_cart = AddToCart::where('customer_id', auth()->id())
                ->whereNull('checkout_id')
                ->with('product')
                ->find($id);
    
            // Jika cart item tidak ditemukan
            if (empty($add_to_cart)) {
                $response['success'] = false;
                $response['message'] = 'Cart item not found';
                return response()->json($response);
            }
    
            $response['success'] = true;
            $response['message'] = 'Cart item successfully retrieved';
            $response['data'] = $add_to_cart;
        } catch (Exception $e) {
            // Tangani kesalahan
            $response['success'] = false;
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }
    
        return response()->json($response);
    }
    


    public function update(Request $request, string $id)
    {
        $response = $this->default_response;

        // Validasi input
        $request->validate([
            'qty' => 'required|numeric|min:1',
        ]);

        try {
            // Temukan cart item berdasarkan ID, customer_id, dan belum di-checkout, dengan relasi produk
            $add_to_cart = AddToCart::where('customer_id', $request->user()->id)
                ->whereNull('checkout_id')
                ->with('product')
                ->find($id);

            // Jika cart item tidak ditemukan
            if (empty($add_to_cart)) {
                $response['success'] = false;
                $response['message'] = 'Cart not found';
                return response()->json($response);
            }

            // Periksa apakah qty melebihi stok produk
            if ($request->qty > $add_to_cart->product->stok) {
                $response['success'] = false;
                $response['message'] = 'Stock not enough';
                return response()->json($response);
            }

            // Update harga satuan, qty, dan total harga
            $add_to_cart->harga_satuan = (int)$add_to_cart->product->harga;
            $add_to_cart->qty = $request->qty;
            $add_to_cart->total_harga = $add_to_cart->harga_satuan * $add_to_cart->qty;
            $add_to_cart->save();

            // Persiapkan respons sukses
            $response['success'] = true;
            $response['message'] = 'Cart successfully updated';
            $response['data'] = $add_to_cart;
        } catch (Exception $e) {
            // Tangani kesalahan
            $response['success'] = false;
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        return response()->json($response);
    }

    public function destroy(Request $request, string $id)
    {
        $response = $this->default_response;

        try {
            // Temukan cart item berdasarkan ID, customer_id, dan belum di-checkout
            $add_to_cart = AddToCart::where('customer_id', $request->user()->id)
                ->whereNull('checkout_id')
                ->find($id);

            // Jika cart item tidak ditemukan
            if (empty($add_to_cart)) {
                $response['success'] = false;
                $response['message'] = 'Cart not found';
                return response()->json($response);
            }

            // Hapus cart item
            $add_to_cart->delete();
            $response['success'] = true;
            $response['message'] = 'Cart successfully deleted'; 
        } catch (Exception $e) {
            // Tangani kesalahan
            $response['success'] = false;
            $response['message'] = 'An error occurred: ' . $e->getMessage();
        }

        return response()->json($response);
    }

    

    
}
