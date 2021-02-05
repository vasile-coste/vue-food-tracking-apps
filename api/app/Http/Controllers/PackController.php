<?php

namespace App\Http\Controllers;

use App\Models\Packs;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackController extends Controller
{
    /** get list of Packs and product number by current user that are not in transport */
    public function getNewPackages(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $packTable = app(Packs::class)->getTable();
        $prodTable = app(Product::class)->getTable();
        $packs = DB::table($packTable)
            ->select($packTable . '.*', DB::raw('COUNT(`' . $prodTable . '`.`pack_id`) as `prod_num`'))
            ->join($prodTable, $prodTable . '.pack_id', '=', $packTable . '.id')
            ->where($packTable . '.user_id', $data['user_id'])
            ->where($packTable . '.transport_id', 0)
            ->orderBy($packTable . '.pack_name')
            ->groupBy($prodTable . '.pack_id')
            ->get();

        return response()->json([
            "success" => true,
            "data" => $packs->all()
        ]);
    }

    public function getAllPackages(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if (!isset($data['page']) || $data['page'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Please select the page number."
            ]);
        }

        $ipp = isset($data['ipp']) ? $data['ipp'] : 20;
        $page = $data['page'];

        $packTable = app(Packs::class)->getTable();
        $prodTable = app(Product::class)->getTable();
        $packs = DB::table($packTable)
            ->select($packTable . '.*', DB::raw('COUNT(`' . $prodTable . '`.`pack_id`) as `prod_num`'))
            ->join($prodTable, $prodTable . '.pack_id', '=', $packTable . '.id')
            ->orderBy($packTable . '.id', 'DESC')
            ->groupBy($prodTable . '.pack_id')
            ->paginate($ipp, ['*'], 'page', $page);


        $data = [
            'total' => $packs->total(),
            'lastPage' => $packs->lastPage(),
            'items' => $packs->items(),
            'currentPage' => $packs->currentPage(),
            'packs_new' => Packs::where('transport_id', 0)->select(DB::raw('COUNT(id) as packs'))->get()->first()->toArray()['packs'],
            'packs_done' => Packs::where('transport_id', '>', 0)->select(DB::raw('COUNT(id) as packs'))->get()->first()->toArray()['packs']
        ];

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    /** get a list of products for current package id */
    public function getProducts(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['pack_id']) || $data['pack_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $products = Product::where('pack_id', $data['pack_id'])->get();

        return response()->json([
            "success" => true,
            "data" => $products->all()
        ]);
    }

    /** create a new package name and adds products to it */
    public function addPackage(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $err = [];
        if (!isset($data['pack_name']) || $data['pack_name'] == "") {
            $err[] = "Please enter Package Name.";
        }
        if (!isset($data['products']) || count($data['products']) == 0) {
            $err[] = "Products are missing, please select at least one product.";
        }

        if (count($err) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $err)
            ]);
        }

        $save = new Packs($data);
        $save->save();

        Product::whereIn('id', $data['products'])
            ->update(['pack_id' => $save->id]);

        // get current package and product count
        $packTable = app(Packs::class)->getTable();
        $prodTable = app(Product::class)->getTable();
        $pack = DB::table($packTable)
            ->where($packTable . '.id', $save->id)
            ->join($prodTable, $prodTable . '.pack_id', '=', $packTable . '.id')
            ->select($packTable . '.*', DB::raw('COUNT(' . $prodTable . '.pack_id) as prod_num'))
            ->groupBy($prodTable . '.pack_id')
            ->get()
            ->first();

        return response()->json([
            "success" => true,
            "message" => "The package was created successfully.",
            "data" => $pack
        ]);
    }

    /** update package name */
    public function updatePackage(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if (!isset($data['pack_name']) || $data['pack_name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Please enter Package Name."
            ]);
        }

        Packs::where('id', $data['id'])
            ->update(['pack_name' => $data['pack_name']]);

        return response()->json([
            "success" => true,
            "message" => "Package name was updated successfully."
        ]);
    }

    /** 
     * delete a package
     * the products in that package will become available for another package
     * */
    public function deletePackage(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        // mahe products available for other package
        Product::where('pack_id', $data['id'])
            ->update(['pack_id' => 0]);

        Packs::where('id', $data['id'])->delete();

        return response()->json([
            "success" => true,
            "message" => "Package was deleted successfully."
        ]);
    }

    /** add products to an existing package */
    public function addProducts(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $err = [];
        if (!isset($data['id']) || $data['id'] == "") {
            $err[] = "Please select Package Name.";
        }
        if (!isset($data['products']) || count($data['products']) == 0) {
            $err[] = "Products are missing, please select at least one product.";
        }

        if (count($err) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $err)
            ]);
        }

        Product::whereIn('id', $data['products'])
            ->update(['pack_id' => $data['id']]);

        return response()->json([
            "success" => true,
            "message" => "Products were added successfully."
        ]);
    }

    /** remove product from package */
    public function removeProduct(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        Product::where('id', $data['id'])
            ->update(['pack_id' => 0]);

        return response()->json([
            "success" => true,
            "message" => "Product removed successfully from package."
        ]);
    }
}
