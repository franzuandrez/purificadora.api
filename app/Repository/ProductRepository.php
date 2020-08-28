<?php


namespace App\Repository;


use App\Product;

class ProductRepository
{


    public function all()
    {
        return Product::get();
    }
}
