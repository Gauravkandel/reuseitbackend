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
    public function homeAppliances(HomeApplianceRequest $request)
    {
        return $this->insertProduct($request, HomeAppliance::class, ['type_of_appliance', 'brand', 'model', 'capacity', 'features', 'condition', 'warranty_information'], 2);
    }
    public function furnitures(FurnitureRequest $request)
    {
        return $this->insertProduct($request, furniture::class, ['type_of_furniture', 'material', 'dimensions', 'color', 'style', 'condition', 'assembly_required'], 3);
    }
    public function clothing(ClothingRequest $request)
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
    public function motorcycle(MotorRequest $request)
    {
        return $this->insertProduct($request, motorcycle::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'], 9);
    }
    public function scooter(ScooterRequest $request)
    {
        return $this->insertProduct($request, scooter::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'], 10);
    }
    public function bicycle(BicycleRequest $request)
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
    public function music(MusicRequest $request)
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
    public function insertProductByCategory(Request $request)
    {
        return $this->insertProducts($request);
    }
    private function insertProducts($request)
    {
        $productData = $request->all();
        DB::beginTransaction();
        try {

            $category = category::where('id', $request->category_id)->first();
            $onlyarray = [];
            $data = json_decode($category->fields, true);
            $validationRules = [];

            foreach ($data as $fieldName) {
                if ($fieldName != 'product_id') {
                    $validationRules[$fieldName] = 'required|string|max:255';
                }
                if ($fieldName === "dimensions") {
                    $validationRules[$fieldName] = ['required', 'string', new ValidDimensions];
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
            ];
            $request->validate($validationRules);

            for ($i = 0; $i < count($data); $i++) {
                $onlyarray[$i] = $data[$i];
            }
            // Insert into products table
            $productData['category_id'] = $request->category_id; // Assuming $categoryType is the category ID
            $product = Product::create($productData);

            // Get category-specific information

            if (!$category) {
                throw new \Exception('Invalid category type');
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
        } catch (ValidationException $e) {
            // Validation failed, return the validation errors
            DB::rollback();
            $errors = collect($e->validator->errors()->all())->flatten();

            return response()->json(['errors' => $errors], 422);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => 'Failed to insert data. ' . $e->getMessage()], 500);
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
