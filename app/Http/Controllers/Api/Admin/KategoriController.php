<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreKategoriRequest;
use App\Http\Requests\UpdateKategoriRequest;
use App\Models\Kategori;
use Exception;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $response = $this->default_response;
        try {
            $kategoris = Kategori::all();

            $response['success'] = true;
            $response['message'] = 'Kategori list';
            $response['data'] = [
                'kategoris' => $kategoris
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

    public function store(StoreKategoriRequest $request)
    {
        $response = $this->default_response;
        try {
            $data = $request->validated();

            $kategori = new Kategori();
            $kategori->name = $data['name'];
            $kategori->description = $data['description'];
            $kategori->save();

            $response['success'] = true;
            $response['data'] =  [
                'kategori' => $kategori
            ];
            $response['message'] = 'Kategori berhasil di buat';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function show(String $id)
    {
        $response = $this->default_response;

        try{
            $kategori = Kategori::find($id);
            $response['success'] = true;
            $response['message'] = 'Kategori ketemu';
            $response['data'] = [
                'kategori' => $kategori
            ];
        }catch(Exception $e){
            $response['message'] = $e->getMessage();
        }

        return response()->json($response);
    }

    public function edit(Kategori $kategori)
    {
        
    }

    public function update(UpdateKategoriRequest $request, String $id)
    {
        $response = $this->default_response;
        try {
            $data = $request->validated();

            $kategori = Kategori::find($id);
            $kategori->name = $data['name'];
            $kategori->description = $data['description'];
            $kategori->save();

            $response['success'] = true;
            $response['data'] =  [
                'kategori' => $kategori
            ];
            $response['message'] = 'Kategori berhasi di Update';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }

    public function destroy(String $id)
    {
        $response = $this->default_response;
        try {
            $kategori = Kategori::find($id);
            $kategori->delete();
            $response['success'] = true;
            $response['message'] = 'Kategori Berhasil di Hapus';
        } catch (Exception $e) {
            $response['message'] = $e->getMessage();
        }
        return response()->json($response);
    }

}
