<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }


    public function index()
    {
        $products = $this->productService->all();
        return view('products.index', compact('products'));
    }


    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create($request->validated());
        return response()->json([
            'status' => 'success',
            'product' => $product
        ]);
    }


    public function edit($id)
    {
        $product = $this->productService->find($id);
        return view('products.edit', compact('product'));
    }


    public function update(UpdateProductRequest $request, $id)
    {
        $product = $this->productService->update($id, $request->validated());
        return response()->json([
            'status' => 'success',
            'product' => $product
        ]);
    }


    public function destroy($id)
    {
        $this->productService->delete($id);
        return redirect()->route('products.index')->with('status', 'Product deleted successfully!');
    }
}
