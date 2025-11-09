<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Material;
use App\Models\Product;
use Illuminate\Http\Request;


class CategoryController extends Controller
{
    public function getAll()
    {
        $companyId = session('company_id');
        $categories = Category::where('company_id', $companyId)->orderBy('type')->orderBy('name')->get();
        return view('Admin.all_category',compact('categories'));
    }

    public function store(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->type = $request->type;
        $category->company_id = session('company_id');

        $category->save();
        return Redirect()->route('all.categories');
        
    }

    public function update(Request $request)
    {
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->type = $request->type;
        $category->company_id = session('company_id');
        $category->save();
        return redirect()->route('all.categories');
    }

    public function destroy($id, Request $request)
    {
        $category = Category::findOrFail($id);
        $type = $request->input('type');
        if ($type === 'material') {
            // Update related materials
            Material::where('category_id', $category->id)->update(['category_id' => null]);
        } elseif ($type === 'product') {
            // Update related products
            Product::where('category_id', $category->id)->update(['category_id' => null]);
        }
        $category->delete();

        return response()->json(['success' => 'Category deleted successfully']);
    }

}