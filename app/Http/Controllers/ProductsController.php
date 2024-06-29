<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;
use Exception;
use Barryvdh\DomPDF\Facade as PDF;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::orderBy('name', 'asc')->get();

        return view('products.products', [
            'products' => $products
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('products.products-add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:products',
            'price' => 'required',
            'category' => 'required',
            'image' => 'required',
            'description' => 'max:1000',
        ]);

        $products = Products::create($request->all());

        Alert::success('Success', 'Products has been saved !');
        return redirect('/products');
    }

    /**
     * Display the specified resource.
     */
    public function show(Products $products)
    {
        //
    }

    public function printProducts()
    {
        $products = Products::all();
        $data = ['productprint' => $products];

        $pdf = PDF::loadView('products.products-print', $data);

        return $pdf->stream('view-products.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $products = Products::findOrFail($id);

        return view('products.products-edit', [
            'products' => $products,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:255|unique:products,name,' . $id . ',id',
            'price' => 'required',
            'category' => 'required',
            'image' => 'required',
            'description' => 'max:1000',
        ]);

        $products = Products::findOrFail($id);
        $products->update($validated);

        Alert::info('Success', 'Products has been updated !');
        return redirect('/products');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $deletedproducts = Products::findOrFail($id);

            $deletedproducts->delete();

            Alert::error('Success', 'Products has been deleted !');
            return redirect('/products');
        } catch (Exception $ex) {
            Alert::warning('Error', 'Cant deleted, Products already used !');
            return redirect('/products');
        }
    }
}