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
use App\Models\antique;
use App\Models\bicycle;
use App\Models\book;
use App\Models\car;
use App\Models\clothing;
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
    public function UpdateProducts(Request $request)
    {
        $productdata = product::with('category', 'image')->findOrFail($request->id);
        if ($productdata->user_id != auth()->id()) {
            return response()->json(['error' => 'Not Authorized to update data'], 401);
        }
        $category = $productdata->category_id;
        switch ($category) {
            case 1:
                $this->Electronics($request);
                break;
            case 2:
                $this->HomeAppliances($request);
                break;
            case 3:
                $this->Furnitures($request);
                break;
            case 4:
                $this->Clothing($request);
                break;
            case 5:
                $this->Sports($request);
                break;
            case 6:
                $this->Books($request);
                break;
            case 7:
                $this->Antiques($request);
                break;
            case 8:
                $this->Cars($request);
                break;
            case 9:
                $this->Motorcycle($request);
                break;
            case 10:
                $this->Scooter($request);
                break;
            case 11:
                $this->Bicycle($request);
                break;
            case 12:
                $this->Toys($request);
                break;
            case 13:
                $this->Music($request);
                break;
            default:
                return response()->json(['error' => "Send valid Information"]);
        }
    }
    public function Electronics(ElectronicsRequest $request)
    {
        return $this->insertProduct($request, electronic::class, ['type_of_electronic', 'brand', 'model', 'condition', 'warranty_information'], 1);
    }
    public function HomeAppliances(HomeApplianceRequest $request)
    {
        return $this->insertProduct($request, HomeAppliance::class, ['type_of_appliance', 'brand', 'model', 'capacity', 'features', 'condition', 'warranty_information'], 2);
    }
    public function Furnitures(FurnitureRequest $request)
    {
        return $this->insertProduct($request, furniture::class, ['type_of_furniture', 'material', 'dimensions', 'color', 'style', 'condition', 'assembly_required'], 3);
    }
    public function Clothing(ClothingRequest $request)
    {
        return $this->insertProduct($request, clothing::class, ['type_of_clothing_accessory', 'size', 'color', 'brand', 'material', 'condition', 'care_instructions'], 4);
    }
    public function Sports(SportsRequest $request)
    {
        return $this->insertProduct($request, sport::class, ['type_of_equipment', 'brand', 'condition', 'size_weight', 'features', 'suitable_sport_activity', 'warranty_information', 'usage_instructions'], 5);
    }
    public function Books(BooksRequest $request)
    {
        return $this->insertProduct($request, book::class, ['title', 'author_artist', 'genre', 'format', 'condition', 'edition', 'isbn_upc', 'warranty_information', 'description'], 6);
    }
    public function Antiques(AntiquesRequest $request)
    {
        return $this->insertProduct($request, antique::class, ['type_of_item', 'era_period', 'material', 'condition', 'provenance_location', 'rarity', 'historical_significance', 'certification'], 7);
    }
    public function Cars(CarsRequest $request)
    {
        return $this->insertProduct($request, car::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'fuel_type', 'owner', 'transmission_type'], 8);
    }
    public function Motorcycle(MotorRequest $request)
    {
        return $this->insertProduct($request, motorcycle::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'], 9);
    }
    public function Scooter(ScooterRequest $request)
    {
        return $this->insertProduct($request, scooter::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'], 10);
    }
    public function Bicycle(BicycleRequest $request)
    {
        return $this->insertProduct($request, bicycle::class, ['brand'], 11);
    }
    public function Toys(ToysRequest $request)
    {
        return $this->insertProduct(
            $request,
            toy::class,
            ['type_of_toy_game', 'age_group', 'brand', 'condition', 'description', 'safety_information', 'assembly_required', 'recommended_use'],
            12
        );
    }
    public function Music(MusicRequest $request)
    {
        return $this->insertProduct(
            $request,
            music::class,
            ['type_of_instrument', 'brand', 'condition', 'material', 'accessories_included', 'sound_characteristics'],
            13
        );
    }
    private function insertProduct($request, $model, $dataKeys, $category)
    {
        $productData = $request->validated();
        DB::beginTransaction();
        try {
            // Insert into products table
            $products  = product::findOrFail($request->id);
            $productData['category_id'] = $category;
            $products->update($productData);

            // Insert into specific table (home_appliances or electronics or any other categoric fields)
            $specificData = $request->only($dataKeys);
            $specificModel = $model::where('product_id', $request->id)->firstOrFail();
            $specificModel->update($specificData);
            // Store the uploaded image paths
            // $existingImages = Product_image::where('product_id', $request->id)->get();
            // foreach ($existingImages as $existingImage) {
            //     $imagePath = public_path('images') . '/' . $existingImage->image_url;
            //     if (file_exists($imagePath)) {
            //         unlink($imagePath);
            //     }
            // }
            // if ($request->has('image_urls')) {
            //     foreach ($request->file('image_urls') as $index => $image) {
            //         $imageName = time() . $index . '_' . $image->getClientOriginalName();
            //         $image->move(public_path('images'), $imageName);
            //         $productImage = new product_image([
            //             'product_id' => $request->id,
            //             'image_url' => $imageName,
            //         ]);
            //         $productImage->save();
            //     }
            // } else {
            //     return response()->json(['error' => 'Image is required'], 422);
            // }
            DB::commit();
            return response()->json(['success' => 'Updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to insert data.'], 500);
        }
    }
}
