<?php

namespace App\Http\Controllers;

use App\Models\Field;
use App\Models\Packs;
use App\Models\Product;
use App\Models\Transport;

class CustomerController extends Controller
{
    /** get all info the the scanned QR code */
    public function getData(string $qr)
    {
        $action = explode("-", $qr);
        if (count($action) != 2 || !is_numeric($action[1])) {
            return response()->json([
                "success" => false,
                "message" => "The scaned QR code is not valid."
            ]);
        }

        $data = [];
        switch ($action[0]) {
            case 'product':
                $prod = Product::find($action[1]);
                if (!$prod) {
                    return response()->json([
                        "success" => false,
                        "message" => "Product was not found."
                    ]);
                }

                $field = Field::find($prod['field_id']);

                $pack = Packs::find($prod['pack_id']);

                $transport = Transport::find($pack['transport_id']);

                $data = [
                    'product' => [
                        'product_name' => $prod['product_name'],
                        'product_weight' => $prod['product_weight']
                    ],
                    'field' => [
                        'field_name' => $field['field_name'],
                        'seed_name' => $field['seed_name'],
                        'seed_company_name' => $field['seed_company_name'],
                        'fertilizer_name' => $field['fertilizer_name'],
                        'fertilizer_company_name' => $field['fertilizer_company_name'],
                        'harvesting_company_name' => $field['harvesting_company_name']
                    ],
                    'pack' => $pack ? [
                        'pack_name' => $pack['pack_name']
                    ] : null,
                    'transport' => $transport ? [
                        'invoice' => $transport['invoice'],
                        'ship_from' => $transport['ship_from'],
                        'ship_to' => $transport['ship_to'],
                        'ship_company' => $transport['ship_company'],
                        'market' => $transport['market']
                    ] : null
                ];
                break;
            case 'package':
                $pack = Packs::find($action[1]);
                if (!$pack) {
                    return response()->json([
                        "success" => false,
                        "message" => "Package was not found."
                    ]);
                }

                $transport = Transport::find($pack['transport_id']);

                $prods = Product::where('pack_id', $action[1]);
                $data = [
                    'products' => $prods->get()->all(),
                    'pack' => [
                        'pack_name' => $pack['pack_name']
                    ],
                    'transport' => $transport ? [
                        'invoice' => $transport['invoice'],
                        'ship_from' => $transport['ship_from'],
                        'ship_to' => $transport['ship_to'],
                        'ship_company' => $transport['ship_company'],
                        'market' => $transport['market']
                    ] : null
                ];
                break;
            case 'transport':
                $transport = Transport::find($action[1]);
                if (!$transport) {
                    return response()->json([
                        "success" => false,
                        "message" => "Transport was not found."
                    ]);
                }

                $packsWithProducts = Packs::where('transport_id', $action[1])->get()
                    ->map(function ($pack) {
                        $prods = Product::where('pack_id', $pack['id'])->get();
                        return [
                            'pack' => [
                                'pack_name' => $pack['pack_name']
                            ],
                            'products' => $prods->all(),
                        ];
                    })->all();
                $data = [
                    'packs' => $packsWithProducts,
                    'transport' => [
                        'invoice' => $transport['invoice'],
                        'ship_from' => $transport['ship_from'],
                        'ship_to' => $transport['ship_to'],
                        'ship_company' => $transport['ship_company'],
                        'market' => $transport['market']
                    ]
                ];
                break;
            default:
                return response()->json([
                    "success" => false,
                    "message" => "Something is missing, please try again later."
                ]);
        }


        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }
}
