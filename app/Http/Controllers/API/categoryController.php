<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Category;
use App\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Inline\Element\Strong;

class categoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->toUser();
        $categories = User::find($user->id)->getCategories;
        return response()->json([
            'categories' => $categories
        ], 200);
    }

    public function store(Request $request)
    {

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $user_id = JWTAuth::parseToken()->toUser()->id;
        $this->validate($request, [
            'category' => 'required',
        ]);
        try {
            $categori = trim($request['category']);
            $checkcat = DB::table('categories')->where(['user_id' => $user_id, 'name' => $categori])->get();
            $count = count($checkcat);
            if ($count < 1){
                $category = new Category();
                $category->user_id = $user_id;
                $category->name = $categori;
                $category->save();
                $categories = DB::table('categories')->where(['user_id' => $user_id, 'name' => $categori])->first();
                return response()->json([
                    'title' => 'Successful!',
                    'categories' => $categories,
                    'status' => 1,
                    'message' => $categori.' category is created.'
                ], 200);

            }else{
                return response()->json([
                    'title' => 'Error!',
                    'status' => 2,
                    'message' => 'This category already exists.'
                ], 200);
            }

        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'message' => 'Could not create category, please check your connection.'
            ], 500);
        }

    }

    public function update(Request $request, $id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        $this->validate($request, [
            'category' => 'required',
        ]);
        $user_id = JWTAuth::parseToken()->toUser()->id;

        $categori = trim($request['category']);
        $checkcat = DB::table('categories')->where(['user_id' => $user_id, 'name' => $categori])->get();
        $count = count($checkcat);
        try {
            if ($count < 1){
                $category = Category::find($id);
                $category->name = $categori;
                $category->update();
                return response()->json([
                    'title' => 'Successful!',
                    'status' => 1,
                    'message' => 'Category is updated to: '.$categori,
                    'category' => $categori
                ], 200);
            }else{
                return response()->json([
                    'title' => 'Error!',
                    'status' => 2,
                    'message' => 'This category already exists.'
                ], 200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'title' => 'Error!',
                'message' => 'Could not update your category, please check your connection.'
            ], 500);
        }

    }

    public function destroy($id)
    {
        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try {
            $category = Category::findOrFail($id);
            $category->delete();
        } catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json(['status' => 'Category deleted successfully.'], 200);
    }
    public function bulkdelete(Request $request) {

        if (! $user = JWTAuth::parseToken()->authenticate()) {
            return response()->json(['status' => 'User not found!'], 404);
        }
        try{
            foreach($request[0] as $id) {

                $category = Category::findOrFail($id);
                $category->delete();

            }
        }catch (\Throwable $th) {
            return response()->json(['status' => 'An error has occured!'], 500);
        }
        return response()->json(['status' => 'Products deleted successfully.'], 200);

    }
}
