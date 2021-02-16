<?php

namespace App\Http\Controllers;

use App\Models\Packs;
use App\Models\Product;
use App\Models\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransportController extends Controller
{
    /** get transport lits with pagination and total shipment by status */
    public function getAllTransports(Request $request)
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

        $shipTable = app(Transport::class)->getTable();
        $packsTable = app(Packs::class)->getTable();
        $shipment = DB::table($shipTable)
            ->select($shipTable . '.*', DB::raw('COUNT(`' . $packsTable . '`.`transport_id`) as `pack_num`'))
            ->join($packsTable, $packsTable . '.transport_id', '=', $shipTable . '.id');
        if (isset($data['status'])) {
            $shipment = $shipment->where($shipTable . '.status', $data['status']);
        }

        $shipment = $shipment->orderBy($shipTable . '.invoice')
            ->groupBy($packsTable . '.transport_id')
            ->paginate($ipp, ['*'], 'page', $page);


        $data = [
            'total' => $shipment->total(),
            'lastPage' => $shipment->lastPage(),
            'items' => $shipment->items(),
            'currentPage' => $shipment->currentPage(),
            'ship_created' => Transport::where('status', 'created')->select(DB::raw('COUNT(id) as total'))->get()->first()->toArray()['total'],
            'ship_started' => Transport::where('status', 'in_transit')->select(DB::raw('COUNT(id) as total'))->get()->first()->toArray()['total'],
            'ship_completed' => Transport::where('status', 'completed')->select(DB::raw('COUNT(id) as total'))->get()->first()->toArray()['total']
        ];

        return response()->json([
            "success" => true,
            "data" => $data
        ]);
    }

    /** create a new transport and adds packs to it */
    public function addTransport(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $err = [];
        if (!isset($data['invoice']) || $data['invoice'] == "") {
            $err[] = "Please enter Invoice.";
        }
        if (!isset($data['ship_from']) || $data['ship_from'] == "") {
            $err[] = "Please enter Ship From.";
        }
        if (!isset($data['ship_to']) || $data['ship_to'] == "") {
            $err[] = "Please enter Ship To.";
        }
        if (!isset($data['ship_company']) || $data['ship_company'] == "") {
            $err[] = "Please enter Ship Company.";
        }
        if (!isset($data['market']) || $data['market'] == "") {
            $err[] = "Please enter Market.";
        }
        if (!isset($data['packs']) || count($data['packs']) == 0) {
            $err[] = "Packs are missing, please select at least one package.";
        }

        if (count($err) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $err)
            ]);
        }

        $packIds = $data['packs'];
        unset($data['packs']);

        $save = new Transport($data);
        $save->save();

        Packs::whereIn('id', $packIds)
            ->update(['transport_id' => $save->id]);

        // get current transport and pack count
        $shipTable = app(Transport::class)->getTable();
        $packsTable = app(Packs::class)->getTable();
        $shipment = DB::table($shipTable)
            ->select($shipTable . '.*', DB::raw('COUNT(`' . $packsTable . '`.`transport_id`) as `pack_num`'))
            ->join($packsTable, $packsTable . '.transport_id', '=', $shipTable . '.id')
            ->where($shipTable . '.id', $save->id)
            ->orderBy($shipTable . '.invoice')
            ->groupBy($packsTable . '.transport_id')
            ->get()
            ->first();

        return response()->json([
            "success" => true,
            "message" => "The transport was created successfully.",
            "data" => $shipment
        ]);
    }

    /** update transport info */
    public function updateTransport(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        if (!isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $err = [];
        if (!isset($data['invoice']) || $data['invoice'] == "") {
            $err[] = "Please enter Invoice.";
        }
        if (!isset($data['ship_from']) || $data['ship_from'] == "") {
            $err[] = "Please enter Ship From.";
        }
        if (!isset($data['ship_to']) || $data['ship_to'] == "") {
            $err[] = "Please enter Ship To.";
        }
        if (!isset($data['ship_company']) || $data['ship_company'] == "") {
            $err[] = "Please enter Ship Company.";
        }
        if (!isset($data['market']) || $data['market'] == "") {
            $err[] = "Please enter Market.";
        }

        Transport::where('id', $data['id'])
            ->update($data);

        // get current transport and pack count
        $shipTable = app(Transport::class)->getTable();
        $packsTable = app(Packs::class)->getTable();
        $shipment = DB::table($shipTable)
            ->select($shipTable . '.*', DB::raw('COUNT(`' . $packsTable . '`.`transport_id`) as `pack_num`'))
            ->join($packsTable, $packsTable . '.transport_id', '=', $shipTable . '.id')
            ->where($shipTable . '.id', $data['id'])
            ->orderBy($shipTable . '.invoice')
            ->groupBy($packsTable . '.transport_id')
            ->get()
            ->first();

        return response()->json([
            "success" => true,
            "message" => "The transport was updated successfully.",
            "data" => $shipment
        ]);
    }

    /** delet transport and update packs to no trasport */
    public function deleteTransport(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        // make packs available for other package
        Packs::where('transport_id', $data['id'])
            ->update(['transport_id' => 0]);

        Transport::where('id', $data['id'])->delete();

        return response()->json([
            "success" => true,
            "message" => "Package was deleted successfully."
        ]);
    }
}
