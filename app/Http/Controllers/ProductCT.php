<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductCT extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric',
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|numeric',
            'image' => 'required|max:255',
            'expired_at' => 'required|date',
            'modified_by' => 'required|max:255'
        ]);

        if ($validator->fails() ) {
            return response() -> json($validator->messages())->setStatusCode(422);
        }

        $payload =$validator->validated();

        Product::create([
            'category_id' => $payload['category_id'],
            'name' => $payload['name'],
            'description' => $payload['description'],
            'price' => $payload['price'],
            'image' => $payload['image'],
            'expired_at' => $payload['expired_at'],
            'modified_by' => $payload['modified_by']
        ]);

        return response()->json([
            'msg' => 'Data produk berhasil disimpan'
        ],201);
    }

    public function  showAll(){
        $products = Product::all();

        return response()->json([
            'msg' => 'Data produk keseluruhan',
            'data' => $products
        ],200);
    }

    public function showById($id){
        $product = Product::where('id', $id)->first();

        if($product) {

            return response()->json([
                'msg' => 'Data produk dengan ID: '.$id,
                'data' => $product
            ],200);
        
        }
    
        return response()->json([
            'msg' => 'Data produk dengan ID: '.$id.' tidak ditemukan',
        ],404);
    }

    public function showByName($name){
        $product = Product::where('name', 'LIKE', '%'.$name.'%')->get();

        if($product->count() > 0){

            return response()->json([
                'msg' => 'Data produk dengan nama yang mirip: '.$name,
                'data' => $product
            ],200);
        }

        return response()->json([
            'msg' => 'Data produk dengan nama yang mirip: '.$name.' tidak ditemukan',
        ],404);
    }

    public function update(Request $request, $id) {
        // memvalidasi inputan
        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|numeric',
            'name' => 'sometimes|max:255',
            'description' => 'sometimes',
            'price' => 'sometimes|numeric',
            'image' => 'sometimes|max:255',
            'modified_by' => 'sometimes|max:255',
            'expired_at' => 'sometimes|date'
        ]);

        // kondisi apabila inputan yang diinginkan tidak sesuai
        if($validator->fails()) {
            // response json akan dikirim jika ada inputan yang salah
            return response()->json($validator->getMessageBag())->setStatusCode(422);
        }

        $validated = $validator->validate();
        $product = Product::find($id);

        if($product) {
            Product::where('id', $id)->update($validated);

            return response()->json("Data dengan id : {$id} berhasil di update", 200);
        }

        return response()->json("Data dengan id : {$id} tidak ditemukan", 404);
    }

    public function delete($id) {
        $product = Product::where('id', $id)->get();

        if($product) {
            Product::where('id', $id)->delete();

            // response json akan dikirim
            return response()->json("Data produk dengan id: {$id} berhasil dihapus", 200);
        }
        
        return response()->json("Data produk dengan id: {$id} tidak ditemukan",404);
    }

    public function restore($id) {
        $product = Product::onlyTrashed()->where('id', $id);
        $product->restore();
        
        return response()->json("Data produk dengan id: {$id} berhasil dipulihkan", 200);

    }
}