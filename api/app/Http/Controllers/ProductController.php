<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Add Product
     */
    public function addProduct(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $err = [];

        if (!isset($data['field_id']) || $data['field_id'] == "") {
            $err[] = "Please select a field.";
        }
        if (!isset($data['product_name']) || $data['product_name'] == "") {
            $err[] = "Please enter Product name.";
        }
        if (!isset($data['product_weight']) || $data['product_weight'] == "") {
            $err[] = "Please enter Product weight.";
        }

        if (count($err) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $err)
            ]);
        }

        $setFieldAsCompleted = $data['setFieldAsCompleted'];
        if ($setFieldAsCompleted) {
            Field::where('id', $data['field_id'])->update(['products_sold' => 1]);
        }

        unset($data['setFieldAsCompleted']);
        $save = new Product($data);
        $save->save();
        $product = Product::find($save->id);

        return response()->json([
            "success" => true,
            "message" => "Product added successfully.",
            "data" => $product->toArray()
        ]);
    }

    /** Update product */
    public function updateProduct(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $err = [];

        if (!isset($data['product_name']) || $data['product_name'] == "") {
            $err[] = "Please enter Product name.";
        }
        if (!isset($data['product_weight']) || $data['product_weight'] == "") {
            $err[] = "Please enter Product weight.";
        }

        if (count($err) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $err)
            ]);
        }

        Product::where('id', $data['id'])->update($data);

        $product = Product::find($data['id']);

        return response()->json([
            "success" => true,
            "message" => "Product updated successfully.",
            "data" => $product->toArray()
        ]);
    }

    /** Delete product */
    public function deleteProduct(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        Product::find($data['id'])->delete();

        return response()->json([
            "success" => true,
            "message" => "Product deleted successfully.",
        ]);
    }

    /** Get all products, can also get all products by pack_id*/
    public function getProducts(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $ipp = isset($data['ipp']) ? $data['ipp'] : 20;
        $page = $data['page'];

        $products = Product::where('pack_id',  isset($data['pack_id']) && $data['pack_id'] != "" ? $data['pack_id'] : 0)
            ->paginate($ipp, ['*'], 'page', $page);

        $data = [
            'total' => $products->total(),
            'lastPage' => $products->lastPage(),
            'currentPage' => $products->currentPage(),
            'items' => $products->items()
        ];

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    /** Get all products */
    public function getFields()
    {
        $fields = Field::where('products_sold', 0)
            ->where('harvesting_status', 'completed')->get();

        return response()->json([
            "success" => true,
            "data" => $fields->all()
        ]);
    }
}
