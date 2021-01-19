<?php

namespace App\Http\Controllers;

use App\Models\Seeds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeedingController extends Controller
{
    /**
     * get all seeds for current user
     */
    public function seeds(int $user_id)
    {
        $seeds = Seeds::where('user_id', $user_id)->orderBy('name', 'ASC')->get();

        return response()->json([
            "success" => true,
            "data" => $seeds->all()
        ]);
    }

    /**
     * Create new seed
     */
    public function newSeed(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['name']) || $data['name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Please enter Seed Name."
            ]);
        }
        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }
        $seeds = Seeds::where('user_id', $data['user_id'])->where('name', $data['name'])->get();
        if ($seeds->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Seed Name already exists."
            ]);
        }

        // save seed
        (new Seeds($data))->save();

        // get last seed
        $seed = (Seeds::where('user_id', $data['user_id'])
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get())->first();

        return response()->json([
            "success" => true,
            "data" => $seed,
            "message" => "Seed " . $data['name'] . " was added!"
        ]);
    }

    /**
     * Update seed
     */
    public function updateSeed(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['name']) || $data['name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Please enter Seed Name."
            ]);
        }
        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        // update seed name
        Seeds::where('id', $data['id'])
            ->update([
                'name' => $data['name']
            ]);

        return response()->json([
            "success" => true,
            "message" => "Seed name to " . $data['name'] . " updated!"
        ]);
    }

    /**
     * get all seed companies by user id
     */
    public function seedCompanies(int $user_id)
    {
        return response()->json([
            "success" => false,
            "message" => "Not implemented - seedCompanies"
        ]);
    }
    /**
     * get all seed companies by user id and seed id
     */
    public function seedCompaniesBySeed(int $user_id, int $seed_id)
    {
        return response()->json([
            "success" => false,
            "message" => "Not implemented - seedCompaniesBySeed"
        ]);
    }
}
