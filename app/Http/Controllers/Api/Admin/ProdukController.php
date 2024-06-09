<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProdukRequest;
use App\Http\Requests\UpdateProdukRequest;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProdukController extends Controller
{
    public function index()
    {
        $response = $this->default_response;
        try {
            $produks = product::all();

            $response['success'] = true;
            $response['message'] = 'Produk list';
            $response['data'] = [
                'products' => $produks
            ];
        } catch(Exception $e) {
            $response['message'] = $e->getMessage();
        }
        
        return response()->json($response);
    }

    public function create()
    {
        //
    }

    public function store(StoreProdukRequest $request)
    {
    $response = $this->default_response;

    try {
        // Validate request data
        $data = $request->validated();

        // Initialize the path variable
        $imagePath = null;

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imagePath = $file->storeAs('project-image', $file->hashName(), 'public');
        }

        // Start a database transaction
        DB::beginTransaction();

        // Create and save the product
        $product = new Product();
        $product->product_name = $data['product_name'];
        $product->description = $data['description'];
        $product->harga = $data['harga'];
        $product->stok = $data['stok'];
        $product->image = $imagePath;
        $product->kategori_id = $data['kategori_id'];
        $product->save();

        // Commit the transaction
        DB::commit();

        // Prepare the response
        $response['success'] = true;
        $response['data'] = [
            'product' => $product->with('kategori')->find($product->id),
        ];
        $response['message'] = 'Produk Berhasil dibuat';
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        DB::rollBack();
        $response['message'] = $e->getMessage();
    }

    return response()->json($response);
    }


    public function show(String $id)
    {
    $response = $this->default_response;

    try {
        // Retrieve the product by its ID, including the associated category
        $product = Product::with('kategori')->findOrFail($id);

        // Prepare the response
        $response['success'] = true;
        $response['data'] = [
            'product' => $product,
        ];
        $response['message'] = 'Produk berhasil ditemukan';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    return response()->json($response);

    }

    public function edit(Product $product)
    {
        //
    }

    public function update(UpdateProdukRequest $request, String $id)
{
    $response = $this->default_response;

    try {
        // Validate the request data
        $data = $request->validated();

        // Find the product by ID
        $product = Product::findOrFail($id);

        // Handle image upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            // Store the new image
            $file = $request->file('image');
            $imagePath = $file->storeAs('project-image', $file->hashName(), 'public');
            $product->image = $imagePath;
        }

        // Update product details
        $product->product_name = $data['product_name'];
        $product->description = $data['description'];
        $product->harga = $data['harga'];
        $product->stok = $data['stok'];
        $product->kategori_id = $data['kategori_id'];

        // Save the updated product
        $product->save();

        // Prepare the response
        $response['success'] = true;
        $response['data'] = [
            'product' => $product->with('kategori')->find($product->id),
        ];
        $response['message'] = 'Produk berhasil diperbarui';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    return response()->json($response);
}

public function destroy(String $id)
{
    $response = $this->default_response;

    try {
        // Find the product by ID
        $product = Product::findOrFail($id);

        // Delete the associated image if it exists
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        // Delete the product
        $product->delete();

        // Prepare the response
        $response['success'] = true;
        $response['message'] = 'Produk berhasil dihapus';
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }

    return response()->json($response);
}




  
}