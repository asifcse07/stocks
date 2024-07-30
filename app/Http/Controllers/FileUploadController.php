<?php

namespace App\Http\Controllers;

use App\Models\ProductData;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    //

    public function uploadFile(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt', // Example validation rules
        ]);
        $file = $request->file('file');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('uploads', $fileName);

        return redirect()->back()->with('message', 'File uploaded successfully.');
    }


    public function deleteProduct($productId): \Illuminate\Http\RedirectResponse
    {
        $product = ProductData::find($productId);
        $product->delete();
        return redirect()->back()->with('message', 'Deleted successfully.');
    }
}
