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
}
