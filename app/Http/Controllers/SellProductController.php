<?php

namespace App\Http\Controllers;

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
use App\Models\category;
use App\Models\clothing;
use App\Models\DynamicModel;
use App\Models\Electronic;
use App\Models\furniture;
use App\Models\HomeAppliance;
use App\Models\motorcycle;
use App\Models\music;
use App\Models\Product;
use App\Models\Product_image;
use App\Models\scooter;
use App\Models\sport;
use App\Models\toy;
use App\Rules\ValidDimensions;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SellProductController extends Controller
{
    public function getCategory()
    {
        $category = category::all();
        return response()->json([$category]);
    }
    public function electronics(ElectronicsRequest $request)
    {
        return $this->insertProduct($request, electronic::class, ['type_of_electronic', 'brand', 'model', 'condition', 'warranty_information'], 1);
    }
    public function homeappliances(HomeApplianceRequest $request)
    {
        return $this->insertProduct($request, HomeAppliance::class, ['type_of_appliance', 'brand', 'model', 'capacity', 'features', 'condition', 'warranty_information'], 2);
    }
    public function furnitures(FurnitureRequest $request)
    {
        return $this->insertProduct($request, furniture::class, ['type_of_furniture', 'material', 'dimensions', 'color', 'style', 'condition', 'assembly_required'], 3);
    }
    public function clothings(ClothingRequest $request)
    {
        return $this->insertProduct($request, clothing::class, ['type_of_clothing_accessory', 'size', 'color', 'brand', 'material', 'condition', 'care_instructions'], 4);
    }
    public function sports(SportsRequest $request)
    {
        return $this->insertProduct($request, sport::class, ['type_of_equipment', 'brand', 'condition', 'size_weight', 'features', 'suitable_sport_activity', 'warranty_information', 'usage_instructions'], 5);
    }
    public function books(BooksRequest $request)
    {
        return $this->insertProduct($request, book::class, ['title', 'author_artist', 'genre', 'format', 'condition', 'edition', 'isbn_upc', 'warranty_information', 'description'], 6);
    }
    public function antiques(AntiquesRequest $request)
    {
        return $this->insertProduct($request, antique::class, ['type_of_item', 'era_period', 'material', 'condition', 'provenance_location', 'rarity', 'historical_significance', 'certification'], 7);
    }
    public function cars(CarsRequest $request)
    {
        return $this->insertProduct($request, car::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'fuel_type', 'owner', 'transmission_type'], 8);
    }
    public function motorcycles(MotorRequest $request)
    {
        return $this->insertProduct($request, motorcycle::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'], 9);
    }
    public function scooters(ScooterRequest $request)
    {
        return $this->insertProduct($request, scooter::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'], 10);
    }
    public function bicycles(BicycleRequest $request)
    {

        return $this->insertProduct($request, bicycle::class, ['brand'], 11);
    }
    public function toys(ToysRequest $request)
    {
        return $this->insertProduct(
            $request,
            toy::class,
            ['type_of_toy_game', 'age_group', 'brand', 'condition', 'description', 'safety_information', 'assembly_required', 'recommended_use'],
            12
        );
    }
    public function musics(MusicRequest $request)
    {
        return $this->insertProduct(
            $request,
            music::class,
            ['type_of_instrument', 'brand', 'condition', 'material', 'accessories_included', 'sound_characteristics'],
            13
        );
    }
    public function others(MusicRequest $request)
    {
        return $this->insertProduct(
            $request,
            music::class,
            ['type_of_instrument', 'brand', 'condition', 'material', 'accessories_included', 'sound_characteristics'],
            13
        );
    }
    public function makeCategory(Request $request)
    {
        $category_data['category_name'] = $request->category_name;
        $cat_data = $request->fields;
        for ($i = 0; $i < count($cat_data); $i++) {
            $cat_data[$i]['name'] = strtolower(str_replace(' ', '_', $cat_data[$i]['label']));
            $cat_data[$i]['label'] = ucfirst($cat_data[$i]['label']);
        }
        $category_data['fields'] = json_encode($cat_data, true);
        $category_data['function_name'] = $request->category_name;
        $category_data['admin_status'] = 1;
        category::create($category_data);
    }
    public function getIndivCategory($id)
    {
        $category  = category::findorFail($id);
        $category['fields'] = json_decode($category['fields'], true);
        return response()->json($category);
    }
    public function insertProducts(Request $request)
    {
        DB::beginTransaction();
        try {
            $validationRules = [];
            $products_feature = [];
            $category = category::findorFail($request->category_id);
            if (!$category) {
                return response()->json("Category not found");
            }
            $category_fields = json_decode($category->fields, true);
            for ($i = 0; $i < count($category_fields); $i++) {
                $products_feature[$category_fields[$i]['name']] = $request[$category_fields[$i]['name']];
            }
            $productData = $request->all();
            $productData['extra_features'] = json_encode($products_feature, true);
            foreach ($category_fields as $fieldName) {
                if ($fieldName['type'] === "text") {
                    $validationRules[$fieldName['name']] = 'required|string|max:255';
                } else if ($fieldName['type'] === "number") {
                    $validationRules[$fieldName['name']] = 'required|integer|min:0';
                }
            }
            $validationRules += [
                'user_id' => 'required|exists:users,id',
                'pname' => 'required|string|max:255',
                'description' => 'required|string',
                'Province' => 'required|string',
                'District' => 'required|string',
                'Municipality' => 'required|string',
                'price' => 'required|integer|max:100000000',
                'image_urls.*' => 'image|mimes:jpeg,png,jpg,webp',
            ];
            $request->validate($validationRules);
            $product =  Product::create($productData);

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
        } catch (ValidationException $e) {
            // Validation failed, return the validation errors
            DB::rollback();

            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
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
            $model::create($specificData);
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
