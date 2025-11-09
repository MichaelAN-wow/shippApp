<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCalc;
use App\Models\Unit;

class ProductCalcController extends Controller {
    public function store( Request $request ) {
        $inputArray = $request->all();

        $headers = $inputArray[ 'headers' ];
        $data = $inputArray[ 'data' ];
        $formulas = $inputArray['formulas'];
        $unitId = $inputArray['unit_id'];

        // Convert headers and data to JSON
        $headersJson = json_encode( $headers );
        $dataJson = json_encode( $data );
        $formulasJson = json_encode($formulas);

        // Store the headers and data in the database
        $productCalc = ProductCalc::create( [
            'headers' => $headersJson,
            'data' => $dataJson,
            'formulas' => $formulasJson,
            'unit_id' => $unitId,
            'company_id' => session('company_id')
        ] );

        return response()->json( $productCalc );
    }

    public function index() {
        $productCalcs = ProductCalc::where('company_id', session('company_id'))->with('unit')->get();
        $units = Unit::all()->groupBy('type');
        return view( 'Admin.product_calculator', compact( 'productCalcs', 'units' ));
    }

    public function destroy($id)
    {
        $product = ProductCalc::findOrFail($id);

        if ($product->delete()) {
            return response()->json(['success' => 'Successfully deleted!']);
        } else {
            return response()->json(['error' => 'Failed to delete!'], 500);
        }
    }

    public function getProductCalc($id)
    {
        // Fetch the product calculation record by ID
        $productCalc = ProductCalc::findOrFail($id);

        // Prepare the response data
        return response()->json([
            'headers' => json_decode($productCalc->headers),
            'data' => json_decode($productCalc->data),
            'formulas' => json_decode($productCalc->formulas),
            'unit_id' => $productCalc->unit_id
        ]);
    }

    public function update(Request $request, $id) {
        $productCalc = ProductCalc::findOrFail($id);

        $request->validate([
            'headers' => 'required|array',
            'data' => 'required|array',
            'formulas' => 'required|array',
            'unit_id' => 'required|integer|exists:units,id',
        ]);

        $productCalc->headers = json_encode($request->input('headers'));
        $productCalc->data = json_encode($request->input('data'));
        $productCalc->formulas = json_encode($request->input('formulas'));
        $productCalc->unit_id = $request->input('unit_id');

        // Save the updated product calculation back to the database
        $productCalc->save();

        // Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Product calculation updated successfully!'
        ]);
    }
}