<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Categories;

class CategoriesCT extends Controller
{
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails() ) {
            return response() -> json($validator->messages())->setStatusCode(422);
        }

        $payload =$validator->validated();

        Categories::create([
            'name' => $payload['name'],
        ]);

        return response()->json([
            'msg' => 'Data produk berhasil disimpan'
        ],201);
    }

    public function  showAll(){
        $categories = Categories::all();

        return response()->json([
            'msg' => 'Data produk keseluruhan',
            'data' => $categories
        ],200);
    }

    public function showById($id){
        $categories = Categories::where('id', $id)->first();

        if($categories) {

            return response()->json([
                'msg' => 'Data produk dengan ID: '.$id,
                'data' => $categories
            ],200);
        
        }
    
        return response()->json([
            'msg' => 'Data produk dengan ID: '.$id.' tidak ditemukan',
        ],404);
    }

    public function showByName($name){
        $categories = Categories::where('name', 'LIKE', '%'.$name.'%')->get();

        if($categories->count() > 0){

            return response()->json([
                'msg' => 'Data produk dengan nama yang mirip: '.$name,
                'data' => $categories
            ],200);
        }

        return response()->json([
            'msg' => 'Data produk dengan nama yang mirip: '.$name.' tidak ditemukan',
        ],404);
    }

    public function update(Request $request, $id) {
        // memvalidasi inputan
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|max:255'
        ]);

        // kondisi apabila inputan yang diinginkan tidak sesuai
        if($validator->fails()) {
            // response json akan dikirim jika ada inputan yang salah
            return response()->json($validator->getMessageBag())->setStatusCode(422);
        }

        $validated = $validator->validate();
        $categories = Categories::find($id);

        if($categories) {
            Categories::where('id', $id)->update($validated);

            return response()->json("Data dengan id : {$id} berhasil di update", 200);
        }

        return response()->json("Data dengan id : {$id} tidak ditemukan", 404);
    }

    public function delete($id) {
        $categories = Categories::where('id', $id)->get();

        if($categories) {
            Categories::where('id', $id)->delete();

            // response json akan dikirim
            return response()->json("Data produk dengan id: {$id} berhasil dihapus", 200);
        }
        
        return response()->json("Data produk dengan id: {$id} tidak ditemukan",404);
    }

    public function restore($id) {
        $categories = Categories::onlyTrashed()->where('id', $id);
        $categories->restore();
        
        return response()->json("Data produk dengan id: {$id} berhasil dipulihkan", 200);

    }
}