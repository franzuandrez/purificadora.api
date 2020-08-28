<?php

namespace App\Http\Controllers;

use App\Repository\ProductRepository;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {

        $products = $this->productRepository->all();

        return response([
            'success' => true,
            'data' => $products
        ]);
    }

    public function store(Request $request)
    {

        $product = $this->productRepository->storeFromRequest($request);
        return response([
            'success' => true,
            'data' => $product,
            'message' => 'Guardado correctamente'
        ]);
    }

    public function update(Request $request, $id)
    {

        $product = $this->productRepository->updateFromRequest($request, $id);
        return response([
            'success' => true,
            'data' => $product,
            'message' => 'Actualizado correctamente'
        ]);
    }

    public function show($id)
    {
        $product = $this->productRepository->findById($id);
        return response([
            'success' => true,
            'data' => $product,
        ]);

    }
}
