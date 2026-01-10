<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('inventory')
            ->orderBy('denominazione_commerciale')
            ->get();

        $title = 'Products';

        return view('products.index', compact('products', 'title'));
    }
}
