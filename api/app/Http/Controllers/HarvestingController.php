<?php

namespace App\Http\Controllers;

use App\Models\HarvestingCompanies;
use Illuminate\Http\Request;

class HarvestingController extends Controller
{
    
    /**
     * get all companies for current user
     */
    public function companies(int $user_id)
    {
        $fertilizers = HarvestingCompanies::where('user_id', $user_id)->orderBy('company_name', 'ASC')->get();

        return response()->json([
            "success" => true,
            "data" => $fertilizers->all()
        ]);
    }

    /**
     * Create new company
     */
    public function newCompany(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['company_name']) || $data['company_name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Please enter Company Name."
            ]);
        }
        if (!isset($data['user_id']) || $data['user_id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }
        $checkCompany = HarvestingCompanies::where('user_id', $data['user_id'])->where('company_name', $data['company_name'])->get();
        if ($checkCompany->count() > 0) {
            return response()->json([
                "success" => false,
                "message" => "Company Name already exists."
            ]);
        }

        // save company
        $save = new HarvestingCompanies($data);
        $save->save(); //$save->id; get last inserted id row

        // get last company
        $getCompany = HarvestingCompanies::find($save->id);

        return response()->json([
            "success" => true,
            "data" => $getCompany,
            "message" => "Company " . $data['company_name'] . " was added!"
        ]);
    }

    /**
     * Update company
     */
    public function updateCompany(Request $request)
    {
        $data = $request->toArray();

        if (!isset($data['company_name']) || $data['company_name'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Please enter Company Name."
            ]);
        }
        if (!isset($data['user_id']) || $data['user_id'] == "" || !isset($data['id']) || $data['id'] == "") {
            return response()->json([
                "success" => false,
                "message" => "Something is missing, please try again later."
            ]);
        }

        // update company name
        HarvestingCompanies::where('id', $data['id'])
            ->update([
                'company_name' => $data['company_name']
            ]);

        return response()->json([
            "success" => true,
            "message" => "Company name was updated succesfully!"
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

        (HarvestingCompanies::find($data['id']))->delete();

        return response()->json([
            "success" => true,
            "message" => "The company was deleted succesfully.",
        ]);
    }
}
