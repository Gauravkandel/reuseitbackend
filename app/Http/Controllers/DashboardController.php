<?php

namespace App\Http\Controllers;

use App\Models\product;
use App\Http\Requests\AntiquesRequest;
use App\Http\Requests\BicycleRequest;
use App\Http\Requests\BooksRequest;
use App\Http\Requests\CarsRequest;
use App\Http\Requests\ClothingRequest;
use App\Http\Requests\ElectronicsRequest;
use App\Http\Requests\FurnitureRequest;
use App\Http\Requests\HomeApplianceRequest;
use App\Http\Requests\MotorRequest;
use App\Http\Requests\MusicRequest;
use App\Http\Requests\ScooterRequest;
use App\Http\Requests\SportsRequest;
use App\Http\Requests\ToysRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\antique;
use App\Models\bicycle;
use App\Models\book;
use App\Models\car;
use App\Models\category;
use App\Models\clothing;
use App\Models\DynamicModel;
use App\Models\Electronic;
use App\Models\furniture;
use App\Models\HomeAppliance;
use App\Models\motorcycle;
use App\Models\music;
use App\Models\Product_image;
use App\Models\scooter;
use App\Models\sport;
use App\Models\toy;
use Illuminate\Http\Request;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $ProductServices;

    public function __construct(ProductService $ProductServices)
    {
        $this->middleware('auth:api');
        $this->ProductServices = $ProductServices;
    }
    public function myProducts(Request $request)
    {
        $user = auth()->user();
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);
        $products = product::where('user_id', $user->id)
            ->with('category', 'image')->skip(($page - 1) * $limit)
            ->take($limit)->get();
        return response()->json($products, 200);
    }
    public function deleteAds($id)
    {
        $products = product::find($id);
        $products->delete();
        return response()->json(['Message' => 'Deleted Successfully']);
    }
    public function Soldout(Request $request)
    {
        $product_id = $request->product_id;
        $product_sp = $request->selling_price;
        $productdata = product::find($product_id);
        $productdata->status = 1;
        $productdata->selling_price =  $product_sp;
        $productdata->save();
        return response()->json(['message' => 'successfull'], 200);
    }
    public function EditUserProducts($id)
    {
        try {
            $product = Product::with(['category', 'image'])->findOrFail($id);
            if ($product->user_id === auth()->id()) {

                $category = $product->category->category_name;

                $data = $this->ProductServices->getProductData($category, $id); //sending data to function getproductData

                return response()->json(['data' => $data], 200);
            } else {
                return response()->json(['error' => 'UnAuthorized to view data'], 401);
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (\Exception $e) {
            // Handling exceptions 
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    //update Products
    public function insertProductByCategory(Request $request)
    {
        return $this->insertProducts($request);
    }
    private function insertProducts($request)
    {
        $productData = $request->all();
        DB::beginTransaction();
        try {
            // Insert into products table
            $productData['category_id'] = $request->category_id; // Assuming $categoryType is the category ID
            $product = Product::create($productData);

            // Get category-specific information
            $category = category::where('id', $request->category_id)->first();

            if (!$category) {
                throw new \Exception('Invalid category type');
            }

            // foreach (json_decode($category->fields, true) as $field) {
            //     $validationRules[$field] = 'required|string|max:255'; // Adjust the validation rules as needed
            // }
            // $validatedData = $request->validate($validationRules);
            $onlyarray = [];
            $data = json_decode($category->fields, true);
            for ($i = 0; $i < count($data); $i++) {
                $onlyarray[$i] = $data[$i];
            }
            $dynamicModel = new DynamicModel();
            $dynamicModel->setTableBasedOnCondition($category->function_name, $onlyarray);
            // Insert into specific table (home_appliances or electronics or any other categoric fields)
            $specificData = $request->only($onlyarray);
            $specificData['product_id'] = $product->id;
            $dynamicModel->create($specificData);

            // Store the uploaded image paths
            if ($request->has('image_urls')) {
                foreach ($request->file('image_urls') as $index => $image) {
                    $imageName = time() . $index . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images'), $imageName);
                    $productImage = new product_image([
                        'product_id' => $product->id,
                        'image_url' => $imageName,
                    ]);
                    $productImage->save();
                }
            } else {
                return response()->json(['error' => 'Image is required'], 422);
            }
            DB::commit();
            return response()->json(['success' => 'successful', "product_id" => $product->id, "status" => 200], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to insert data. ' . $e], 500);
        }
    }

    private function insertProduct($request, $model, $dataKeys, $category)
    {
        $productData = $request->validated();
        DB::beginTransaction();
        try {
            // Insert into products table
            $productData['category_id'] = $category;
            $product = Product::create($productData);

            // Insert into specific table (home_appliances or electronics or any other categoric fields)
            $specificData = $request->only($dataKeys);
            $specificData['product_id'] = $product->id;
            $specificModel = $model::create($specificData);

            // Store the uploaded image paths
            if ($request->has('image_urls')) {
                foreach ($request->file('image_urls') as $index => $image) {
                    $imageName = time() . $index . '_' . $image->getClientOriginalName();
                    $image->move(public_path('images'), $imageName);
                    $productImage = new product_image([
                        'product_id' => $product->id,
                        'image_url' => $imageName,
                    ]);
                    $productImage->save();
                }
            } else {
                return response()->json(['error' => 'Image is required'], 422);
            }
            DB::commit();
            return response()->json(['success' => 'successful', "product_id" => $product->id, "status" => 200], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to insert data. ' . $e], 500);
        }
    }
}
