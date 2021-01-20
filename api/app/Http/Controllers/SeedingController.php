<?php

namespace App\Http\Controllers;

use App\Models\SeedingCompany;
use App\Models\Seeds;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $save = new Seeds($data);
        $save->save(); //$save->id; get last inserted id row

        // get last seed
        $seed = Seeds::find($save->id);

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
        $companyTable = app(SeedingCompany::class)->getTable();
        $seedTable = app(Seeds::class)->getTable();

        $seedCompanies = DB::table($companyTable)
            ->where($companyTable . '.user_id', $user_id)
            ->join($seedTable, $seedTable . '.id', '=', $companyTable . '.seed_id')
            ->select($companyTable . '.*', DB::raw($seedTable . '.name as seed_name'))
            ->orderBy($companyTable . '.company_name', 'ASC')
            ->get();

        return response()->json([
            "success" => true,
            "data" => $seedCompanies->all()
        ]);
    }
    /**
     * get all seed companies by user id and seed id
     */
    public function seedCompaniesBySeed(int $user_id, int $seed_id)
    {
        $seedCompanies = SeedingCompany::where('user_id', $user_id)
            ->where('seed_id', $seed_id)
            ->orderBy('company_name', 'ASC')
            ->get();

        return response()->json([
            "success" => true,
            "data" => $seedCompanies->all()
        ]);
    }

    /**
     * add a new seeding company
     */
    public function newSeedCompanies(Request $request)
    {
        $data = $request->toArray();

        $err = [];
        if (!isset($data['company_name']) || $data['company_name'] == "") {
            $err[] = "Please enter Company Name.";
        }
        if (!isset($data['seed_id']) || $data['seed_id'] == "") {
            $err[] = "Please select a Seed.";
        }

        if (count($err) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $err)
            ]);
        }

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        $seedCompanies = SeedingCompany::where('user_id', $data['user_id'])
            ->where('company_name', $data['company_name'])
            ->where('seed_id', $data['seed_id'])
            ->get();
        if ($seedCompanies->count() > 0) {
            $seed = Seeds::find($data['seed_id']);
            return response()->json([
                "success" => false,
                "message" => "The company with seed $seed->name already exists."
            ]);
        }

        $save = new SeedingCompany($data);
        $save->save();

        // return inserted row
        $companyTable = app(SeedingCompany::class)->getTable();
        $seedTable = app(Seeds::class)->getTable();
        $seedCompany = DB::table($companyTable)
            ->where($companyTable . '.id', $save->id)
            ->join($seedTable, $seedTable . '.id', '=', $companyTable . '.seed_id')
            ->select($companyTable . '.*', DB::raw($seedTable . '.name as seed_name'))
            ->get();
        return response()->json([
            "success" => true,
            "message" => "The company was added succesfully.",
            "data" => $seedCompany->first()
        ]);
    }

    /**
     * update company name and seed that it provides
     */
    public function updateSeedCompanies(Request $request)
    {
        $data = $request->toArray();

        $err = [];
        if (!isset($data['company_name']) || $data['company_name'] == "") {
            $err[] = "Please enter Company Name.";
        }
        if (!isset($data['seed_id']) || $data['seed_id'] == "") {
            $err[] = "Please select a Seed.";
        }

        if (count($err) > 0) {
            return response()->json([
                "success" => false,
                "message" => implode(" ", $err)
            ]);
        }

        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        // check if another record other than this(the one that is getting updated) has the same value
        $seedCompanies = SeedingCompany::where('user_id', $data['user_id'])
            ->where('company_name', $data['company_name'])
            ->where('seed_id', $data['seed_id'])
            ->where('id', '!=', $data['id'])
            ->get();
        if ($seedCompanies->count() > 0) {
            $seed = Seeds::find($data['seed_id']);
            return response()->json([
                "success" => false,
                "message" => "The company with seed $seed->name already exists."
            ]);
        }

        // update row
        SeedingCompany::where('id', $data['id'])
            ->update([
                'seed_id' => $data['seed_id'],
                'company_name' => $data['company_name']
            ]);

        // return the updated row
        $companyTable = app(SeedingCompany::class)->getTable();
        $seedTable = app(Seeds::class)->getTable();
        $seedCompany = DB::table($companyTable)
            ->where($companyTable . '.id', $data['id'])
            ->join($seedTable, $seedTable . '.id', '=', $companyTable . '.seed_id')
            ->select($companyTable . '.*', DB::raw($seedTable . '.name as seed_name'))
            ->get();

        return response()->json([
            "success" => true,
            "message" => "The company was added succesfully.",
            "data" => $seedCompany->first()
        ]);
    }
    
    /**
     * delete company
     */
    public function deleteSeedCompanies(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }
        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        (SeedingCompany::find($data['id']))->delete();

        return response()->json([
            "success" => true,
            "message" => "The company was deleted succesfully.",
        ]);
    }
}
