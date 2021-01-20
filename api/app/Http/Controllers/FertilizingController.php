<?php

namespace App\Http\Controllers;

use App\Models\FertilizerCompanies;
use App\Models\Fertilizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FertilizingController extends Controller
{
    /**
     * get all fertilizers for current user
     */
    public function fertilizers(int $user_id)
    {
        $fertilizers = Fertilizer::where('user_id', $user_id)->orderBy('name', 'ASC')->get();

        return response()->json([
            "success" => true,
            "data" => $fertilizers->all()
        ]);
    }

    /**
     * Create new fertilizer
     */
    public function newFertilizer(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['name']) || $data['name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Please enter Fertilizer Name."
            ]);
        }
        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }
        $fertilizers = Fertilizer::where('user_id', $data['user_id'])->where('name', $data['name'])->get();
        if ($fertilizers->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Fertilizer Name already exists."
            ]);
        }

        // save fertilizer
        $save = new Fertilizer($data);
        $save->save(); //$save->id; get last inserted id row

        // get last fertilizer
        $fertilizer = Fertilizer::find($save->id);

        return response()->json([
            "success" => true,
            "data" => $fertilizer,
            "message" => "Fertilizer " . $data['name'] . " was added!"
        ]);
    }

    /**
     * Update fertilizer
     */
    public function updateFertilizer(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['name']) || $data['name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Please enter Fertilizer Name."
            ]);
        }
        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        // update seed name
        Fertilizer::where('id', $data['id'])
            ->update([
                'name' => $data['name']
            ]);

        return response()->json([
            "success" => true,
            "message" => "Fertilizer name was updated succesfully!"
        ]);
    }

    /**
     * get all fertilizer companies by user id
     */
    public function companies(int $user_id)
    {
        $companyTable = app(FertilizerCompanies::class)->getTable();
        $fertilizerTable = app(Fertilizer::class)->getTable();

        $companies = DB::table($companyTable)
            ->where($companyTable . '.user_id', $user_id)
            ->join($fertilizerTable, $fertilizerTable . '.id', '=', $companyTable . '.fertilizer_id')
            ->select($companyTable . '.*', DB::raw($fertilizerTable . '.name as fertilizer_name'))
            ->orderBy($companyTable . '.company_name', 'ASC')
            ->get();

        return response()->json([
            "success" => true,
            "data" => $companies->all()
        ]);
    }
    /**
     * get all fertilizer companies by user id and fertilizer id
     */
    public function companiesByFertilizer(int $user_id, int $fertilizer_id)
    {
        $companies = FertilizerCompanies::where('user_id', $user_id)
            ->where('fertilizer_id', $fertilizer_id)
            ->orderBy('company_name', 'ASC')
            ->get();

        return response()->json([
            "success" => true,
            "data" => $companies->all()
        ]);
    }

    /**
     * add a new fertilizer company
     */
    public function newCompany(Request $request)
    {
        $data = $request->toArray();

        $err = [];
        if (!isset($data['company_name']) || $data['company_name'] == "") {
            $err[] = "Please enter Company Name.";
        }
        if (!isset($data['fertilizer_id']) || $data['fertilizer_id'] == "") {
            $err[] = "Please select a Fertilizer.";
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

        $checkCompanies = FertilizerCompanies::where('user_id', $data['user_id'])
            ->where('company_name', $data['company_name'])
            ->where('fertilizer_id', $data['fertilizer_id'])
            ->get();
        if ($checkCompanies->count() > 0) {
            $seed = Fertilizer::find($data['fertilizer_id']);
            return response()->json([
                "success" => false,
                "message" => "The company with seed $seed->name already exists."
            ]);
        }

        $save = new FertilizerCompanies($data);
        $save->save();

        // return inserted row
        $companyTable = app(FertilizerCompanies::class)->getTable();
        $fertilizerTable = app(Fertilizer::class)->getTable();
        $company = DB::table($companyTable)
            ->where($companyTable . '.id', $save->id)
            ->join($fertilizerTable, $fertilizerTable . '.id', '=', $companyTable . '.fertilizer_id')
            ->select($companyTable . '.*', DB::raw($fertilizerTable . '.name as fertilizer_name'))
            ->get();
        return response()->json([
            "success" => true,
            "message" => "The company was added succesfully.",
            "data" => $company->first()
        ]);
    }

    /**
     * update company name and fertilizer that it provides
     */
    public function updateCompany(Request $request)
    {
        $data = $request->toArray();

        $err = [];
        if (!isset($data['company_name']) || $data['company_name'] == "") {
            $err[] = "Please enter Company Name.";
        }
        if (!isset($data['fertilizer_id']) || $data['fertilizer_id'] == "") {
            $err[] = "Please select a Fertilizer.";
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
        $seedCompanies = FertilizerCompanies::where('user_id', $data['user_id'])
            ->where('company_name', $data['company_name'])
            ->where('fertilizer_id', $data['fertilizer_id'])
            ->where('id', '!=', $data['id'])
            ->get();
        if ($seedCompanies->count() > 0) {
            $seed = Fertilizer::find($data['fertilizer_id']);
            return response()->json([
                "success" => false,
                "message" => "The company with seed $seed->name already exists."
            ]);
        }

        // update row
        FertilizerCompanies::where('id', $data['id'])
            ->update([
                'fertilizer_id' => $data['fertilizer_id'],
                'company_name' => $data['company_name']
            ]);

        // return the updated row
        $companyTable = app(FertilizerCompanies::class)->getTable();
        $seedTable = app(Fertilizer::class)->getTable();
        $company = DB::table($companyTable)
            ->where($companyTable . '.id', $data['id'])
            ->join($seedTable, $seedTable . '.id', '=', $companyTable . '.fertilizer_id')
            ->select($companyTable . '.*', DB::raw($seedTable . '.name as fertilizer_name'))
            ->get();

        return response()->json([
            "success" => true,
            "message" => "The company was added succesfully.",
            "data" => $company->first()
        ]);
    }
    
    /**
     * delete company
     */
    public function deleteCompany(Request $request)
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

        (FertilizerCompanies::find($data['id']))->delete();

        return response()->json([
            "success" => true,
            "message" => "The company was deleted succesfully.",
        ]);
    }
}
