<?php


namespace App\Repository;

use Illuminate\Http\Request;
use App\Product;

class ProductRepository
{


    public function all($request)
    {
        $search = $request->get('search');

        return Product::where(
            function ($query) use ($search) {
                return $query->where('description', 'LIKE', '%' . $search . '%');
            })
            ->get();
    }


    public function storeFromRequest(Request $request)
    {
        $product = new Product();
        $product = $this->save($product, $request);

        return $product;

    }

    public function updateFromRequest(Request $request, $id)
    {
        $product = $this->findById($id);
        $product = $this->save($product, $request);

        return $product;

    }


    public function findById($id)
    {
        return Product::findOrFail($id);
    }

    private function save(Product $product, Request $request)
    {
        $product->description = $request->description;
        $product->price = $request->price;
        $product->status = 1;
        $product->save();

        return $product;
    }
}
