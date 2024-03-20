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
use App\Models\category;
use App\Models\Product_image;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DashboardController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
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
        if (!$product_sp) {
            $product_sp = $productdata->price;
        }
        $productdata->status = 1;
        $productdata->selling_price =  $product_sp;
        $productdata->sold_at = Carbon::now();
        $productdata->save();
        return response()->json(['message' => 'successfull'], 200);
    }

    public function electronics(ElectronicsRequest $request)
    {
        // return $this->UpdateProduct($request, electronic::class, ['type_of_electronic', 'brand', 'model', 'condition', 'warranty_information'], 1);
        return $this->UpdateData($request, 1);
    }
    public function homeappliances(HomeApplianceRequest $request)
    {
        // return $this->UpdateProduct($request, HomeAppliance::class, ['type_of_appliance', 'brand', 'model', 'capacity', 'features', 'condition', 'warranty_information'], 2);
        return $this->UpdateData($request, 2);
    }
    public function furnitures(FurnitureRequest $request)
    {
        // return $this->UpdateProduct($request, furniture::class, ['type_of_furniture', 'material', 'dimensions', 'color', 'style', 'condition', 'assembly_required'], 3);
        return $this->UpdateData($request, 3);
    }
    public function clothings(ClothingRequest $request)
    {
        // return $this->UpdateProduct($request, clothing::class, ['type_of_clothing_accessory', 'size', 'color', 'brand', 'material', 'condition', 'care_instructions'], 4);
        return $this->UpdateData($request, 4);
    }
    public function sports(SportsRequest $request)
    {
        // return $this->UpdateProduct($request, sport::class, ['type_of_equipment', 'brand', 'condition', 'size_weight', 'features', 'suitable_sport_activity', 'warranty_information', 'usage_instructions'], 5);

        return $this->UpdateData($request, 5);
    }
    public function books(BooksRequest $request)
    {
        // return $this->UpdateProduct($request, book::class, ['title', 'author_artist', 'genre', 'format', 'condition', 'edition', 'isbn_upc', 'warranty_information', 'description'], 6);
        return $this->UpdateData($request, 6);
    }
    public function antiques(AntiquesRequest $request)
    {
        // return $this->UpdateProduct($request, antique::class, ['type_of_item', 'era_period', 'material', 'condition', 'provenance_location', 'rarity', 'historical_significance', 'certification'], 7);
        return $this->UpdateData($request, 7);
    }
    public function cars(CarsRequest $request)
    {
        // return $this->UpdateProduct($request, car::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'fuel_type', 'owner', 'transmission_type'], 8);
        return $this->UpdateData($request, 8);
    }
    public function motorcycles(MotorRequest $request)
    {
        // return $this->UpdateProduct($request, motorcycle::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'], 9);
        return $this->UpdateData($request, 9);
    }
    public function scooters(ScooterRequest $request)
    {
        // return $this->UpdateProduct($request, scooter::class, ['brand', 'model', 'year', 'mileage', 'condition', 'km_driven', 'color', 'used_time', 'owner'], 10);
        return $this->UpdateData($request, 10);
    }
    public function bicycles(BicycleRequest $request)
    {

        // return $this->UpdateProduct($request, bicycle::class, ['brand'], 11);
        return $this->UpdateData($request, 11);
    }
    public function toys(ToysRequest $request)
    {
        // return $this->UpdateProduct(
        //     $request,
        //     toy::class,
        //     ['type_of_toy_game', 'age_group', 'brand', 'condition', 'description', 'safety_information', 'assembly_required', 'recommended_use'],
        //     12
        // );
        return $this->UpdateData($request, 12);
    }
    public function musics(MusicRequest $request)
    {
        // return $this->UpdateProduct(
        //     $request,
        //     music::class,
        //     ['type_of_instrument', 'brand', 'condition', 'material', 'accessories_included', 'sound_characteristics'],
        //     13
        // );
        return $this->UpdateData($request, 13);
    }
    public function others(MusicRequest $request)
    {
        // return $this->UpdateProduct(
        //     $request,
        //     music::class,
        //     ['type_of_instrument', 'brand', 'condition', 'material', 'accessories_included', 'sound_characteristics'],
        //     13
        // );
        return $this->UpdateData($request, 14);
    }

    private function UpdateData($request, $category_id)
    {
        $productData = $request->validated();
        DB::beginTransaction();
        try {
            $products_feature = [];
            $product = Product::findOrFail($request->id);
            //$request->id is the ID of the product to be updated
            $productData['category_id'] = $category_id;

            $category = category::findorFail($category_id);
            $category_fields = json_decode($category->fields, true);
            for ($i = 0; $i < count($category_fields); $i++) {
                $products_feature[$category_fields[$i]] = $request[$category_fields[$i]];
            }
            $productData['extra_features'] = json_encode($products_feature, true);

            $product->update($productData);
            $pre_images = $request->old_image;
            if ($pre_images != null) {
                foreach ($pre_images as $pre_image) {
                    Product_image::find($pre_image)->delete();
                }
            }
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
            }
            DB::commit();
            return response()->json(['success' => 'Successful Update'], 200);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function UpdateProducts(Request $request)
    {
        DB::beginTransaction();
        try {
            $validationRules = [];
            $products_feature = [];
            $product = Product::findOrFail($request->id);
            //$request->id is the ID of the product to be updated
            $productData = $request->all();
            $productData['category_id'] = $request->category_id;
            $category = category::findorFail($request->category_id);
            $category_fields = json_decode($category->fields, true);
            for ($i = 0; $i < count($category_fields); $i++) {
                $products_feature[$category_fields[$i]['name']] = $request[$category_fields[$i]['name']];
            }
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
            $product->update($productData);
            $pre_images = $request->old_image;
            if ($pre_images != null) {
                foreach ($pre_images as $pre_image) {
                    Product_image::find($pre_image)->delete();
                }
            }
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
            }
            DB::commit();
            return response()->json(['success' => 'Successful Update'], 200);
        } catch (ValidationException $e) {
            // Validation failed, return the validation errors
            DB::rollback();
            return response()->json(['errors' => $e->validator->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }
    public function viewUserDetails(Request $request)
    {
        $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|max:255',
            'Province' => 'string|max:255',
            'District' => 'string|max:255',
            'Municipality' => 'string|max:255',
            'Profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp',
        ]);
        $id = auth()->user()->id;
        $user = User::findOrFail($id);

        // Update user details
        $user->name = $request->name;
        $user->email = $request->email;
        $user->Province = $request->Province;
        $user->District = $request->District;
        $user->Municipality = $request->Municipality;
        if ($request->has('Profile_image')) {
            $profile = $request->file('Profile_image');
            $profile_name = time() . $profile->getClientOriginalName();
            $profile->move(public_path('images'), $profile_name);
            $user->Profile_image = $profile_name;
        }
        $user->save();
        return response()->json(['message' => 'User details updated successfully']);
    }
}
